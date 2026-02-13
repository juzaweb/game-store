<?php

namespace Juzaweb\Modules\GameStore\Models;

use Juzaweb\Modules\Core\Models\Model;

class GameLanguageTranslation extends Model
{
    protected $table = 'game_language_translations';

    protected $fillable = [
        'game_language_id',
        'locale',
        'name',
        'slug',
    ];
}
