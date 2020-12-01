<?php

use Illuminate\Support\Facades\Route;

use Redbeed\OpenOverlay\Http\Controllers\Api\Connection\WebhookController;
use Redbeed\OpenOverlay\Http\Controllers\Connection\AuthController;

Route::middleware(['auth'])->group(function () {

    Route::prefix('connection')->group(function () {
        Route::get('/redirect')
            ->name('redirect')
            ->uses([AuthController::class, 'redirect']);

        Route::get('/callback')
            ->name('callback')
            ->uses([AuthController::class, 'handleProviderCallback']);

        Route::get('/webhook')
            ->name('webhook')
            ->uses([WebhookController::class, 'handleProviderCallback']);
    });

});
