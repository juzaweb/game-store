<?php

namespace Juzaweb\Modules\GameStore\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Juzaweb\Modules\Core\Models\Model;

class SystemRequirement extends Model
{
    use HasUuids;

    protected $table = 'system_requirements';

    protected $fillable = [
        'game_id',
        'type',
        'game_platform_id',
        'requirements',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }

    public function platform()
    {
        return $this->belongsTo(GamePlatform::class, 'game_platform_id');
    }
}
