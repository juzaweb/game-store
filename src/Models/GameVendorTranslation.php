<?php

namespace Juzaweb\Modules\GameStore\Models;

use Juzaweb\Modules\Core\Models\Model;
use Juzaweb\Modules\Core\Traits\HasSlug;

class GameVendorTranslation extends Model
{
    use HasSlug;

    protected $table = 'game_vendor_translations';

    protected $fillable = [
        'game_vendor_id',
        'locale',
        'name',
        'slug',
    ];
}
