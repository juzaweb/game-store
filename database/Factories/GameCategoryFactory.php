<?php

namespace Juzaweb\Modules\GameStore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Juzaweb\Modules\GameStore\Models\GameCategory;

/**
 * @extends Factory<GameCategory>
 */
class GameCategoryFactory extends Factory
{
    protected $model = GameCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'parent_id' => null,
            'name' => $this->faker->words(3, true),
            'slug' => $this->faker->slug(),
        ];
    }

    /**
     * Indicate that the category has a parent.
     */
    public function withParent(mixed $parentId): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parentId,
        ]);
    }
}
