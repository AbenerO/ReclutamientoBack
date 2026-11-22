<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\EstadoPostulacion;
use App\Models\Postulacion;
use App\Models\Vacante;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class ReportePostulacionApiController extends AppBaseController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver reporte postulaciones'),
        ];
    }

    public function porEstado(): JsonResponse
    {
        $estados = EstadoPostulacion::orderBy('orden')
            ->get(['id', 'nombre']);
        $vacantes = Vacante::orderBy('titulo')
            ->get(['id', 'titulo']);

        $conteos = Postulacion::select('vacante_id', 'estado_postulacion_id', DB::raw('count(*) as cantidad'))
            ->groupBy('vacante_id', 'estado_postulacion_id')
            ->get();

        $filas = [];

        foreach ($vacantes as $vacante) {
            $porEstado = [];
            $total = 0;

            foreach ($estados as $estado) {
                $cantidad = $conteos
                    ->where('vacante_id', $vacante->id)
                    ->where('estado_postulacion_id', $estado->id)
                    ->sum('cantidad');

                $porEstado[$estado->id] = $cantidad;
                $total += $cantidad;
            }

            $filas[] = [
                'vacante_id' => $vacante->id,
                'vacante' => $vacante->titulo,
                'estados' => $porEstado,
                'total' => $total,
            ];
        }

        return $this->sendResponse([
            'estados' => $estados,
            'filas' => $filas,
        ], 'Reporte de postulaciones por estado recuperado con éxito.');
    }
}
