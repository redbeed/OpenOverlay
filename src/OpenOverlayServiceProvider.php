<?php

namespace Redbeed\OpenOverlay;

use Illuminate\Support\ServiceProvider;
use Redbeed\OpenOverlay\Console\ConsoleServiceProvider;

class OpenOverlayServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'redbeed');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'redbeed');

         $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
         $this->loadRoutesFrom(__DIR__.'/../routes/openoverlay.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(ConsoleServiceProvider::class);

        $this->mergeConfigFrom(__DIR__.'/../config/openoverlay.php', 'openoverlay');

        // Register the service the package provides.
        $this->app->singleton('openoverlay', function ($app) {
            return new OpenOverlay;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['openoverlay'];
    }


    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/openoverlay.php' => config_path('openoverlay.php'),
        ], 'openoverlay.config');
    }
}
