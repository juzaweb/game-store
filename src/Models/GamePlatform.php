<?php

namespace Juzaweb\Modules\GameStore\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Juzaweb\Modules\Core\Models\Model;
use Juzaweb\Modules\Core\Support\Traits\MenuBoxable;
use Juzaweb\Modules\Core\Traits\HasSlug;
use Juzaweb\Modules\Core\Traits\UsedInFrontend;
use Juzaweb\Modules\GameStore\Database\Factories\GamePlatformFactory;

class GamePlatform extends Model
{
    use HasUuids, UsedInFrontend, HasFactory, HasSlug,  MenuBoxable;

    protected $table = 'game_platforms';

    protected $fillable = [
        'name',
        'slug',
    ];

    public function getUrl(): string
    {
        return home_url("game/platform/{$this->slug}", $this->locale);
    }

    public function scopeWhereInMenuBox(Builder $builder): Builder
    {
        return $builder;
    }

    public function getEditUrl(): string
    {
        return route('admin.game-platforms.edit', [$this->id]);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): GamePlatformFactory
    {
        return GamePlatformFactory::new();
    }
}
