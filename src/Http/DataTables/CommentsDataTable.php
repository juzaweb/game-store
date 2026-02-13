<?php

namespace Juzaweb\Modules\GameStore\Http\DataTables;

use Juzaweb\Modules\Core\Http\DataTables\CommentsDataTable as BaseCommentsDataTable;

class CommentsDataTable extends BaseCommentsDataTable
{
    protected string $actionUrl = 'game-comments/bulk';
}
