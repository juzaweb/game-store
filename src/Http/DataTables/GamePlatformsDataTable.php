<?php

namespace Juzaweb\Modules\GameStore\Http\DataTables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Juzaweb\Modules\Core\DataTables\Action;
use Juzaweb\Modules\Core\DataTables\BulkAction;
use Juzaweb\Modules\Core\DataTables\Column;
use Juzaweb\Modules\Core\DataTables\DataTable;
use Juzaweb\Modules\GameStore\Models\GamePlatform;

class GamePlatformsDataTable extends DataTable
{
    protected string $actionUrl = 'game-platforms/bulk';

    public function query(GamePlatform $model): Builder
    {
        return $model->newQuery();
    }

    public function getColumns(): array
    {
        return [
			Column::checkbox(),
			Column::id(),
			Column::actions(),
			Column::editLink('name', admin_url('game-platforms/{id}/edit'), __('core::translation.label')),
			Column::createdAt()
		];
    }

    public function actions(Model $model): array
    {
        return [
            Action::edit(admin_url("game-platforms/{$model->id}/edit"))->can('game-platforms.edit'),
            Action::delete()->can('game-platforms.delete'),
        ];
    }

    public function bulkActions(): array
    {
        return [
            BulkAction::delete()->can('game-platforms.delete'),
        ];
    }
}
