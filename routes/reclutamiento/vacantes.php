<?php

use App\Http\Controllers\Api\VacanteApiController;
use Illuminate\Support\Facades\Route;

Route::get('vacantes/getColumnas', [VacanteApiController::class, 'getColumnas'])->name('vacantes.getColumnas');
Route::apiResource('vacantes', VacanteApiController::class)->parameters(['vacantes' => 'vacante']);
