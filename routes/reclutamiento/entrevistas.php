<?php

use App\Http\Controllers\Api\EntrevistaApiController;
use Illuminate\Support\Facades\Route;

Route::get('entrevistas/getColumnas', [EntrevistaApiController::class, 'getColumnas'])->name('entrevistas.getColumnas');
Route::apiResource('entrevistas', EntrevistaApiController::class)
    ->parameters(['entrevistas' => 'entrevista'])
    ->except('destroy');
