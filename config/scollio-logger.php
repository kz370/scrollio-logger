<?php

return [
    'enabled' => env('SCOLLIO_LOGGER_ENABLED', true),
    'title' => config('app.name', 'Scollio') . ' Logs',
    
    'dashboard' => [
        'enabled' => env('SCOLLIO_LOGGER_DASHBOARD_ENABLED', true),
        'route' => env('SCOLLIO_LOGGER_DASHBOARD_ROUTE', 'scollio-logs/dashboard'),
        'prefix' => 'scollio-logs',
        'middleware' => [
            'auth:sanctum',
            'web'
        ],
        'pagination' => env('SCOLLIO_LOGGER_DASHBOARD_PAGINATION', 25),
        'theme' => 'auto',
    ],
    
    'table' => 'scollio_logs',
    'retention_days' => env('SCOLLIO_LOGGER_RETENTION_DAYS', null),
    
    'channels' => [
        'default',
        'request_logging',
        'exception_logging',
    ],
    
    'levels' => [
        'emergency',
        'alert',
        'critical',
        'error',
        'warning',
        'notice',
        'info',
        'debug'
    ],
    
    'colors' => [
        'emergency' => 'bg-red-900 text-white',
        'alert' => 'bg-red-700 text-white',
        'critical' => 'bg-red-600 text-white',
        'error' => 'bg-red-500 text-white',
        'warning' => 'bg-yellow-400 text-black',
        'notice' => 'bg-blue-200 text-black',
        'info' => 'bg-blue-500 text-white',
        'debug' => 'bg-gray-200 text-black'
    ],
    
    'theme_support' => true,

    'middleware' => [
        'request_logging' => [
            'enabled' => env('SCOLLIO_REQUEST_LOGGING_ENABLED', false),
            'log_api' => env('SCOLLIO_REQUEST_LOGGING_API', false),
            'log_web' => env('SCOLLIO_REQUEST_LOGGING_WEB', false),
            'log_payload' => env('SCOLLIO_REQUEST_LOGGING_PAYLOAD', false),
            'log_headers' => env('SCOLLIO_REQUEST_LOGGING_HEADERS', false),

            'skip_routes' => [
                'scollio-logs/dashboard*',
                'telescope/*',
                'horizon/*',
                '_debugbar/*',
                'livewire/*',
            ],

            'sensitive_keys' => [
                'password',
                'password_confirmation',
                'token',
                'secret',
                'key',
                'api_key',
                'access_token',
                'refresh_token',
                'client_secret',
            ],
        ],

        'exception_logging' => [
            // Master switch for exception logging
            'enabled' => env('SCOLLIO_EXCEPTION_LOGGING_ENABLED', false),

            // Control which request types to log exceptions for
            'log_api_exceptions' => env('SCOLLIO_EXCEPTION_LOGGING_API', true),
            'log_web_exceptions' => env('SCOLLIO_EXCEPTION_LOGGING_WEB', true),

            // Additional data to log with exceptions
            'log_stack_trace' => env('SCOLLIO_EXCEPTION_LOGGING_STACK_TRACE', true),
            'log_request_data' => env('SCOLLIO_EXCEPTION_LOGGING_REQUEST_DATA', true),

            // Channel to log exceptions to
            'channel' => env('SCOLLIO_EXCEPTION_LOGGING_CHANNEL', 'exception_logging'),

            // Routes to skip exception logging (performance optimization)
            'skip_routes' => [
                'scollio-logs/dashboard*',
                'telescope/*',
                'horizon/*',
                '_debugbar/*',
                'livewire/*',
            ],

            // Only log exceptions for specific routes (leave empty to log all)
            'only_routes' => [],

            // Ignore specific exception types
            'ignore_exceptions' => [
                // \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
            ],

            // Only log specific exception types (leave empty to log all)
            'only_exceptions' => [],

            // Map exception types to log levels
            'exception_levels' => [
                \Illuminate\Database\QueryException::class => 'critical',
                \Illuminate\Database\Eloquent\ModelNotFoundException::class => 'warning',
                \Illuminate\Validation\ValidationException::class => 'info',
                \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class => 'notice',
                \Symfony\Component\HttpKernel\Exception\HttpException::class => 'warning',
            ],

            // Sensitive keys to filter from request data
            'sensitive_keys' => [
                'password',
                'password_confirmation',
                'token',
                'secret',
                'key',
                'api_key',
                'access_token',
                'refresh_token',
                'client_secret',
            ],
        ],
    ],
];
