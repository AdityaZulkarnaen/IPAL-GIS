<?php

if (!function_exists('module_path')) {
    /**
     * Get the path to a module folder.
     *
     * @param string $module Nama module (contoh: 'IPAL')
     * @param string $path   Sub-path di dalam module (contoh: 'Routes/web.php')
     * @return string
     */
    function module_path(string $module, string $path = ''): string
    {
        $modulePath = base_path("Modules/{$module}");

        return $path ? "{$modulePath}/{$path}" : $modulePath;
    }
}
