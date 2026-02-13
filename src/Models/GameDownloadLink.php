<?php

namespace Juzaweb\Modules\GameStore\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Juzaweb\Modules\Core\Models\Model;

class GameDownloadLink extends Model
{
    use HasUuids;

    protected $table = 'game_download_links';

    protected $fillable = [
        'title',
        'url',
        'size',
        'platform',
        'order',
        'game_id',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class, 'game_id', 'id');
    }
}
