<?php

use App\Http\Controllers\Api\CandidatoApiController;
use Illuminate\Support\Facades\Route;

Route::get('candidatos/getColumnas', [CandidatoApiController::class, 'getColumnas'])->name('candidatos.getColumnas');
Route::apiResource('candidatos', CandidatoApiController::class)->parameters(['candidatos' => 'candidato']);
