<?php

namespace Juzaweb\Modules\GameStore\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Juzaweb\Modules\Core\Facades\Breadcrumb;
use Juzaweb\Modules\Core\Http\Controllers\AdminController;
use Juzaweb\Modules\GameStore\Http\DataTables\GamePlatformsDataTable;
use Juzaweb\Modules\GameStore\Http\Requests\GamePlatformActionsRequest;
use Juzaweb\Modules\GameStore\Http\Requests\GamePlatformRequest;
use Juzaweb\Modules\GameStore\Models\GamePlatform;

class GamePlatformController extends AdminController
{
    public function index(GamePlatformsDataTable $dataTable)
    {
        Breadcrumb::add(__('game-store::translation.game_platforms'));

        $createUrl = action([static::class, 'create']);

        return $dataTable->render(
            'game-store::game-platform.index',
            compact('createUrl')
        );
    }

    public function create()
    {
        Breadcrumb::add(__('game-store::translation.game_platforms'), admin_url('gameplatforms'));

        Breadcrumb::add(__('game-store::translation.create_game_platform'));

        $backUrl = action([static::class, 'index']);

        return view(
            'game-store::game-platform.form',
            [
                'model' => new GamePlatform(),
                'action' => action([static::class, 'store']),
                'backUrl' => $backUrl,
            ]
        );
    }

    public function edit(string $id)
    {
        Breadcrumb::add(__('game-store::translation.game_platforms'), admin_url('gameplatforms'));

        Breadcrumb::add(__('game-store::translation.create_game_platforms'));

        $model = GamePlatform::findOrFail($id);
        $backUrl = action([static::class, 'index']);

        return view(
            'game-store::game-platform.form',
            [
                'action' => action([static::class, 'update'], [$id]),
                'model' => $model,
                'backUrl' => $backUrl,
            ]
        );
    }

    public function store(GamePlatformRequest $request)
    {
        $model = DB::transaction(
            function () use ($request) {
                $data = $request->validated();

                return GamePlatform::create($data);
            }
        );

        return $this->success([
            'redirect' => action([static::class, 'index']),
            'message' => __('game-store::translation.gameplatform_name_created_successfully', ['name' => $model->name]),
        ]);
    }

    public function update(GamePlatformRequest $request, string $id)
    {
        $model = GamePlatform::findOrFail($id);

        $model = DB::transaction(
            function () use ($request, $model) {
                $data = $request->validated();

                $model->update($data);

                return $model;
            }
        );

        return $this->success([
            'redirect' => action([static::class, 'index']),
            'message' => __('game-store::translation.gameplatform_name_updated_successfully', ['name' => $model->name]),
        ]);
    }

    public function bulk(GamePlatformActionsRequest $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        $models = GamePlatform::whereIn('id', $ids)->get();

        foreach ($models as $model) {
            if ($action === 'activate') {
                $model->update(['active' => true]);
            }

            if ($action === 'deactivate') {
                $model->update(['active' => false]);
            }

            if ($action === 'delete') {
                $model->delete();
            }
        }

        return $this->success([
            'message' => __('game-store::translation.bulk_action_performed_successfully'),
        ]);
    }
}
