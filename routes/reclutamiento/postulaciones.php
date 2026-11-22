<?php

use App\Http\Controllers\Api\PostulacionApiController;
use App\Http\Controllers\Api\PostulacionHistorialApiController;
use Illuminate\Support\Facades\Route;

Route::get('postulaciones/getColumnas', [PostulacionApiController::class, 'getColumnas'])->name('postulaciones.getColumnas');
Route::put('postulaciones/{postulacion}/estado', [PostulacionApiController::class, 'cambiarEstado'])->name('postulaciones.cambiarEstado');

Route::apiResource('postulaciones', PostulacionApiController::class)->except(['destroy'])->parameters(['postulaciones' => 'postulacion']);

Route::get('postulacion_historial/getColumnas', [PostulacionHistorialApiController::class, 'getColumnas'])->name('postulacion_historial.getColumnas');
Route::apiResource('postulacion_historial', PostulacionHistorialApiController::class)->parameters(['postulacion_historial' => 'postulacionHistorial']);
