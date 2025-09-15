<?php

use Illuminate\Support\Facades\Route;
use Kz370\ScollioLogger\Http\Controllers\LogDashboardController;

if (config('scollio-logger.dashboard.enabled', true)) {
    Route::prefix(config('scollio-logger.dashboard.route', 'scollio-logs/dashboard'))
        ->middleware(config('scollio-logger.dashboard.middleware', ['web']))
        ->group(function () {
            Route::get('/', [LogDashboardController::class, 'index'])->name('scollio-logs.index');
            Route::get('/show/{id}', [LogDashboardController::class, 'show'])->name('scollio-logs.show');
            Route::delete('/delete/{id}', [LogDashboardController::class, 'delete'])->name('scollio-logs.delete');
            Route::post('/clear', [LogDashboardController::class, 'clear'])->name('scollio-logs.clear');
        });
}
