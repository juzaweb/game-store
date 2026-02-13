<?php

namespace Juzaweb\Modules\GameStore\Http\DataTables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Juzaweb\Modules\Core\DataTables\Action;
use Juzaweb\Modules\Core\DataTables\BulkAction;
use Juzaweb\Modules\Core\DataTables\Column;
use Juzaweb\Modules\Core\DataTables\DataTable;
use Juzaweb\Modules\GameStore\Models\Game;
use Yajra\DataTables\EloquentDataTable;

class GamesDatatable extends DataTable
{
    protected string $actionUrl = 'games/bulk';

    public function query(Game $model): Builder
    {
        return $model->newQuery()
            ->withTranslation()
            ->with([
                'categories' => fn ($q) => $q->withTranslation(),
                'media',
            ])
            ->withTranslation();
    }

    public function getColumns(): array
    {
        return [
			Column::checkbox(),
			Column::id(),
			Column::actions(),
            Column::computed('thumbnail')
                ->title(__('game-store::translation.thumbnail'))
                ->width(80),
            Column::editLink('title', 'games/{id}/edit', __('game-store::translation.title')),
			Column::computed('categories')->title(__('game-store::translation.categories')),
			Column::make('views'),
			Column::computed('status'),
			Column::createdAt()
		];
    }

    public function actions(Model $model): array
    {
        return [
            Action::edit("games/{$model->id}/edit")->can('games.edit'),

            Action::delete()->can('games.delete'),
        ];
    }

    public function bulkActions(): array
    {
        return [
            BulkAction::delete()->can('games.delete'),
        ];
    }

    public function renderColumns(EloquentDataTable $builder): EloquentDataTable
    {
        return parent::renderColumns($builder)
            ->editColumn('thumbnail', function (Game $model) {
                $thumb = $model->thumbnail;
                return '<img src="'.$thumb.'" alt="" class="img-fluid w-100" />';
            })
            ->editColumn('categories', function (Game $model) {
                return $model->categories->pluck('name')->implode(', ');
            })
            ->editColumn('status', function (Game $model) {
                return $model->status->label();
            })
            ->rawColumns(['thumbnail']);
    }
}
