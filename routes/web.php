<?php

use Illuminate\Support\Facades\Route;
use Kz370\ScollioLogger\Http\Controllers\ScollioLogController;

if (config('scollio-logger.enabled') && config('scollio-logger.dashboard.enabled')) {
    Route::prefix(config('scollio-logger.dashboard.route', 'scollio-logs/dashboard'))
        ->middleware(config('scollio-logger.dashboard.middleware', ['web']))
        ->group(function () {
            Route::get('/', [ScollioLogController::class, 'index'])->name('scollio-logs.index');
            Route::get('/{id}', [ScollioLogController::class, 'show'])->name('scollio-logs.show');
            Route::delete('/{id}', [ScollioLogController::class, 'destroy'])->name('scollio-logs.delete');
            Route::post('/clear', [ScollioLogController::class, 'clear'])->name('scollio-logs.clear');
            Route::get('/logs/poll', [ScollioLogController::class, 'poll'])->name('scollio-logs.poll');
        });
}
