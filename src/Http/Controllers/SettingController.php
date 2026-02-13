<?php

namespace Juzaweb\Modules\GameStore\Http\Controllers;

use Juzaweb\Modules\Core\Facades\Breadcrumb;
use Juzaweb\Modules\Core\Facades\Setting;
use Juzaweb\Modules\Core\Http\Controllers\AdminController;
use Juzaweb\Modules\GameStore\Http\Requests\SettingRequest;

class SettingController extends AdminController
{
    public function index()
    {
        Breadcrumb::add(__('game-store::translation.settings'));

        return view('game-store::setting.index');
    }

    public function update(SettingRequest $request)
    {
        Setting::sets($request->validated());

        return $this->success(
            [
                'message' => __('game-store::translation.settings_updated_successfully'),
                'redirect' => action([self::class, 'index']),
            ]
        );
    }
}
