<?php

namespace Juzaweb\Modules\GameStore\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Juzaweb\Modules\Core\Facades\Breadcrumb;
use Juzaweb\Modules\Core\Http\Controllers\AdminController;
use Juzaweb\Modules\GameStore\Http\DataTables\GameVendorsDataTable;
use Juzaweb\Modules\GameStore\Http\Requests\GameVendorActionsRequest;
use Juzaweb\Modules\GameStore\Http\Requests\GameVendorRequest;
use Juzaweb\Modules\GameStore\Models\GameVendor;

class GameVendorController extends AdminController
{
    public function index(GameVendorsDataTable $dataTable)
    {
        Breadcrumb::add(__('game-store::translation.game_vendors'));

        $createUrl = action([static::class, 'create']);

        return $dataTable->render(
            'game-store::game-vendor.index',
            compact('createUrl')
        );
    }

    public function create()
    {
        Breadcrumb::add(__('game-store::translation.game_vendors'), admin_url('gamevendors'));

        Breadcrumb::add(__('game-store::translation.create_game_vendor'));

        $backUrl = action([static::class, 'index']);

        return view(
            'game-store::game-vendor.form',
            [
                'model' => new GameVendor(),
                'action' => action([static::class, 'store']),
                'backUrl' => $backUrl,
            ]
        );
    }

    public function edit(string $id)
    {
        Breadcrumb::add(__('game-store::translation.game_vendors'), admin_url('gamevendors'));

        Breadcrumb::add(__('game-store::translation.create_game_vendors'));

        $model = GameVendor::findOrFail($id);
        $backUrl = action([static::class, 'index']);

        return view(
            'game-store::game-vendor.form',
            [
                'action' => action([static::class, 'update'], [$id]),
                'model' => $model,
                'backUrl' => $backUrl,
            ]
        );
    }

    public function store(GameVendorRequest $request)
    {
        $model = DB::transaction(
            function () use ($request) {
                $data = $request->validated();

                return GameVendor::create($data);
            }
        );

        return $this->success([
            'redirect' => action([static::class, 'index']),
            'message' => __('game-store::translation.gamevendor_name_created_successfully', ['name' => $model->name]),
        ]);
    }

    public function update(GameVendorRequest $request, string $id)
    {
        $model = GameVendor::findOrFail($id);

        $model = DB::transaction(
            function () use ($request, $model) {
                $data = $request->validated();

                $model->update($data);

                return $model;
            }
        );

        return $this->success([
            'redirect' => action([static::class, 'index']),
            'message' => __('game-store::translation.gamevendor_name_updated_successfully', ['name' => $model->name]),
        ]);
    }

    public function bulk(GameVendorActionsRequest $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        $models = GameVendor::whereIn('id', $ids)->get();

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
