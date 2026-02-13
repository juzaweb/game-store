<?php

namespace Juzaweb\Modules\GameStore\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Juzaweb\Modules\Core\Facades\Breadcrumb;
use Juzaweb\Modules\Core\FileManager\MediaUploader;
use Juzaweb\Modules\Core\Http\Controllers\AdminController;
use Juzaweb\Modules\GameStore\Enums\GameStatus;
use Juzaweb\Modules\GameStore\Http\DataTables\GamesDatatable;
use Juzaweb\Modules\GameStore\Http\Requests\GameActionsRequest;
use Juzaweb\Modules\GameStore\Http\Requests\GameRequest;
use Juzaweb\Modules\GameStore\Http\Requests\IgdbImportRequest;
use Juzaweb\Modules\GameStore\Models\Game;
use Juzaweb\Modules\GameStore\Models\GameCategory;
use Juzaweb\Modules\GameStore\Models\GameLanguage;
use Juzaweb\Modules\GameStore\Models\GamePlatform;
use Juzaweb\Modules\GameStore\Models\GameVendor;
use MarcReichel\IGDBLaravel\Models\Game as IGDBGame;

class GameController extends AdminController
{
    public function index(GamesDatatable $dataTable)
    {
        Breadcrumb::add(__('game-store::translation.games'));

        $createUrl = action([static::class, 'create']);

        return $dataTable->render(
            'game-store::game.index',
            compact('createUrl')
        );
    }

    public function create()
    {
        Breadcrumb::add(__('game-store::translation.games'), admin_url('games'));

        Breadcrumb::add(__('game-store::translation.create_game'));

        $backUrl = action([static::class, 'index']);
        $locale = $this->getFormLanguage();
        $categories = GameCategory::withTranslation()
            ->with('children')
            ->whereNull('parent_id')
            ->get();

        return view(
            'game-store::game.form',
            [
                'model' => new Game,
                'action' => action([static::class, 'store']),
                'backUrl' => $backUrl,
                'locale' => $locale,
                'categories' => $categories,
            ]
        );
    }

    public function edit(string $id)
    {
        Breadcrumb::add(__('game-store::translation.games'), admin_url('games'));

        Breadcrumb::add(__('game-store::translation.edit_game'));

        $locale = $this->getFormLanguage();
        $model = Game::withTranslation($locale)
            ->with([
                'downloadLinks',
                'vendors' => fn($q) => $q->withTranslation($locale),
                'platforms',
                'languages' => fn($q) => $q->withTranslation($locale),
                'systemRequirements',
            ])
            ->findOrFail($id);

        $model->setDefaultLocale($locale);
        $backUrl = action([static::class, 'index']);
        $categories = GameCategory::withTranslation()
            ->with('children')
            ->whereNull('parent_id')
            ->get();

        return view(
            'game-store::game.form',
            [
                'action' => action([static::class, 'update'], [$id]),
                'model' => $model,
                'backUrl' => $backUrl,
                'locale' => $locale,
                'categories' => $categories,
            ]
        );
    }

    public function store(GameRequest $request)
    {
        $locale = $this->getFormLanguage();
        $model = DB::transaction(
            function () use ($request, $locale) {
                $data = $request->validated();
                $game = new Game($data);
                $game->setDefaultLocale($locale);
                $game->save();

                if ($request->has('categories')) {
                    $game->categories()->sync($request->input('categories', []));
                }

                $this->syncVendors($game, $request->input('vendors', []));
                $this->syncPlatforms($game, $request->input('platforms', []));
                $this->syncLanguages($game, $request->input('languages', []));

                $game->setThumbnail($request->input('thumbnail'));

                $screenshots = $request->input('screenshots', []);
                if ($screenshots) {
                    $game->attachMedia($screenshots, 'screenshots');
                }

                // Handle download links
                if ($request->has('download_links')) {
                    $downloadLinks = $request->input('download_links', []);
                    foreach ($downloadLinks as $index => $linkData) {
                        if (empty($linkData['url'])) {
                            continue;
                        }

                        $game->downloadLinks()->create([
                            'title' => $linkData['title'] ?? null,
                            'url' => $linkData['url'],
                            'size' => $linkData['size'] ?? null,
                            'platform' => $linkData['platform'] ?? null,
                            'order' => $index,
                        ]);
                    }
                }

                return $game;
            }
        );

        return $this->success([
            'redirect' => action([static::class, 'edit'], [$model->id]),
            'message' => __('game-store::translation.game_name_created_successfully', ['name' => $model->title]),
        ]);
    }

    public function update(GameRequest $request, string $id)
    {
        $locale = $this->getFormLanguage();
        $model = Game::findOrFail($id);

        $model = DB::transaction(
            function () use ($request, $model, $locale) {
                $model->setDefaultLocale($locale);
                $data = $request->validated();
                $model->update($data);

                $model->categories()->sync($request->input('categories', []));

                $this->syncVendors($model, $request->input('vendors', []));
                $this->syncPlatforms($model, $request->input('platforms', []));
                $this->syncLanguages($model, $request->input('languages', []));

                $model->setThumbnail($request->input('thumbnail'));

                $model->clearMediaChannel('screenshots');
                $screenshots = $request->input('screenshots', []);
                if ($screenshots) {
                    $model->attachMedia($screenshots, 'screenshots');
                }

                // Handle download links
                if ($request->has('download_links')) {
                    $downloadLinks = $request->input('download_links', []);
                    $existingIds = [];

                    $index = 1;
                    foreach ($downloadLinks as $linkData) {
                        if (empty($linkData['url'])) {
                            continue;
                        }

                        $downloadLink = $model->downloadLinks()->updateOrCreate(
                            [
                                'id' => $linkData['id'] ?? null,
                            ],
                            [
                                'title' => $linkData['title'] ?? null,
                                'url' => $linkData['url'],
                                'size' => $linkData['size'] ?? null,
                                'platform' => $linkData['platform'] ?? null,
                                'order' => $index,
                            ]
                        );

                        $existingIds[] = $downloadLink->id;
                        $index++;
                    }

                    // Delete removed links
                    $model->downloadLinks()
                        ->whereNotIn('id', $existingIds)
                        ->get()
                        ->each
                        ->delete();
                }

                // Handle system requirements (2 simple fields: minimum and recommended)
                // Update or create minimum requirements
                if ($request->filled('requirements')) {
                    $requirements = $request->input('requirements');
                    foreach (['minimum', 'recommended'] as $type) {
                        if (isset($requirements[$type]) && is_array($requirements[$type])) {
                            foreach ($requirements[$type] as $platformId => $reqText) {
                                if (!empty($reqText)) {
                                    $model->systemRequirements()->updateOrCreate(
                                        [
                                            'type' => $type,
                                            'game_platform_id' => $platformId,
                                        ],
                                        [
                                            'requirements' => $reqText,
                                        ]
                                    );
                                } else {
                                    // Delete if field is empty
                                    $model->systemRequirements()
                                        ->where('type', $type)
                                        ->where('game_platform_id', $platformId)
                                        ->delete();
                                }
                            }
                        }
                    }
                } else {
                    // Delete if field is empty
                    $model->systemRequirements()->delete();
                }

                if (!$model->wasChanged()) {
                    $model->touch();
                }

                return $model;
            }
        );

        return $this->success([
            'redirect' => action([static::class, 'index']),
            'message' => __('game-store::translation.game_name_updated_successfully', ['name' => $model->title]),
        ]);
    }

    public function bulk(GameActionsRequest $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        $models = Game::whereIn('id', $ids)->get();

        foreach ($models as $model) {
            if ($action === 'activate') {
                $model->update(['status' => GameStatus::PUBLISHED]);
            }

            if ($action === 'deactivate') {
                $model->update(['status' => GameStatus::DRAFT]);
            }

            if ($action === 'delete') {
                $model->delete();
            }
        }

        return $this->success([
            'message' => __('game-store::translation.bulk_action_performed_successfully'),
        ]);
    }

    /**
     * Search games from IGDB API
     */
    public function searchIgdb(Request $request)
    {
        if (!setting('igdb_client_id') || !setting('igdb_client_secret')) {
            return $this->error([
                'message' => __('game-store::translation.igdb_not_configured'),
            ]);
        }

        config([
            'igdb.credentials.client_id' => setting('igdb_client_id'),
            'igdb.credentials.client_secret' => setting('igdb_client_secret'),
        ]);

        $request->validate([
            'query' => 'required|string|min:2',
        ]);

        try {
            $games = IGDBGame::search($request->input('query'))
                ->cache(3600)
                ->select(['id', 'name', 'summary'])
                ->with(['cover'])
                ->limit(10)
                ->get();

            return $this->success([
                'data' => $games->map(function ($game) {
                    return [
                        'id' => $game->id,
                        'name' => $game->name,
                        'summary' => $game->summary ?? '',
                        'cover_url' => $game->cover->url ?? null,
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            return $this->error([
                'message' => __('game-store::translation.igdb_search_failed', ['error' => $e->getMessage()]),
            ]);
        }
    }

    /**
     * Import game from IGDB
     */
    public function importFromIgdb(IgdbImportRequest $request)
    {
        if (!setting('igdb_client_id') || !setting('igdb_client_secret')) {
            return $this->error([
                'message' => __('game-store::translation.igdb_not_configured'),
            ]);
        }

        config([
            'igdb.credentials.client_id' => setting('igdb_client_id'),
            'igdb.credentials.client_secret' => setting('igdb_client_secret'),
        ]);

        // Fetch detailed game info from IGDB
        $igdbGame = IGDBGame::where('id', (int) $request->input('igdb_id'))
            ->select([
                'id',
                'name',
                'summary',
            ])
            ->with([
                'cover',
                'screenshots',
                'involved_companies.company',
                'platforms',
                'language_supports.language',
                'genres',
            ])
            ->first();

        if (!$igdbGame) {
            return $this->error([
                'message' => __('game-store::translation.game_not_found_on_igdb'),
            ]);
        }

        $thumbnail = null;
        // Handle cover image
        if (isset($igdbGame->cover->url)) {
            $coverUrl = str_replace('t_thumb', 't_screenshot_med', $igdbGame->cover->url);
            $coverUrl = 'https:' . $coverUrl;

            try {
                $media = MediaUploader::make($coverUrl)->upload();
                $thumbnail = $media;
            } catch (\Exception $e) {
                Log::warning('Failed to download cover image: ' . $e->getMessage());
            }
        }

        $screenshots = [];
        if (isset($igdbGame->screenshots)) {
            foreach ($igdbGame->screenshots->take(10) as $screenshot) {
                if (!isset($screenshot->url)) {
                    continue;
                }

                $screenshotUrl = str_replace('t_thumb', 't_screenshot_big', $screenshot->url);
                $screenshotUrl = 'https:' . $screenshotUrl;

                try {
                    $media = MediaUploader::make($screenshotUrl)->upload();
                    $screenshots[] = $media;
                } catch (\Exception $e) {
                    Log::warning('Failed to download screenshot: ' . $e->getMessage());
                }
            }
        }

        $locale = $this->getFormLanguage();

        // Create the game
        $game = DB::transaction(
            function () use ($igdbGame, $locale, $thumbnail, $screenshots) {
                $game = new Game();
                $game->setDefaultLocale($locale);
                $game->title = $igdbGame->name;
                $game->content = $igdbGame->summary ?? '';
                $game->status = GameStatus::DRAFT;
                $game->save();

                if ($thumbnail) {
                    $game->setThumbnail($thumbnail);
                }

                if ($screenshots) {
                    $game->attachMedia($screenshots, 'screenshots');
                }

                if (isset($igdbGame->genres)) {
                    $genreNames = $igdbGame->genres
                        ->pluck('name')
                        ->filter()
                        ->unique()
                        ->take(5);

                    // Sync categories (genres)
                    $categoryIds = [];
                    foreach ($genreNames as $genreName) {
                        $category = GameCategory::whereTranslation('name', $genreName)->first();
                        if (!$category) {
                            // Create new category
                            $category = new GameCategory();
                            $category->setDefaultLocale($locale);
                            $category->name = $genreName;
                            $category->slug = Str::slug($genreName);
                            $category->save();
                        }

                        $categoryIds[] = $category->id;
                    }

                    $game->categories()->sync($categoryIds);
                }

                // Handle vendors/developers
                if (isset($igdbGame->involved_companies)) {
                    $vendorNames = $igdbGame->involved_companies
                        ->pluck('company.name')
                        ->filter()
                        ->unique()
                        ->take(5);

                    $this->syncVendors($game, $vendorNames->toArray());
                }

                // Handle platforms
                if (isset($igdbGame->platforms)) {
                    $platformNames = $igdbGame->platforms
                        ->pluck('name')
                        ->filter()
                        ->unique()
                        ->take(10);

                    $this->syncPlatforms($game, $platformNames->toArray());
                }

                // Handle languages
                if (isset($igdbGame->language_supports)) {
                    $languageNames = $igdbGame->language_supports
                        ->pluck('language.name')
                        ->filter()
                        ->unique()
                        ->take(10);

                    $this->syncLanguages($game, $languageNames->toArray());
                }

                return $game;
            }
        );

        return $this->success([
            'redirect' => action([static::class, 'edit'], [$game->id]),
            'message' => __('game-store::translation.game_imported_successfully', ['name' => $game->title]),
        ]);
    }

    /**
     * Sync vendors for the game, creating new ones if needed.
     */
    protected function syncVendors(Game $game, array $vendors = []): void
    {
        $locale = $this->getFormLanguage();
        $vendorIds = collect($vendors)->map(
            function ($vendor) use ($locale) {
                if (Str::isUuid($vendor)) {
                    return $vendor;
                }

                $newVendor = GameVendor::whereTranslation('name', $vendor)->first();
                if ($newVendor) {
                    return $newVendor->id;
                }

                // Create new vendor with the name
                $newVendor = new GameVendor();
                $newVendor->setDefaultLocale($locale);
                $newVendor->name = $vendor;
                $newVendor->save();

                return $newVendor->id;
            }
        );

        $game->vendors()->sync($vendorIds);
    }

    /**
     * Sync platforms for the game, creating new ones if needed.
     */
    protected function syncPlatforms(Game $game, array $platforms = []): void
    {
        $platformIds = collect($platforms)->map(
            function ($platform) {
                if (Str::isUuid($platform)) {
                    return $platform;
                }

                $newPlatform = GamePlatform::firstOrCreate(
                    [
                        'name' => $platform,
                    ]
                );

                return $newPlatform->id;
            }
        );

        $game->platforms()->sync($platformIds);
    }

    /**
     * Sync languages for the game, creating new ones if needed.
     */
    protected function syncLanguages(Game $game, array $languages = []): void
    {
        $locale = $this->getFormLanguage();
        $languageIds = collect($languages)->map(
            function ($language) use ($locale) {
                if (Str::isUuid($language)) {
                    return $language;
                }

                $newLanguage = GameLanguage::whereTranslation('name', $language)->first();
                if ($newLanguage) {
                    return $newLanguage->id;
                }

                $newLanguage = new GameLanguage();
                $newLanguage->setDefaultLocale($locale);
                $newLanguage->name = $language;
                $newLanguage->save();

                return $newLanguage->id;
            }
        );

        $game->languages()->sync($languageIds);
    }
}
