<?php

namespace Database\Factories;

use App\Models\Recipe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class RecipeImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $recipe = Recipe::factory()->create();

        return [
            'recipe_id' => $recipe->id,
            'path' => fake()->text(),
            'mime' => fake()->mimeType(),
            'size' => fake()->numberBetween(100,400),
            'position' => fake()->numberBetween(1,10),
            'url' => fake()->imageUrl(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
