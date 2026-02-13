<?php

namespace Juzaweb\Modules\GameStore\Http\Controllers\Admin;

use Juzaweb\Modules\Core\Facades\Breadcrumb;
use Juzaweb\Modules\Core\Http\Controllers\Admin\CommentController as BaseCommentController;
use Juzaweb\Modules\Core\Http\DataTables\CommentsDataTable as BaseCommentsDataTable;
use Juzaweb\Modules\GameStore\Http\DataTables\CommentsDataTable;
use Juzaweb\Modules\GameStore\Models\Game;

class CommentController extends BaseCommentController
{
    protected string $commentableType = Game::class;

    public function index(BaseCommentsDataTable $dataTable)
    {
        Breadcrumb::add(__('core::translation.comments'));

        $dataTable = app(CommentsDataTable::class);

        return $dataTable->forCommentableType($this->commentableType)->render(
            'core::comment.index',
            []
        );
    }
}
