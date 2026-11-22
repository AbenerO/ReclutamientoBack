<?php

use App\Http\Controllers\Api\ReportePostulacionApiController;
use Illuminate\Support\Facades\Route;

Route::get('reportes/postulaciones-por-estado', [ReportePostulacionApiController::class, 'porEstado']);
