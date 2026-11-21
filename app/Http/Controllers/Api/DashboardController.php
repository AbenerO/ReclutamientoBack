<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\Notificacion;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DashboardController extends AppBaseController
{
    public function estadisticas(): JsonResponse
    {
        $hoy       = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();

        // ── KPIs ─────────────────────────────────────────────────────────────
        $kpis = [
            'total_usuarios'        => User::count(),
            'usuarios_activos'      => User::where('estado', true)->count(),
            'total_roles'           => Role::count(),
            'notificaciones_hoy'    => Notificacion::whereDate('created_at', $hoy)->count(),
            'notificaciones_mes'    => Notificacion::where('created_at', '>=', $inicioMes)->count(),
            'notificaciones_fallidas_mes' => Notificacion::where('created_at', '>=', $inicioMes)
                                                ->where('estado', 'fallido')->count(),
        ];

        // ── Usuarios por rol ─────────────────────────────────────────────────
        $usuariosPorRol = Role::withCount('user')
            ->get()
            ->map(fn ($rol) => ['nombre' => $rol->name, 'total' => (int) $rol->user_count]);

        // ── Notificaciones por canal (mes actual) ────────────────────────────
        $notificacionesPorCanal = Notificacion::selectRaw('canal, count(*) as total')
            ->where('created_at', '>=', $inicioMes)
            ->groupBy('canal')
            ->get()
            ->map(fn ($item) => ['canal' => $item->canal, 'total' => (int) $item->total]);

        // ── Últimos usuarios creados ─────────────────────────────────────────
        $ultimosUsuarios = User::with('roles:id,name')
            ->latest('id')
            ->limit(8)
            ->get(['id', 'name', 'username', 'email', 'estado', 'created_at'])
            ->map(fn ($u) => [
                'id'         => $u->id,
                'nombre'     => $u->name,
                'username'   => $u->username,
                'email'      => $u->email,
                'estado'     => (bool) $u->estado,
                'roles'      => $u->roles->pluck('name'),
                'creado_en'  => $u->created_at?->format('d/m/Y H:i'),
            ]);

        return $this->sendResponse([
            'kpis'                     => $kpis,
            'usuarios_por_rol'         => $usuariosPorRol,
            'notificaciones_por_canal' => $notificacionesPorCanal,
            'ultimos_usuarios'         => $ultimosUsuarios,
        ], 'Estadísticas recuperadas con éxito.');
    }
}
