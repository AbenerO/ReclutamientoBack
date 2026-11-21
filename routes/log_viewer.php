<?php

use App\Http\Controllers\Api\LogViewerController;
use Illuminate\Support\Facades\Route;

Route::prefix('configuracion/log-viewer')->name('log-viewer.')->group(function () {
    Route::get('menu',                   [LogViewerController::class, 'menu'])->name('menu');
    Route::get('logs/{date}/download',   [LogViewerController::class, 'download'])->name('logs.download');
    Route::get('logs/{date}',            [LogViewerController::class, 'show'])->name('logs.show');
    Route::delete('logs/delete',         [LogViewerController::class, 'delete'])->name('logs.delete');
    Route::post('logs/frontend',         [LogViewerController::class, 'frontendLogs'])->name('logs.frontend');
});
