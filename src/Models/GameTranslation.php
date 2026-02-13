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

use Illuminate\Database\Eloquent\Builder;
use Juzaweb\Modules\Core\Contracts\Sitemapable;
use Juzaweb\Modules\Core\Models\Model;
use Juzaweb\Modules\Core\Traits\HasSitemap;
use Juzaweb\Modules\Core\Traits\HasSlug;
use Juzaweb\Modules\GameStore\Enums\GameStatus;

class GameTranslation extends Model implements Sitemapable
{
    use HasSlug, HasSitemap;

    protected $table = 'game_translations';

    protected $fillable = [
        'game_id',
        'locale',
        'title',
        'content',
        'slug',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id', 'id');
    }

    public function scopeForSitemap(Builder $builder): Builder
    {
        return $builder
            ->join('games', 'game_translations.game_id', '=', 'games.id')
            ->where('games.status', GameStatus::PUBLISHED->value)
            ->select(['game_translations.*'])
            ->cacheDriver('file')
            ->cacheFor(3600 * 24)
            ->orderBy('updated_at', 'desc');
    }

    public function getUrl(): string
    {
        if ($this->locale != setting('language')) {
            return home_url("{$this->locale}/game/{$this->slug}");
        }

        return home_url("game/{$this->slug}");
    }
}
