<?php

use Illuminate\Support\Facades\Route;
use Juzaweb\Modules\GameStore\Http\Controllers\GameController;
use Juzaweb\Modules\GameStore\Http\Controllers\GameCategoryController;
use Juzaweb\Modules\GameStore\Http\Controllers\SettingController;
use Juzaweb\Modules\GameStore\Http\Controllers\GamePlatformController;
use Juzaweb\Modules\GameStore\Http\Controllers\GameVendorController;
use Juzaweb\Modules\GameStore\Http\Controllers\GameLanguageController;
use Juzaweb\Modules\GameStore\Http\Controllers\Admin\CommentController;

Route::admin('games', GameController::class);

Route::admin('game-comments', CommentController::class)
    ->except(['create', 'edit', 'store', 'update']);
Route::admin('game-categories', GameCategoryController::class);

Route::get('/game-settings', [SettingController::class, 'index'])
    ->name('admin.game-settings.index')
    ->middleware(['permission:game-settings.index']);
Route::put('/game-settings', [SettingController::class, 'update'])
    ->name('admin.game-settings.update')
    ->middleware(['permission:game-settings.update']);

// IGDB import routes
Route::post('/games/igdb/search', [GameController::class, 'searchIgdb'])
    ->name('admin.games.igdb.search')
    ->middleware(['permission:games.create']);
Route::post('/games/igdb/import', [GameController::class, 'importFromIgdb'])
    ->name('admin.games.igdb.import')
    ->middleware(['permission:games.create']);

Route::admin('game-platforms', GamePlatformController::class);

Route::admin('game-vendors', GameVendorController::class);
Route::admin('game-languages', GameLanguageController::class);
