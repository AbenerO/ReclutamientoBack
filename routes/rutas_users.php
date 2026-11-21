<?php

use App\Http\Controllers\Api\PermissionApiController;
use App\Http\Controllers\Api\RoleApiController;
use App\Http\Controllers\Api\UsuarioApiController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'permissions', 'as' => 'permissions.'], function () {
    Route::get('/{permission}/roles', [PermissionApiController::class, 'roles']);
});
Route::apiResource('permissions', PermissionApiController::class);

Route::group(['prefix' => 'roles', 'as' => 'roles.'], function () {
    Route::get('/{role}/permissions', [RoleApiController::class, 'permisos']);
});
Route::apiResource('roles', RoleApiController::class);

Route::get('usuarios/columnas', [UsuarioApiController::class, 'getColumnas']);
Route::post('usuarios/{usuario}/reset-password', [UsuarioApiController::class, 'resetPassword']);
Route::apiResource('usuarios', UsuarioApiController::class);
