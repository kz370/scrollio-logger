<?php

namespace Scollio;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use Scollio\Logger\Logger;
use Scollio\Http\Middleware\GlobalRequestLogger;
use Scollio\Http\Middleware\ExceptionLogger;

class LoggerServiceProvider extends ServiceProvider
{
    public function register()
    {
        if (config('scollio-logger.enabled', true)) {
            $this->app->singleton('scollio-logger', function () {
                return new Logger();
            });
        }
    }

    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/scollio-logger.php', 'scollio-logger');

        // Register middleware aliases
        $this->registerMiddlewareAlias();

        // Register middleware globally if enabled
        $this->registerRequestLoggingMiddleware();
        $this->registerExceptionLoggingMiddleware();

        // Load routes, views, and publish assets
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../src/resources/views', 'scollio-logger');
        
        $this->publishes([
            __DIR__ . '/../config/scollio-logger.php' => config_path('scollio-logger.php'),
        ], 'scollio-logger-config');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'scollio-logger-migrations');
    }

    protected function registerMiddlewareAlias(): void
    {
        $router = $this->app->make('router');
        $router->aliasMiddleware('scollio-request-logger', GlobalRequestLogger::class);
        $router->aliasMiddleware('scollio-exception-logger', ExceptionLogger::class);
    }

    protected function registerRequestLoggingMiddleware(): void
    {
        $config = $this->app['config']->get('scollio-logger.middleware.request_logging', []);

        if (!empty($config['enabled'])) {
            $kernel = $this->app->make(Kernel::class);
            $kernel->pushMiddleware(GlobalRequestLogger::class);
        }
    }

    protected function registerExceptionLoggingMiddleware(): void
    {
        $config = $this->app['config']->get('scollio-logger.middleware.exception_logging', []);

        if (!empty($config['enabled'])) {
            $kernel = $this->app->make(Kernel::class);
            $kernel->pushMiddleware(ExceptionLogger::class);
        }
    }
}
