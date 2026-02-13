<?php

namespace Juzaweb\Modules\GameStore\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Juzaweb\Modules\Core\Facades\Breadcrumb;
use Juzaweb\Modules\Core\Http\Controllers\AdminController;
use Juzaweb\Modules\GameStore\Http\DataTables\GameCategoriesDatatable;
use Juzaweb\Modules\GameStore\Http\Requests\GameCategoryRequest;
use Juzaweb\Modules\GameStore\Models\GameCategory;

class GameCategoryController extends AdminController
{
    public function index(GameCategoriesDatatable $dataTable)
    {
        Breadcrumb::add(__('game-store::translation.game_categories'));

        $createUrl = action([self::class, 'create']);

        return $dataTable->render('game-store::category.index', compact('createUrl'));
    }

    public function create()
    {
        Breadcrumb::add(__('game-store::translation.game_categories'), action([self::class, 'index']));

        Breadcrumb::add(__('game-store::translation.create_new_game_category'));

        $categories = GameCategory::withTranslation()
            ->with(['children'])
            ->whereNull('parent_id')
            ->get();
        $locale = $this->getFormLanguage();

        $parentCategories = [];
        $this->mapCategories($categories, $parentCategories);

        return view(
            'game-store::category.form',
            [
                'model' => new GameCategory(),
                'action' => action([self::class, 'store']),
                'parentCategories' => $parentCategories,
                'locale' => $locale,
            ]
        );
    }

    public function edit(string $id)
    {
        $locale = $this->getFormLanguage();
        $model = GameCategory::findOrFail($id);
        $model->setDefaultLocale($locale);

        Breadcrumb::add(__('game-store::translation.game_categories'), action([self::class, 'index']));

        Breadcrumb::add(__('game-store::translation.edit_game_category_name', ['name' => $model->name]));

        $categories = GameCategory::withTranslation()
            ->with(['children'])
            ->whereNull('parent_id')
            ->where('id', '!=', $id)
            ->get();

        $parentCategories = [];
        $this->mapCategories($categories, $parentCategories, '', $id);

        return view(
            'game-store::category.form',
            [
                'model' => $model,
                'action' => action([self::class, 'update'], [$id]),
                'parentCategories' => $parentCategories,
                'locale' => $locale,
            ]
        );
    }

    public function store(GameCategoryRequest $request)
    {
        $data = $request->validated();
        $locale = $this->getFormLanguage();

        $category = DB::transaction(
            function () use ($data, $locale) {
                $category = new GameCategory();
                $category->fill($data);
                $category->setDefaultLocale($locale);
                $category->save();

                return $category;
            }
        );

        return $this->success(
            [
                'message' => __('game-store::translation.game_category_created_successfully'),
                'redirect' => action([self::class, 'index']),
                'data' => [
                    'id' => $category->id,
                    'name' => $category->name,
                ],
            ]
        );
    }

    public function update(GameCategoryRequest $request, string $id)
    {
        $data = $request->validated();
        $category = GameCategory::findOrFail($id);
        $locale = $this->getFormLanguage();

        if (isset($data['parent_id']) && $data['parent_id'] == $id) {
            unset($data['parent_id']);
        }

        DB::transaction(
            function () use ($category, $data, $locale) {
                $category->setDefaultLocale($locale);
                $category->fill($data);
                $category->save();
            }
        );

        return $this->success(
            [
                'message' => __('game-store::translation.game_category_updated_successfully'),
                'redirect' => action([self::class, 'index']),
            ]
        );
    }

    public function bulk(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if ($action == 'delete') {
            GameCategory::whereIn('id', $ids)
                ->get()
                ->each
                ->delete();
        }

        return $this->success(
            [
                'message' => __('game-store::translation.game_category_updated_successfully'),
            ]
        );
    }

    protected function mapCategories($categories, &$result, $prefix = '', $excludeId = null)
    {
        foreach ($categories as $category) {
            if ($excludeId && $category->id == $excludeId) {
                continue;
            }

            $result[$category->id] = $prefix . ' ' . $category->name;

            if ($category->children && $category->children->isNotEmpty()) {
                $this->mapCategories($category->children, $result, $prefix . '--', $excludeId);
            }
        }
    }
}
