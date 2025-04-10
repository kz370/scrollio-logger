<?php

namespace Kz370\ScollioLogger;

use Exception;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Schema;
use Kz370\ScollioLogger\Logger\Logger;
use Illuminate\Support\ServiceProvider;
use Kz370\ScollioLogger\Console\RotateScollioLogs;
use Kz370\ScollioLogger\Http\Middleware\TrafficLogger;

class ScollioLoggerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Merge package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/scollio-logger.php', 'scollio-logger');

        // Register main Logger singleton if enabled
        if (config('scollio-logger.enabled', true)) {
            $this->app->singleton('scollio-logger', fn() => new Logger());
        }
    }

    public function boot(Application $app): void
    {

        $tableExists = false;
        try {
            $tableExists = Schema::hasTable('scollio_logger');
        } catch (Exception $e) {
            // Silently fail if DB not ready (e.g. during install)
        }

        // Register middleware alias
        if ($tableExists) {
            $this->registerMiddlewareAlias();
            $this->registerGlobalMiddleware($app);
        }

        // Load routes and views
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'scollio');

        // Publish config, views, migrations
        $this->publishes([
            __DIR__ . '/../config/scollio-logger.php' => config_path('scollio-logger.php'),
        ], 'scollio-logger-config');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/scollio'),
        ], 'scollio-logger-views');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'scollio-logger-migrations');

        // Register artisan commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                RotateScollioLogs::class,
            ]);
        }
    }

    protected function registerMiddlewareAlias(): void
    {
        Route::aliasMiddleware('scollio-logger', TrafficLogger::class);
    }

    protected function registerGlobalMiddleware(Application $app): void
    {
        $enabled = (bool) config('scollio-logger.middleware.global', true);

        if ($enabled) {
            $kernel = $this->app->make(Kernel::class);
            $kernel->pushMiddleware(TrafficLogger::class);
        }
    }
}
