<?php

use App\Http\Controllers\Api\DepartamentoApiController;
use App\Http\Controllers\Api\MunicipioApiController;
use App\Http\Controllers\Api\NotificacionApiController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'configuracion'], function () {
    // ── Ubicaciones (catálogo Guatemala) ──────────────────────────────────────
    Route::apiResource('departamento', DepartamentoApiController::class)->only(['index']);
    Route::apiResource('municipio', MunicipioApiController::class)->only(['index']);

    // ── Notificaciones (bitácora + envío de prueba) ───────────────────────────
    Route::get('notificacion/getColumnas', [NotificacionApiController::class, 'getColumnas']);
    Route::post('notificacion/prueba', [NotificacionApiController::class, 'enviarPrueba']);
    Route::apiResource('notificacion', NotificacionApiController::class)->only(['index', 'show']);
});
