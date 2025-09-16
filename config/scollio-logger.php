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
        'default'
    ],
    'levels' => [
        'emergency','alert','critical','error','warning','notice','info','debug'
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

    // New middleware configuration
    'middleware' => [
        'request_logging' => [
            'enabled' => env('SCOLLIO_REQUEST_LOGGING_ENABLED', false),
            'log_api' => env('SCOLLIO_REQUEST_LOGGING_API', false),
            'log_web' => env('SCOLLIO_REQUEST_LOGGING_WEB', false),
            'log_payload' => env('SCOLLIO_REQUEST_LOGGING_PAYLOAD', false),
            'log_headers' => env('SCOLLIO_REQUEST_LOGGING_HEADERS', false),
            
            // Routes to skip (performance optimization)
            'skip_routes' => [
                'scollio-logs/dashboard*',
                'telescope/*',
                'horizon/*',
                '_debugbar/*',
                'livewire/*',
            ],
            
            // Sensitive keys to filter from payload
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
