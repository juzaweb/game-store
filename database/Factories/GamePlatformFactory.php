<?php

namespace Juzaweb\Modules\GameStore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Juzaweb\Modules\GameStore\Models\GamePlatform;

/**
 * @extends Factory<GamePlatform>
 */
class GamePlatformFactory extends Factory
{
    protected $model = GamePlatform::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->randomElement(['PC', 'Mac', 'Linux', 'PlayStation', 'Xbox', 'Nintendo Switch']);
        
        return [
            'name' => $name,
            'slug' => $this->faker->slug(),
        ];
    }
}
