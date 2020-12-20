<?php

use Illuminate\Support\Facades\Route;

use Redbeed\OpenOverlay\Http\Controllers\Api\Connection\WebhookController;
use Redbeed\OpenOverlay\Http\Controllers\Connection\AppTokenController;
use Redbeed\OpenOverlay\Http\Controllers\Connection\AuthController;
use Redbeed\OpenOverlay\Http\Controllers\Connection\BotAuthController;

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

            // prefix: /connection/bot
            Route::prefix('bot')->group(function () {
                Route::get('/redirect')->uses([BotAuthController::class, 'redirect'])
                    ->name('connection.bot.redirect');

                Route::get('/callback')->uses([BotAuthController::class, 'handleProviderCallback'])
                    ->name('connection.bot.callback');

            });
        });

        Route::middleware(['api'])->group(function () {
            Route::any('/webhook')->uses([WebhookController::class, 'handleProviderCallback'])
                ->name('connection.webhook');
        });

    });
});
