<?php

namespace Juzaweb\Modules\GameStore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Juzaweb\Modules\GameStore\Models\GameVendor;

/**
 * @extends Factory<GameVendor>
 */
class GameVendorFactory extends Factory
{
    protected $model = GameVendor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'slug' => $this->faker->slug(),
        ];
    }
}
