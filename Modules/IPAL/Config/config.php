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

];
