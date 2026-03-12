<?php

namespace Modules\IPAL\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;

class IPALServiceProvider extends ServiceProvider
{
    /**
     * Namespace module ini.
     */
    protected string $moduleNamespace = 'Modules\IPAL\Http\Controllers';

    /**
     * Boot module IPAL.
     */
    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerViews();
        $this->registerMigrations();
        $this->registerConfig();
        $this->registerComponents();
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
    }

    /**
     * Register routes untuk module IPAL.
     */
    protected function registerRoutes(): void
    {
        // Web routes
        Route::middleware('web')
            ->namespace($this->moduleNamespace)
            ->prefix('ipal')
            ->name('ipal.')
            ->group(module_path('IPAL', 'Routes/web.php'));

        // API routes
        Route::middleware('api')
            ->namespace($this->moduleNamespace)
            ->prefix('api/ipal')
            ->name('api.ipal.')
            ->group(module_path('IPAL', 'Routes/api.php'));
    }

    /**
     * Register views untuk module IPAL.
     */
    protected function registerViews(): void
    {
        $viewPath = resource_path('views/modules/ipal');
        $sourcePath = module_path('IPAL', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], 'ipal-views');

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), 'ipal');
    }

    /**
     * Register Blade anonymous components untuk module IPAL.
     */
    protected function registerComponents(): void
    {
        Blade::anonymousComponentPath(
            module_path('IPAL', 'Resources/views/components')
        );
    }

    /**
     * Register migrations untuk module IPAL.
     */
    protected function registerMigrations(): void
    {
        $this->loadMigrationsFrom(module_path('IPAL', 'Database/Migrations'));
    }

    /**
     * Register config untuk module IPAL.
     */
    protected function registerConfig(): void
    {
        $this->publishes([
            module_path('IPAL', 'Config/config.php') => config_path('ipal.php'),
        ], 'ipal-config');

        $this->mergeConfigFrom(module_path('IPAL', 'Config/config.php'), 'ipal');
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    /**
     * Get publishable view paths.
     */
    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path . '/modules/ipal')) {
                $paths[] = $path . '/modules/ipal';
            }
        }
        return $paths;
    }
}
