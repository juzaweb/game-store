<?php

namespace Juzaweb\Modules\GameStore\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     *
     * @return void
     */
    public function boot()
    {
        $this->routes(function () {
            // Route::middleware('api')
            //     ->prefix('api/v1')
            //     ->group(__DIR__ . '/../routes/api.php');

            $adminPrefix = $this->app['config']->get('core.admin_prefix');

            Route::middleware(['admin'])
                ->prefix($adminPrefix)
                ->group(__DIR__ . '/../routes/admin.php');

            // Route::middleware(['theme'])
            //     ->prefix(Locale::setLocale())
            //     ->group(__DIR__ . '/../routes/web.php');
        });
    }
}
