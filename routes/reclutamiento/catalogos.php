<?php

use App\Http\Controllers\Api\EstadoPostulacionApiController;
use App\Http\Controllers\Api\EstadoVacanteApiController;
use App\Http\Controllers\Api\FuenteReclutamientoApiController;
use App\Http\Controllers\Api\ModalidadApiController;
use App\Http\Controllers\Api\ResultadoEntrevistaApiController;
use App\Http\Controllers\Api\TipoEntrevistaApiController;
use App\Http\Controllers\Api\UnidadApiController;
use Illuminate\Support\Facades\Route;

// Catálogos del módulo de reclutamiento

Route::get('unidades/getColumnas', [UnidadApiController::class, 'getColumnas'])->name('unidades.getColumnas');
Route::apiResource('unidades', UnidadApiController::class)->parameters(['unidades' => 'unidad']);

Route::get('modalidades/getColumnas', [ModalidadApiController::class, 'getColumnas'])->name('modalidades.getColumnas');
Route::apiResource('modalidades', ModalidadApiController::class)->parameters(['modalidades' => 'modalidad']);

Route::get('estados_vacante/getColumnas', [EstadoVacanteApiController::class, 'getColumnas'])->name('estados_vacante.getColumnas');
Route::apiResource('estados_vacante', EstadoVacanteApiController::class)->parameters(['estados_vacante' => 'estadoVacante']);

Route::get('estados_postulacion/getColumnas', [EstadoPostulacionApiController::class, 'getColumnas'])->name('estados_postulacion.getColumnas');
Route::apiResource('estados_postulacion', EstadoPostulacionApiController::class)->parameters(['estados_postulacion' => 'estadoPostulacion']);

Route::get('fuentes_reclutamiento/getColumnas', [FuenteReclutamientoApiController::class, 'getColumnas'])->name('fuentes_reclutamiento.getColumnas');
Route::apiResource('fuentes_reclutamiento', FuenteReclutamientoApiController::class)->parameters(['fuentes_reclutamiento' => 'fuenteReclutamiento']);

Route::get('tipos_entrevista/getColumnas', [TipoEntrevistaApiController::class, 'getColumnas'])->name('tipos_entrevista.getColumnas');
Route::apiResource('tipos_entrevista', TipoEntrevistaApiController::class)->parameters(['tipos_entrevista' => 'tipoEntrevista']);

Route::get('resultados_entrevista/getColumnas', [ResultadoEntrevistaApiController::class, 'getColumnas'])->name('resultados_entrevista.getColumnas');
Route::apiResource('resultados_entrevista', ResultadoEntrevistaApiController::class)->parameters(['resultados_entrevista' => 'resultadoEntrevista']);
