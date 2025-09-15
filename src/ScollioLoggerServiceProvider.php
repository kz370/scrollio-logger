<?php

namespace Kz370\ScollioLogger;

use Illuminate\Support\ServiceProvider;

class ScollioLoggerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('scollio-logger', function () {
            return new Logger\ScollioLogger();
        });
    }

    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/scollio-logger.php', 'scollio-logger');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'scollio-logger');

        // Publish config
        $this->publishes([
            __DIR__.'/../config/scollio-logger.php' => config_path('scollio-logger.php'),
        ], 'scollio-logger-config');

        // Publish migrations
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'scollio-logger-migrations');

        // Publish views
        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/scollio-logger'),
        ], 'scollio-logger-views');
    }
}
