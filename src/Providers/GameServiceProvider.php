<?php

namespace Juzaweb\Modules\GameStore\Providers;

use Illuminate\Support\Facades\File;
use Juzaweb\Modules\Core\Facades\Menu;
use Juzaweb\Modules\Core\Facades\MenuBox;
use Juzaweb\Modules\Core\Facades\Sitemap;
use Juzaweb\Modules\Core\Providers\ServiceProvider;
use Juzaweb\Modules\GameStore\Models\GameCategory;
use Juzaweb\Modules\GameStore\Models\GameLanguage;
use Juzaweb\Modules\GameStore\Models\GamePlatform;
use Juzaweb\Modules\GameStore\Models\GameTranslation;
use Juzaweb\Modules\GameStore\Models\GameVendor;
use Juzaweb\Modules\GameStore\Services\GamePaymentHandler;
use Juzaweb\Modules\Payment\Contracts\PaymentManager;

class GameServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app[PaymentManager::class]->registerModule(
            'game',
            new GamePaymentHandler()
        );

        Sitemap::register(
            'games',
            GameTranslation::class
        );

        MenuBox::make('game-categories', GameCategory::class, function () {
            return [
                'label' => __('game-store::translation.categories'),
                'icon' => 'fas fa-tags',
                'priority' => 10,
                'field' => 'name',
            ];
        });

        MenuBox::make('game-vendors', GameVendor::class, function () {
            return [
                'label' => __('game-store::translation.vendors'),
                'icon' => 'fas fa-tags',
                'priority' => 10,
                'field' => 'name',
            ];
        });

        MenuBox::make('game-platforms', GamePlatform::class, function () {
            return [
                'label' => __('game-store::translation.platforms'),
                'icon' => 'fas fa-tags',
                'priority' => 10,
                'field' => 'name',
            ];
        });

        MenuBox::make('game-languages', GameLanguage::class, function () {
            return [
                'label' => __('game-store::translation.languages'),
                'icon' => 'fas fa-tags',
                'priority' => 10,
                'field' => 'name',
            ];
        });

        $this->booted(
            function () {
                $this->registerMenus();
            }
        );
    }

    public function register(): void
    {
        $this->registerHelpers();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/migrations');
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerMenus(): void
    {
        if (File::missing(storage_path('app/installed'))) {
            return;
        }

        Menu::make('games-management', function () {
            return [
                'title' => __('game-store::translation.games'),
                'icon' => 'fa fa-gamepad',
                'priority' => 31,
                'url' => 'games',
            ];
        });

        Menu::make('games', function () {
            return [
                'title' => __('game-store::translation.games'),
                'parent' => 'games-management',
                'permissions' => ['games.index'],
            ];
        });

        Menu::make('game-categories', function () {
            return [
                'title' => __('game-store::translation.categories'),
                'parent' => 'games-management',
                'permissions' => ['game-categories.index'],
            ];
        });

        Menu::make('game-vendors', function () {
            return [
                'title' => __('game-store::translation.vendors'),
                'parent' => 'games-management',
                'permissions' => ['game-vendors.index'],
            ];
        });

        Menu::make('game-platforms', function () {
            return [
                'title' => __('game-store::translation.platforms'),
                'parent' => 'games-management',
                'permissions' => ['game-platforms.index'],
            ];
        });

        Menu::make('game-languages', function () {
            return [
                'title' => __('game-store::translation.languages'),
                'parent' => 'games-management',
                'permissions' => ['game-languages.index'],
            ];
        });

        Menu::make('game-comments', function () {
            return [
                'title' => __('core::translation.comments'),
                'parent' => 'games-management',
                'permissions' => ['game-comments.index'],
            ];
        });

        Menu::make('game-settings', function () {
            return [
                'title' => __('game-store::translation.settings'),
                'parent' => 'games-management',
                'priority' => 99,
                'permissions' => ['game-settings.index'],
            ];
        });
    }

    protected function registerHelpers(): void
    {
        $helpersFile = __DIR__ . '/../helpers/helpers.php';
        if (file_exists($helpersFile)) {
            require_once $helpersFile;
        }
    }

    protected function registerConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../config/game-store.php' => config_path('game-store.php'),
        ], 'game-store-config');
        $this->mergeConfigFrom(__DIR__ . '/../config/game-store.php', 'game-store');
    }

    protected function registerTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'game-store');
        $this->loadJsonTranslationsFrom(__DIR__ . '/../resources/lang');
    }

    protected function registerViews(): void
    {
        $viewPath = resource_path('views/modules/game');

        $sourcePath = __DIR__ . '/../resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', 'game-module-views']);

        $this->loadViewsFrom($sourcePath, 'game-store');
    }
}
