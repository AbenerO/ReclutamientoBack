<?php

use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MenuOpcionApiController;
use App\Http\Controllers\Api\PerfilController;
use App\Http\Controllers\WhatsAppWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('dashboard')->group(function () {
        Route::get('estadisticas', [DashboardController::class, 'estadisticas']);
    });

    Route::put('perfil', [PerfilController::class, 'update']);
    Route::post('perfil/avatar', [PerfilController::class, 'subirAvatar']);
    Route::delete('perfil/avatar', [PerfilController::class, 'eliminarAvatar']);
    Route::put('perfil/cambiar-password', [PerfilController::class, 'cambiarPassword']);

    Route::get('/user', function (Request $request) {
        $user = $request->user();

        if ($user->hasRole('SuperAdministrador')) {
            $abilities = [['action' => 'manage', 'subject' => 'all']];
        } else {
            $abilities = $user->getAllPermissions()->map(fn($p) => [
                'action'  => $p->name,
                'subject' => $p->subject,
            ])->values()->all();
        }

        return array_merge($user->toArray(), ['abilities' => $abilities]);
    });

    require __DIR__ . '/rutas_users.php';
    require __DIR__ . '/configuracion.php';
    require __DIR__ . '/dev.php';
    require __DIR__ . '/log_viewer.php';

    require __DIR__ . '/reclutamiento/catalogos.php';
    require __DIR__ . '/reclutamiento/vacantes.php';
    require __DIR__ . '/reclutamiento/candidatos.php';
    require __DIR__ . '/reclutamiento/postulaciones.php';
    require __DIR__ . '/reclutamiento/entrevistas.php';
});

Route::post('menu-opcions/actualizar/orden', [MenuOpcionApiController::class, 'actualizarOrden'])->name('menu-opcions.getColumnas');

Route::apiResource('menu-opcions', MenuOpcionApiController::class);

require __DIR__ . '/auth.php';
