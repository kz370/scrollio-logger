<?php

return [
    'enabled' => true,
    'dashboard' => [
        'enabled' => true,
        'route' => 'scollio-logs/dashboard',
        'prefix' => 'scollio-logs',
        'middleware' => [
            'web'
        ],
        'pagination' => 25,
        'theme' => 'auto'
    ],
    'table' => 'scollio_logs',
    'retention_days' => null,
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
    'theme_support' => [
        'light' => true,
        'dark' => true
    ]
];