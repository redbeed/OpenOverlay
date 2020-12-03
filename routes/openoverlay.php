<?php

use Illuminate\Support\Facades\Route;

use Redbeed\OpenOverlay\Http\Controllers\Api\Connection\WebhookController;
use Redbeed\OpenOverlay\Http\Controllers\Connection\AppTokenController;
use Redbeed\OpenOverlay\Http\Controllers\Connection\AuthController;

Route::name('open_overlay.')->group(function () {

    // prefix: /connection
    Route::prefix('connection')->group(function () {

        Route::middleware(['web', 'auth'])->group(function () {

            Route::get('/redirect')->uses([AuthController::class, 'redirect'])
                ->name('connection.redirect');

            Route::get('/callback')->uses([AuthController::class, 'handleProviderCallback'])
                ->name('connection.callback');

            // prefix: /connection/app-token
            Route::prefix('app-token')->group(function () {
                Route::get('/redirect')->uses([AppTokenController::class, 'redirect'])
                    ->name('connection.app-token.redirect');

                Route::get('/callback')->uses([AppTokenController::class, 'handleProviderCallback'])
                    ->name('connection.app-token.callback');

            });
        });

        Route::middleware(['api'])->group(function () {
            Route::any('/webhook')->uses([WebhookController::class, 'handleProviderCallback'])
                ->name('connection.webhook');
        });

    });
});
