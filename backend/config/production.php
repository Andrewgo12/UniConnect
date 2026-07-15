<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Production Environment Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration settings for the production environment.
    | It includes database connections, cache settings, security settings,
    | and other environment-specific configurations.
    |
    */

    'app' => [
        'name' => env('APP_NAME', 'UniConnect'),
        'env' => env('APP_ENV', 'production'),
        'debug' => env('APP_DEBUG', false),
        'url' => env('APP_URL', 'https://api.uniconect.com'),
        'timezone' => env('APP_TIMEZONE', 'America/Bogota'),
        'locale' => 'es',
        'fallback_locale' => 'en',
        'key' => env('APP_KEY'),
        'cipher' => 'AES-256-CBC',
        'maintenance' => [
            'driver' => 'file',
            'file' => storage_path('framework/down'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, the examples below are configured for the MySQL database.
    |
    */

    'database' => [
        'default' => [
            'driver' => env('DB_CONNECTION', 'mysql'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'uniconect'),
            'username' => env('DB_USERNAME', 'uniconect_user'),
            'password' => env('DB_PASSWORD'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                // PDO::MYSQL_ATTR_SSL_DISABLE_SSL_VERIFY_PEER => true,
            ]) : [],
            'pool' => [
                'min' => 5,
                'max' => 20,
                'wait_timeout' => 60,
                'wait_after' => 5,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Cache settings for the application.
    |
    */

    'cache' => [
        'default' => env('CACHE_DRIVER', 'redis'),
        'stores' => [
            'redis' => [
                'driver' => 'redis',
                'connection' => 'default',
                'url' => env('REDIS_URL'),
                'password' => env('REDIS_PASSWORD', null),
                'database' => env('REDIS_DB', 0),
                'port' => env('REDIS_PORT', 6379),
                'prefix' => env('REDIS_PREFIX', 'uniconect'),
                'read_timeout' => 60,
                'write_timeout' => 60,
            ],
        ],
        'prefix' => env('CACHE_PREFIX', 'uniconect'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Configuration
    |--------------------------------------------------------------------------
    |
    | Session configuration for the application.
    |
    */

    'session' => [
        'driver' => env('SESSION_DRIVER', 'file'),
        'lifetime' => env('SESSION_LIFETIME', 120),
        'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),
        'encrypt' => false,
        'files' => 'sessions',
        'connection' => null,
        'table' => 'sessions',
        'store' => null,
        'lottery' => [2, 100],
        'cookie' => env('SESSION_COOKIE', 'uniconect_session'),
        'path' => '/',
        'secure' => env('SESSION_SECURE_COOKIE', true),
        'http_only' => env('SESSION_HTTP_ONLY', true),
        'same_site' => env('SESSION_SAME_SITE', 'lax'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Queue configuration for the application.
    |
    */

    'queue' => [
        'default' => env('QUEUE_CONNECTION', 'redis'),
        'connections' => [
            'redis' => [
                'driver' => 'redis',
                'connection' => 'default',
                'url' => env('REDIS_URL'),
                'password' => env('REDIS_PASSWORD', null),
                'database' => env('REDIS_DB', 1),
                'port' => env('REDIS_PORT', 6379),
                'prefix' => env('REDIS_PREFIX', 'uniconect_queue'),
                'read_timeout' => 60,
                'write_timeout' => 60,
                'block_for' => 0,
                'after_commit' => 0,
            ],
        ],
        'failed' => [
            'driver' => env('QUEUE_FAILED_DRIVER', 'database'),
            'database' => env('QUEUE_FAILED_DATABASE', 'mysql'),
            'table' => 'failed_jobs',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Filesystem Configuration
    |--------------------------------------------------------------------------
    |
    | Filesystem configuration for the application.
    |
    */

    'filesystems' => [
        'default' => env('FILESYSTEM_DRIVER', 'local'),
        'cloud' => env('CLOUD_FILESYSTEM_DRIVER', 's3'),
        'disks' => [
            'local' => [
                'driver' => 'local',
                'root' => storage_path('app'),
            ],
            'public' => [
                'driver' => 'local',
                'root' => storage_path('app/public'),
                'url' => env('APP_URL').'/storage',
            ],
            's3' => [
                'driver' => 's3',
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
                'region' => env('AWS_DEFAULT_REGION'),
                'bucket' => env('AWS_BUCKET'),
                'url' => env('AWS_URL'),
                'endpoint' => env('AWS_ENDPOINT'),
                'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
                'options' => [
                    'visibility' => 'public',
                    'cache' => [
                        'Store' => 'redis',
                        'Expire' => 600,
                    ],
                ],
            ],
            'backups' => [
                'driver' => 'local',
                'root' => storage_path('app/backups'),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Logging configuration for the application.
    |
    */

    'logging' => [
        'default' => env('LOG_CHANNEL', 'stack'),
        'channels' => [
            'stack' => [
                'driver' => 'single',
                'path' => storage_path('logs/laravel.log'),
                'level' => env('LOG_LEVEL', 'debug'),
                'replace_placeholders' => true,
            ],
            'daily' => [
                'driver' => 'daily',
                'path' => storage_path('logs/daily'),
                'level' => env('LOG_LEVEL', 'debug'),
                'replace_placeholders' => true,
            ],
            'security' => [
                'driver' => 'daily',
                'path' => storage_path('logs/security'),
                'level' => env('LOG_LEVEL', 'debug'),
                'replace_placeholders' => true,
            ],
            'accessibility' => [
                'driver' => 'daily',
                'path' => storage_path('logs/accessibility'),
                'level' => env('LOG_LEVEL', 'debug'),
                'replace_placeholders' => true,
            ],
        ],
        'deprecations' => env('LOG_DEPRECATIONS_CHANNEL', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Security configuration for the application.
    |
    */

    'security' => [
        'encryption' => [
            'key' => env('APP_KEY'),
            'cipher' => 'AES-256-CBC',
        ],
        'csrf' => [
            'token' => env('CSRF_TOKEN'),
            'http_only' => env('CSRF_HTTP_ONLY', true),
            'secure_cookie' => env('CSRF_SECURE_COOKIE', true),
            'same_site' => env('CSRF_SAME_SITE', 'lax'),
        ],
        'rate_limiting' => [
            'enabled' => env('RATE_LIMITING_ENABLED', true),
            'throttle' => [
                'api' => env('API_RATE_LIMIT', '60:1'),
                'auth' => env('AUTH_RATE_LIMIT', '5:1'),
                'uploads' => env('UPLOAD_RATE_LIMIT', '10:1'),
            ],
        ],
        'ip_blocking' => [
            'enabled' => env('IP_BLOCKING_ENABLED', true),
            'max_attempts' => env('IP_MAX_ATTEMPTS', 5),
            'lockout_duration' => env('IP_LOCKOUT_DURATION', 900),
        ],
        'password_policy' => [
            'min_length' => env('PASSWORD_MIN_LENGTH', 8),
            'max_length' => env('PASSWORD_MAX_LENGTH', 72),
            'require_uppercase' => env('PASSWORD_REQUIRE_UPPERCASE', true),
            'require_lowercase' => env('PASSWORD_REQUIRE_LOWERCASE', true),
            'require_numbers' => env('PASSWORD_REQUIRE_NUMBERS', true),
            'require_symbols' => env('PASSWORD_REQUIRE_SYMBOLS', false),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Broadcasting Configuration
    |--------------------------------------------------------------------------
    |
    | Broadcasting configuration for the application.
    |
    */

    'broadcasting' => [
        'default' => env('BROADCAST_DRIVER', 'redis'),
        'connections' => [
            'redis' => [
                'driver' => 'redis',
                'connection' => 'default',
                'url' => env('REDIS_URL'),
                'password' => env('REDIS_PASSWORD', null),
                'database' => env('REDIS_DB', 0),
                'port' => env('REDIS_PORT', 6379),
                'prefix' => env('REDIS_PREFIX', 'uniconect_broadcast'),
                'read_timeout' => 60,
                'write_timeout' => 60,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Services Configuration
    |--------------------------------------------------------------------------
    |
    | Service configuration for the application.
    |
    */

    'services' => [
        'mail' => [
            'transport' => env('MAIL_MAILER', 'smtp'),
            'host' => env('MAIL_HOST', 'smtp.mailtrap.io'),
            'port' => env('MAIL_PORT', 2525),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'noreply@uniconect.com'),
                'name' => env('MAIL_FROM_NAME', 'UniConnect'),
            ],
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'stream' => 'tls',
            'timeout' => null,
            'auth_mode' => null,
        ],
        'push_notification' => [
            'driver' => 'fcm',
            'key' => env('FIREBASE_CREDENTIALS'),
            'app_id' => env('FIREBASE_APP_ID'),
            'priority' => 'high',
        ],
        'sms' => [
            'driver' => 'twilio',
            'from' => env('TWILIO_FROM'),
            'sid' => env('TWILIO_SID'),
            'token' => env('TWILIO_TOKEN'),
        ],
        'vibration' => [
            'enabled' => env('VIBRATION_ENABLED', true),
            'default_intensity' => env('VIBRATION_DEFAULT_INTENSITY', 'medium'),
            'max_duration' => env('VIBRATION_MAX_DURATION', 3000),
        ],
        'accessibility' => [
            'screen_reader' => [
                'enabled' => env('SCREEN_READER_ENABLED', true),
                'voice_rate' => env('VOICE_RATE', 'medium'),
            ],
            'voice_commands' => [
                'enabled' => env('VOICE_COMMANDS_ENABLED', true),
                'language' => env('VOICE_LANGUAGE', 'es-CO'),
            ],
            'high_contrast' => [
                'enabled' => env('HIGH_CONTRAST_ENABLED', true),
                'theme' => env('HIGH_CONTRAST_THEME', 'dark'),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    |
    | Performance optimization configuration.
    |
    */

    'performance' => [
        'cache' => [
            'enabled' => env('PERFORMANCE_CACHE_ENABLED', true),
            'default_ttl' => env('CACHE_DEFAULT_TTL', 3600),
            'redis_memory_limit' => env('REDIS_MEMORY_LIMIT', '512mb'),
        ],
        'database' => [
            'slow_query_threshold' => env('DB_SLOW_QUERY_THRESHOLD', 1000),
            'query_log_enabled' => env('DB_QUERY_LOG_ENABLED', false),
            'connection_pool' => [
                'min' => env('DB_POOL_MIN', 5),
                'max' => env('DB_POOL_MAX', 20),
            ],
        ],
        'api' => [
            'response_time_threshold' => env('API_RESPONSE_TIME_THRESHOLD', 500),
            'rate_limiting' => [
                'enabled' => env('API_RATE_LIMITING_ENABLED', true),
                'requests_per_minute' => env('API_REQUESTS_PER_MINUTE', 100),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring Configuration
    |--------------------------------------------------------------------------
    |
    | Monitoring and health check configuration.
    |
    */

    'monitoring' => [
        'health_check' => [
            'enabled' => env('HEALTH_CHECK_ENABLED', true),
            'endpoint' => '/api/v1/health',
            'interval' => env('HEALTH_CHECK_INTERVAL', 60),
        ],
        'error_tracking' => [
            'enabled' => env('ERROR_TRACKING_ENABLED', true),
            'channels' => ['stack', 'security', 'accessibility'],
            'slack_webhook' => env('SLACK_WEBHOOK_URL'),
            'email_recipients' => env('ERROR_EMAIL_RECIPIENTS', 'admin@uniconect.com'),
        ],
        'analytics' => [
            'enabled' => env('ANALYTICS_ENABLED', true),
            'tracking_endpoint' => env('ANALYTICS_ENDPOINT'),
            'api_key' => env('ANALYTICS_API_KEY'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Accessibility Configuration
    |--------------------------------------------------------------------------
    |
    | Accessibility and accessibility features configuration.
    |
    */

    'accessibility' => [
        'vibration_patterns' => [
            'emergency' => [
                'pattern' => [100, 200, 100, 200, 100, 200, 100, 200],
                'duration' => 2000,
                'intensity' => 'high',
            ],
            'message_received' => [
                'pattern' => [50, 50, 50, 50, 50, 50, 50, 50],
                'duration' => 500,
                'intensity' => 'medium',
            ],
        ],
        'screen_reader' => [
            'enabled' => true,
            'voice_rate' => 'medium',
            'language' => 'es-CO',
            'aria_labels' => true,
        ],
        'voice_commands' => [
            'enabled' => true,
            'language' => 'es-CO',
            'commands' => [
                'navigate' => ['navegar', 'ir a'],
                'message' => ['enviar mensaje', 'llamar emergencia'],
                'emergency' => ['activar emergencia', 'llamar contacto'],
            ],
        ],
        'high_contrast' => [
            'enabled' => true,
            'theme' => 'dark',
            'color_scheme' => 'blue_yellow_black',
            'large_text' => true,
        ],
        'language_support' => [
            'enabled' => true,
            'default_language' => 'es-CO',
            'supported_languages' => ['es-CO', 'en-US', 'pt-BR'],
        ],
    ],
];
