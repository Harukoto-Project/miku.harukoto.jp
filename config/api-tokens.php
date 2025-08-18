<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Token Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for API token management,
    | including default permissions and token settings.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Default Permissions
    |--------------------------------------------------------------------------
    |
    | Default permissions that will be assigned to new users when they
    | are created. These can be overridden per user.
    |
    */
    'default_permissions' => [
        'welcomer.read',
        // Add more default permissions as needed
        // 'welcomer.write',
        // 'admin.read',
    ],

    /*
    |--------------------------------------------------------------------------
    | Token Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for token generation and management.
    |
    */
    'token' => [
        // Default token expiration time in hours
        'default_expiration_hours' => env('API_TOKEN_DEFAULT_EXPIRATION_HOURS', 24),
        
        // Maximum token expiration time in hours
        'max_expiration_hours' => env('API_TOKEN_MAX_EXPIRATION_HOURS', 168), // 1 week
        
        // Token length in bytes (will be converted to hex, so actual length is 2x)
        'length_bytes' => env('API_TOKEN_LENGTH_BYTES', 32),
        
        // Whether to auto-generate tokens for new users
        'auto_generate' => env('API_TOKEN_AUTO_GENERATE', true),
        
        // Whether to auto-renew expired tokens
        'auto_renew' => env('API_TOKEN_AUTO_RENEW', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Permission Levels
    |--------------------------------------------------------------------------
    |
    | Available permission levels and their descriptions.
    | These are used for documentation and validation.
    |
    */
    'permission_levels' => [
        '*' => [
            'name' => 'All Permissions',
            'description' => 'Administrator access to all features',
            'scope' => 'Global',
        ],
        'welcomer.read' => [
            'name' => 'Welcomer Read',
            'description' => 'Read Welcomer configuration settings',
            'scope' => 'Welcomer',
        ],
        'welcomer.write' => [
            'name' => 'Welcomer Write',
            'description' => 'Modify Welcomer configuration settings',
            'scope' => 'Welcomer',
        ],
        'admin.read' => [
            'name' => 'Admin Read',
            'description' => 'Read administrative information',
            'scope' => 'Administration',
        ],
        'admin.write' => [
            'name' => 'Admin Write',
            'description' => 'Modify administrative settings',
            'scope' => 'Administration',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Security-related configuration for token management.
    |
    */
    'security' => [
        // Whether to hash tokens in the database
        'hash_tokens' => env('API_TOKEN_HASH_TOKENS', false),
        
        // Rate limiting for token validation
        'rate_limit' => [
            'max_attempts' => env('API_TOKEN_RATE_LIMIT_MAX', 50),
            'decay_minutes' => env('API_TOKEN_RATE_LIMIT_DECAY', 15),
        ],
        
        // Logging settings
        'logging' => [
            'enabled' => env('API_TOKEN_LOGGING_ENABLED', true),
            'level' => env('API_TOKEN_LOGGING_LEVEL', 'info'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for token-related notifications.
    |
    */
    'notifications' => [
        // Whether to notify users when tokens are generated
        'notify_on_generation' => env('API_TOKEN_NOTIFY_ON_GENERATION', false),
        
        // Whether to notify users when tokens are about to expire
        'notify_on_expiration' => env('API_TOKEN_NOTIFY_ON_EXPIRATION', true),
        
        // Hours before expiration to send notification
        'expiration_notification_hours' => env('API_TOKEN_EXPIRATION_NOTIFICATION_HOURS', 1),
    ],
];
