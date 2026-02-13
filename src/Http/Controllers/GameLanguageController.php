<?php

namespace Juzaweb\Modules\GameStore\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Juzaweb\Modules\Core\Facades\Breadcrumb;
use Juzaweb\Modules\Core\Http\Controllers\AdminController;
use Juzaweb\Modules\GameStore\Http\DataTables\GameLanguagesDataTable;
use Juzaweb\Modules\GameStore\Http\Requests\GameLanguageActionsRequest;
use Juzaweb\Modules\GameStore\Http\Requests\GameLanguageRequest;
use Juzaweb\Modules\GameStore\Models\GameLanguage;

class GameLanguageController extends AdminController
{
    public function index(GameLanguagesDataTable $dataTable)
    {
        Breadcrumb::add(__('game-store::translation.game_languages'));

        $createUrl = action([static::class, 'create']);

        return $dataTable->render(
            'game-store::game-language.index',
            compact('createUrl')
        );
    }

    public function create()
    {
        Breadcrumb::add(__('game-store::translation.game_languages'), admin_url('gamelanguages'));

        Breadcrumb::add(__('game-store::translation.create_game_language'));

        $backUrl = action([static::class, 'index']);

        return view(
            'game-store::game-language.form',
            [
                'model' => new GameLanguage(),
                'action' => action([static::class, 'store']),
                'backUrl' => $backUrl,
            ]
        );
    }

    public function edit(string $id)
    {
        Breadcrumb::add(__('game-store::translation.game_languages'), admin_url('gamelanguages'));

        Breadcrumb::add(__('game-store::translation.create_game_languages'));

        $model = GameLanguage::findOrFail($id);
        $backUrl = action([static::class, 'index']);

        return view(
            'game-store::game-language.form',
            [
                'action' => action([static::class, 'update'], [$id]),
                'model' => $model,
                'backUrl' => $backUrl,
            ]
        );
    }

    public function store(GameLanguageRequest $request)
    {
        $model = DB::transaction(
            function () use ($request) {
                $data = $request->validated();

                return GameLanguage::create($data);
            }
        );

        return $this->success([
            'redirect' => action([static::class, 'index']),
            'message' => __('game-store::translation.gamelanguage_name_created_successfully', ['name' => $model->name]),
        ]);
    }

    public function update(GameLanguageRequest $request, string $id)
    {
        $model = GameLanguage::findOrFail($id);

        $model = DB::transaction(
            function () use ($request, $model) {
                $data = $request->validated();

                $model->update($data);

                return $model;
            }
        );

        return $this->success([
            'redirect' => action([static::class, 'index']),
            'message' => __('game-store::translation.gamelanguage_name_updated_successfully', ['name' => $model->name]),
        ]);
    }

    public function bulk(GameLanguageActionsRequest $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        $models = GameLanguage::whereIn('id', $ids)->get();

        foreach ($models as $model) {
            if ($action === 'delete') {
                $model->delete();
            }
        }

        return $this->success([
            'message' => __('game-store::translation.bulk_action_performed_successfully'),
        ]);
    }
}
