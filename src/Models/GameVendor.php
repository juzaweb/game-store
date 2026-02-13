<?php

namespace Juzaweb\Modules\GameStore\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Juzaweb\Modules\Core\Models\Model;
use Juzaweb\Modules\Core\Support\Traits\MenuBoxable;
use Juzaweb\Modules\Core\Traits\UsedInFrontend;
use Juzaweb\Modules\Core\Translations\Contracts\Translatable as TranslatableContract;
use Juzaweb\Modules\Core\Translations\Traits\Translatable;
use Juzaweb\Modules\GameStore\Database\Factories\GameVendorFactory;

class GameVendor extends Model implements TranslatableContract
{
    use Translatable,  HasUuids, UsedInFrontend, HasFactory, MenuBoxable;

    protected $table = 'game_vendors';

    protected $fillable = [];

    public $translatedAttributes = [
        'name',
        'slug',
        'locale',
    ];

    protected $casts = [];

    public function getUrl(): string
    {
        return home_url("game/vendor/{$this->slug}", $this->locale);
    }

    public function scopeWhereInMenuBox(Builder $builder): Builder
    {
        return $builder->withTranslation();
    }

    public function getEditUrl(): string
    {
        return route('admin.game-vendors.edit', [$this->id]);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): GameVendorFactory
    {
        return GameVendorFactory::new();
    }
}
