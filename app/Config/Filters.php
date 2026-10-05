<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use App\Filters\AuthFilter;
use App\Filters\RoleFilter;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseConfig
{
    /**
     * Aliases untuk filter
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,

        // Filter autentikasi dan role
        'auth'          => AuthFilter::class,
        'role'          => RoleFilter::class,
        'rolefilter'    => RoleFilter::class,
    ];

    /**
     * Global filters
     */
    public array $globals = [
        'before' => [
            // Tidak menggunakan auth secara global.
            // Auth diterapkan pada route tertentu.
        ],
        'after' => [
            'toolbar',
        ],
    ];

    /**
     * Filters by HTTP method
     */
    public array $methods = [];

    /**
     * Filters by route
     */
    public array $filters = [];
}