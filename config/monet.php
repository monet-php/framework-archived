<?php

return [
    'auth' => [
        'require_email_verification' => (bool) env('MONET_AUTH_REQUIRE_EMAIL_VERIFICATION', true),

        'routes' => [
            'login' => env('MONET_AUTH_LOGIN_ROUTE', '/login'),
            'register' => env('MONET_AUTH_REGISTER_ROUTE', '/register'),
            'logout' => env('MONET_AUTH_LOGOUT_ROUTE', '/logout'),
            'password' => [
                'request' => env('MONET_AUTH_PASSWORD_REQUEST_ROUTE', '/forgot-password'),
                'reset' => env('MONET_AUTH_PASSWORD_RESET_ROUTE', '/reset-password'),
                'confirm' => env('MONET_AUTH_PASSWORD_CONFIRM_ROUTE', '/confirm-password'),
            ],
            'email' => [
                'notice' => env('MONET_AUTH_EMAIL_NOTICE_ROUTE', '/email-verification'),
                'verify' => env('MONET_AUTH_EMAIL_VERIFY_ROUTE', '/email-verification'),
            ],
        ],
    ],

    'settings' => [
        'driver' => 'file',

        'cache' => [
            'enabled' => true,
            'key' => env('MONET_SETTINGS_CACHE_KEY', 'monet.settings'),
            'ttl' => -1,
        ],

        'file' => [
            'disk' => 'local',
            'path' => 'settings.json',
        ],

        'database' => [
            'table' => 'settings',

            'columns' => [
                'key' => env('MONET_SETTINGS_DATABASE_COLUMNS_KEY', 'key'),
                'value' => env('MONET_SETTINGS_DATABASE_COLUMNS_VALUE', 'value'),
                'autoload' => env('MONET_SETTINGS_DATABASE_COLUMNS_AUTOLOAD', 'autoload'),
            ],
        ],
    ],

    'modules' => [
        'paths' => [
            env('MONET_MODULES_PATH', base_path('modules')),
        ],

        'cache' => [
            'enabled' => true,
            'keys' => [
                'all' => config('MONET_MODULES_CACHE_KEY', 'monet.modules.all'),
                'ordered' => config('MONET_MODULES_CACHE_KEY', 'monet.modules.ordered'),
            ],
        ],
    ],

    'themes' => [
        'paths' => [
            env('MONET_THEMES_PATH', base_path('themes')),
        ],

        'cache' => [
            'enabled' => true,
            'key' => env('MONET_THEMES_CACHE_KEY', 'monet.themes'),
        ],
    ],
];
