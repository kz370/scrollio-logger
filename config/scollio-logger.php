<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Scollio Logger - Core Enable Toggle
    |--------------------------------------------------------------------------
    |
    | Globally enable or disable all logging features.
    | If set to false, no request or exception logs will be recorded,
    | and all middleware/services from Scollio Logger will be skipped.
    |
    */
    'enabled' => env('SCOLLIO_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Dashboard Title
    |--------------------------------------------------------------------------
    |
    | The title that appears at the top of your Scollio Logger dashboard.
    | By default, it uses your app name followed by "Logs".
    |
    */
    'title' => config('app.name', 'Scollio') . ' Logs',

    /*
    |--------------------------------------------------------------------------
    | Dashboard Configuration
    |--------------------------------------------------------------------------
    |
    | Control the built-in web dashboard for viewing logged requests
    | and exceptions in real-time. You can protect it with middleware
    | (e.g., auth:sanctum), define its route and URL prefix, and pagination.
    |
    */
    'dashboard' => [
        'enabled' => env('SCOLLIO_LOGGER_DASHBOARD_ENABLED', true),

        // Default route and URL prefix for the dashboard.
        'route' => env('SCOLLIO_LOGGER_DASHBOARD_ROUTE', 'scollio-logs/dashboard'),
        'prefix' => 'scollio-logs',

        // Middleware stack for securing the dashboard.
        'middleware' => [
            'auth:sanctum',
            'web'
        ],

        // Number of logs displayed per page.
        'pagination' => env('SCOLLIO_LOGGER_DASHBOARD_PAGINATION', 25),
    ],

    /*
    |--------------------------------------------------------------------------
    | Middleware Options
    |--------------------------------------------------------------------------
    |
    | Controls whether Scollio Logger middleware should be registered globally.
    | If disabled, you can still manually apply middleware aliases to specific
    | routes or route groups.
    |
    */
    'middleware' => [
        'global' => env('SCOLLIO_LOGGER_MIDDLEWARE_GLOBAL', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Excluded Routes
    |--------------------------------------------------------------------------
    |
    | URI patterns that should not be logged. Use wildcards (*) to match
    | dynamic segments. Typically, you’ll exclude internal tools, debug,
    | or background routes to reduce noise in logs.
    |
    */
    'skip_routes' => [
        'scollio-logs/dashboard*',
        'telescope/*',
        'horizon/*',
        '_debugbar/*',
        'livewire/*',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    |
    | Default pagination limit used for listing logs via the dashboard
    | or API endpoints. Can be overridden per page request.
    |
    */
    'paginate' => (int) env('SCOLLIO_PAGINATE', 15),

    /*
    |--------------------------------------------------------------------------
    | Route Group Logging Toggles
    |--------------------------------------------------------------------------
    |
    | Fine-grained control over which route groups should be logged.
    | Typically, you’ll log both web and API traffic, but you can
    | disable either type as needed.
    |
    */
    'log_web_routes' => env('SCOLLIO_LOG_WEB_ROUTES', true),
    'log_api_routes' => env('SCOLLIO_LOG_API_ROUTES', true),

    /*
    |--------------------------------------------------------------------------
    | Status Code Filtering
    |--------------------------------------------------------------------------
    |
    | - ignore_status_codes: Skip saving responses with these codes.
    | - only_status_codes: Only log these specific codes.
    |
    | If "only_status_codes" is set, it takes precedence over "ignore_status_codes".
    | This helps reduce noise from common redirects or browser prefetches.
    |
    */
    'ignore_status_codes' => array_filter(array_map('intval', explode(',', env('SCOLLIO_IGNORE_STATUS_CODES', '302,304,419,429')))),
    'only_status_codes'   => array_filter(array_map('intval', explode(',', env('SCOLLIO_ONLY_STATUS_CODES', '')))),

    /*
    |--------------------------------------------------------------------------
    | Sensitive Key Filtering
    |--------------------------------------------------------------------------
    |
    | Prevent sensitive data from being stored in your logs.
    |
    | - sensitive_exact_keys: Matches exact key names (case-insensitive).
    | - sensitive_partial_keys: Matches partial substrings (e.g., "_token").
    |
    | Values from matching keys will be replaced with "[FILTERED]" before saving.
    |
    */
    'sensitive_exact_keys' => [
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

    'sensitive_partial_keys' => [
        '_token',
        'authorization',
        'bearer',
        'auth'
    ],

    /*
    |--------------------------------------------------------------------------
    | Sanitized Response Body
    |--------------------------------------------------------------------------
    |
    | Replace sensitive data in the response body with "[FILTERED]".
    |
    */

    'filtered_string' => env('SCOLLIO_FILTERED_STRING', '[FILTERED]'),

    /*
    |--------------------------------------------------------------------------
    | Log Retention & Rotation
    |--------------------------------------------------------------------------
    |
    | Automatically remove old logs to prevent your database from growing
    | indefinitely.
    |
    | - retention_days: Number of days to keep logs.
    | - rotation_every_minutes: Minimum interval (in minutes) between
    |   rotation runs. The cleanup runs opportunistically on requests.
    |
    */
    'retention_days'         => (int) env('SCOLLIO_RETENTION_DAYS', 30),
    'rotation_every_minutes' => (int) env('SCOLLIO_ROTATION_EVERY_MINUTES', 1440),
];
