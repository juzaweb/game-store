<?php

namespace Juzaweb\Modules\GameStore\Http\DataTables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Juzaweb\Modules\Core\DataTables\Action;
use Juzaweb\Modules\Core\DataTables\BulkAction;
use Juzaweb\Modules\Core\DataTables\Column;
use Juzaweb\Modules\Core\DataTables\DataTable;
use Juzaweb\Modules\GameStore\Models\GameCategory;
use Yajra\DataTables\EloquentDataTable;

class GameCategoriesDatatable extends DataTable
{
    protected string $actionUrl = 'game-categories/bulk';

    public function query(GameCategory $model): Builder
    {
        return $model->newQuery()
            ->with(['parent'])
            ->withTranslation();
    }

    public function getColumns(): array
    {
        return [
            Column::checkbox(),
            Column::id(),
            Column::actions(),
            Column::make('name')->title(__('game-store::translation.name')),
            Column::computed('parent_id')->title(__('game-store::translation.parent_category')),
            Column::createdAt()
        ];
    }

    public function actions(Model $model): array
    {
        return [
            Action::edit("game-categories/{$model->id}/edit")->can('game-categories.edit'),

            Action::delete()->can('game-categories.delete'),
        ];
    }

    public function bulkActions(): array
    {
        return [
            BulkAction::delete()->can('game-categories.delete'),
            BulkAction::make(__('core::translation.translate'), null, 'fas fa-language')
                ->type('url')
                ->action('translate')
                ->can('game-categories.edit'),
        ];
    }

    public function renderColumns(EloquentDataTable $builder): EloquentDataTable
    {
        return parent::renderColumns($builder)
            ->editColumn('parent_id', function (GameCategory $model) {
                return $model->parent ? $model->parent->name : '-';
            });
    }
}
