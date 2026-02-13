<?php
/**
 * JUZAWEB CMS - Laravel CMS for Your Project
 *
 * @package    juzaweb/cms
 * @author     The Anh Dang
 * @link       https://cms.juzaweb.com
 * @license    GNU V2
 */

namespace Juzaweb\Modules\GameStore\Models;

use Juzaweb\Modules\Core\Models\Model;
use Juzaweb\Modules\Core\Traits\HasSlug;

class GameCategoryTranslation extends Model
{
    use HasSlug;

    protected $table = 'game_category_translations';

    protected $fillable = [
        'game_category_id',
        'locale',
        'name',
        'slug',
    ];
}
