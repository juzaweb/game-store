<?php

namespace Juzaweb\Modules\GameStore\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Juzaweb\Modules\Core\Contracts\Viewable;
use Juzaweb\Modules\Core\FileManager\Traits\HasMedia;
use Juzaweb\Modules\Core\Models\Model;
use Juzaweb\Modules\Core\Support\Traits\HasComments;
use Juzaweb\Modules\Core\Traits\HasCode;
use Juzaweb\Modules\Core\Traits\HasContent;
use Juzaweb\Modules\Core\Traits\HasFrontendUrl;
use Juzaweb\Modules\Core\Traits\HasThumbnail;
use Juzaweb\Modules\Core\Traits\HasViews;
use Juzaweb\Modules\Core\Traits\Translatable;
use Juzaweb\Modules\Core\Traits\UsedInFrontend;
use Juzaweb\Modules\GameStore\Database\Factories\GameFactory;
use Juzaweb\Modules\GameStore\Enums\GameStatus;

class Game extends Model implements Viewable
{
    use HasCode,
        HasComments,
        HasFactory,
        HasMedia,
        HasThumbnail,
        HasUuids,
        Translatable,
        UsedInFrontend,
        HasViews,
        HasContent,
        HasFrontendUrl;

    protected $fillable = [
        'views',
        'status',
        'price',
        'compare_price',
        'is_free',
    ];

    public $translatedAttributes = [
        'title',
        'content',
        'slug',
        'locale',
    ];

    public $translatedAttributeFormats = [
        'content' => 'html',
    ];

    protected $casts = [
        'views' => 'integer',
        'status' => GameStatus::class,
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'is_free' => 'boolean',
    ];

    public $mediaChannels = [
        'thumbnail',
        'screenshots',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(GameCategory::class, 'game_category', 'game_id', 'game_category_id');
    }

    /**
     * Get the first category (for backward compatibility).
     * Use $game->categories->first() to get a single model instance.
     */
    public function category(): BelongsToMany
    {
        return $this->belongsToMany(GameCategory::class, 'game_category', 'game_id', 'game_category_id')
            ->take(1);
    }

    public function downloadLinks(): HasMany
    {
        return $this->hasMany(GameDownloadLink::class, 'game_id', 'id')->orderBy('order');
    }

    public function vendors(): BelongsToMany
    {
        return $this->belongsToMany(GameVendor::class, 'game_game_vendor', 'game_id', 'game_vendor_id');
    }

    public function platforms(): BelongsToMany
    {
        return $this->belongsToMany(GamePlatform::class, 'game_game_platform', 'game_id', 'game_platform_id');
    }

    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(GameLanguage::class, 'game_game_language', 'game_id', 'game_language_id');
    }

    public function systemRequirements(): HasMany
    {
        return $this->hasMany(SystemRequirement::class, 'game_id', 'id');
    }

    public function scopeWhereInFrontend(Builder $builder, bool $cache): Builder
    {
        return $builder->with(
            [
                'category' => fn($q) => $q
                    ->when($cache, fn($q) => $q->cacheFor(3600))
                    ->withTranslation(),
                'media' => fn($q) => $q
                    ->when($cache, fn($q) => $q->cacheFor(3600)),
            ]
        )
            ->withTranslation(null, null, $cache)
            ->where('status', GameStatus::PUBLISHED);
    }

    public function getUrl(): string
    {
        return home_url("game/{$this->slug}", $this->locale);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): GameFactory
    {
        return GameFactory::new();
    }
}
