<?php

namespace Juzaweb\Modules\GameStore\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Juzaweb\Modules\Core\Models\Model;
use Juzaweb\Modules\Core\Support\Traits\MenuBoxable;
use Juzaweb\Modules\Core\Traits\HasSlug;
use Juzaweb\Modules\Core\Traits\UsedInFrontend;
use Juzaweb\Modules\Core\Translations\Contracts\Translatable as TranslatableContract;
use Juzaweb\Modules\Core\Translations\Traits\Translatable;

class GameLanguage extends Model implements TranslatableContract
{
    use Translatable,  HasUuids, UsedInFrontend, HasSlug, MenuBoxable;

    protected $table = 'game_languages';

    protected $fillable = [
        'slug',
    ];

    public $translatedAttributes = [
        'name',
        'locale',
    ];

    public function getUrl(): string
    {
        return home_url("game/language/{$this->slug}", $this->locale);
    }

    public function scopeWhereInMenuBox(Builder $builder): Builder
    {
        return $builder->withTranslation();
    }

    public function getEditUrl(): string
    {
        return route('admin.game-languages.edit', [$this->id]);
    }
}
