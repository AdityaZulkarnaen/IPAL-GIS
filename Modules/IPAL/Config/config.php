<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Module IPAL Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk module IPAL (Instalasi Pengolahan Air Limbah).
    | Developer IPAL bisa menambahkan konfigurasi yang diperlukan di sini.
    |
    */

    'name' => 'IPAL',

    'description' => 'Module Instalasi Pengolahan Air Limbah (IPAL)',

    /**
     * Apakah module ini aktif atau tidak.
     * Bisa diatur melalui .env dengan key IPAL_MODULE_ENABLED
     */
    'enabled' => env('IPAL_MODULE_ENABLED', true),

    /**
     * Prefix URL untuk module IPAL.
     * Semua route IPAL akan diawali dengan prefix ini.
     */
    'route_prefix' => 'ipal',

    /**
     * Middleware default untuk module IPAL.
     */
    'middleware' => ['web', 'auth', 'verified'],

    /**
     * Role yang diizinkan mengakses module IPAL.
     */
    'allowed_roles' => ['Super Admin', 'Admin'],

    /**
     * Konfigurasi fitur Aduan.
     */
    'aduan_max_foto'          => env('IPAL_ADUAN_MAX_FOTO', 5),
    'aduan_foto_max_kb_user'  => env('IPAL_ADUAN_FOTO_MAX_KB_USER', 200),
    'aduan_foto_max_kb_admin' => env('IPAL_ADUAN_FOTO_MAX_KB_ADMIN', 1024),

    /**
     * Aktifkan captcha matematika pada form aduan publik.
     */
    'aduan_captcha_enabled'   => env('IPAL_ADUAN_CAPTCHA_ENABLED', false),

    /**
     * Rate limiting untuk endpoint POST /aduan.
     * Dua layer: per time window dan per hari, keduanya berdasarkan IP.
     */
    'aduan_rate_limit_per_window'      => env('IPAL_ADUAN_RATE_LIMIT_PER_WINDOW', 10),
    'aduan_rate_limit_window_minutes'  => env('IPAL_ADUAN_RATE_LIMIT_WINDOW_MINUTES', 30),
    'aduan_rate_limit_per_day'         => env('IPAL_ADUAN_RATE_LIMIT_PER_DAY', 20),

];
