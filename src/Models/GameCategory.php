<?php

namespace Juzaweb\Modules\GameStore\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Juzaweb\Modules\Core\Models\Model;
use Juzaweb\Modules\Core\Support\Traits\MenuBoxable;
use Juzaweb\Modules\Core\Traits\UsedInFrontend;
use Juzaweb\Modules\Core\Translations\Contracts\Translatable as TranslatableContract;
use Juzaweb\Modules\Core\Translations\Traits\Translatable;
use Juzaweb\Modules\GameStore\Database\Factories\GameCategoryFactory;

class GameCategory extends Model implements TranslatableContract
{
    use Translatable,  HasUuids, UsedInFrontend, HasFactory, MenuBoxable;

    protected $table = 'game_categories';

    protected $fillable = [
        'parent_id',
    ];

    public $translatedAttributes = [
        'name',
        'slug',
        'locale',
    ];

    protected $casts = [];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'parent_id', 'id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(__CLASS__, 'parent_id', 'id');
    }

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_category', 'game_category_id', 'game_id');
    }

    public function getUrl(): string
    {
        return home_url("game/category/{$this->slug}", $this->locale);
    }

    public function scopeWhereInMenuBox(Builder $builder): Builder
    {
        return $builder->withTranslation();
    }

    public function getEditUrl(): string
    {
        return route('admin.game-categories.edit', [$this->id]);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return GameCategoryFactory::new();
    }
}
