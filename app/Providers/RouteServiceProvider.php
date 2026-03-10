<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('aduan-submission', function (Request $request) {
            return [
                Limit::perMinutes(
                    config('ipal.aduan_rate_limit_window_minutes', 30),
                    config('ipal.aduan_rate_limit_per_window', 10)
                )
                    ->by($request->ip())
                    ->response(fn () => response()->json([
                        'success' => false,
                        'message' => 'Terlalu banyak pengiriman. Silakan coba lagi dalam beberapa menit.',
                        'data'    => null,
                    ], 429)),

                Limit::perDay(config('ipal.aduan_rate_limit_per_day', 20))
                    ->by($request->ip())
                    ->response(fn () => response()->json([
                        'success' => false,
                        'message' => 'Batas pengiriman harian telah tercapai. Silakan coba lagi besok.',
                        'data'    => null,
                    ], 429)),
            ];
        });
    }

    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'))
            ->middleware(VerifyEmail::class);
    }
}
