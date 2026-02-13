<?php

namespace Juzaweb\Modules\GameStore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Juzaweb\Modules\GameStore\Models\Game;

/**
 * @extends Factory<Game>
 */
class GameFactory extends Factory
{
    protected $model = Game::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(3);
        
        return [
            'category_id' => null,
            'views' => $this->faker->numberBetween(0, 10000),
            'status' => 'published',
            'title' => $title,
            'content' => $this->faker->paragraph(),
            'slug' => $this->faker->slug(),
        ];
    }

    /**
     * Indicate that the game is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Indicate that the game has a category.
     */
    public function withCategory(mixed $categoryId): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $categoryId,
        ]);
    }
}
