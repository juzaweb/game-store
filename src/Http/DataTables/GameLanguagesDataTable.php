<?php

namespace Juzaweb\Modules\GameStore\Http\DataTables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Juzaweb\Modules\Core\DataTables\Action;
use Juzaweb\Modules\Core\DataTables\BulkAction;
use Juzaweb\Modules\Core\DataTables\Column;
use Juzaweb\Modules\Core\DataTables\DataTable;
use Juzaweb\Modules\GameStore\Models\GameLanguage;

class GameLanguagesDataTable extends DataTable
{
    protected string $actionUrl = 'game-languages/bulk';

    public function query(GameLanguage $model): Builder
    {
        return $model->newQuery()->withTranslation();
    }

    public function getColumns(): array
    {
        return [
            Column::checkbox(),
            Column::id(),
            Column::actions(),
            Column::editLink('name', admin_url('game-languages/{id}/edit'), __('core::translation.label')),
            Column::createdAt(),
        ];
    }

    public function actions(Model $model): array
    {
        return [
            Action::edit(admin_url("game-languages/{$model->id}/edit"))->can('game-languages.edit'),
            Action::delete()->can('game-languages.delete'),
        ];
    }

    public function bulkActions(): array
    {
        return [
            BulkAction::delete()->can('game-languages.delete'),
            BulkAction::make(__('core::translation.translate'), null, 'fas fa-language')
                ->type('url')
                ->action('translate')
                ->can('game-languages.edit'),
        ];
    }
}
