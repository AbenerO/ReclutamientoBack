<?php

use App\Http\Controllers\Api\DevController;
use Illuminate\Support\Facades\Route;

Route::prefix('dev')->group(function () {
    Route::get('logs', [DevController::class, 'logs']);
    Route::delete('logs', [DevController::class, 'limpiarLogs']);
    Route::get('colas', [DevController::class, 'colas']);
    Route::delete('colas/fallidos', [DevController::class, 'limpiarColas']);
    Route::get('info', [DevController::class, 'info']);
    Route::get('redis', [DevController::class, 'redis']);
    Route::delete('redis/{clave}', [DevController::class, 'redisBorrarClave'])->where('clave', '.*');
});