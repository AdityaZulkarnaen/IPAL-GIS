<?php

namespace Modules\IPAL\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckIPALAccess
{
    /**
     * Middleware untuk cek akses ke module IPAL.
     * Bisa dikustomisasi sesuai kebutuhan role IPAL.
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah module IPAL aktif
        if (!config('ipal.enabled', true)) {
            abort(404, 'Module IPAL tidak aktif.');
        }

        return $next($request);
    }
}
