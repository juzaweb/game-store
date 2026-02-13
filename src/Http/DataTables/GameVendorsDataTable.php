<?php

namespace Juzaweb\Modules\GameStore\Http\DataTables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Juzaweb\Modules\Core\DataTables\Action;
use Juzaweb\Modules\Core\DataTables\BulkAction;
use Juzaweb\Modules\Core\DataTables\Column;
use Juzaweb\Modules\Core\DataTables\DataTable;
use Juzaweb\Modules\GameStore\Models\GameVendor;

class GameVendorsDataTable extends DataTable
{
    protected string $actionUrl = 'game-vendors/bulk';

    public function query(GameVendor $model): Builder
    {
        return $model->newQuery()->withTranslation();
    }

    public function getColumns(): array
    {
        return [
            Column::checkbox(),
            Column::id(),
            Column::actions(),
            Column::editLink('name', admin_url('game-vendors/{id}/edit'), __('core::translation.label')),
            Column::make('slug'),
            Column::createdAt()
        ];
    }

    public function actions(Model $model): array
    {
        return [
            Action::edit(admin_url("game-vendors/{$model->id}/edit"))->can('game-vendors.edit'),
            Action::delete()->can('game-vendors.delete'),
        ];
    }

    public function bulkActions(): array
    {
        return [
            BulkAction::delete()->can('game-vendors.delete'),
            BulkAction::make(__('core::translation.translate'), null, 'fas fa-language')
                ->type('url')
                ->action('translate')
                ->can('game-vendors.edit'),
        ];
    }
}
