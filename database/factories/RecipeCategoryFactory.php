<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class RecipeCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(1, true),
            'description' => fake()->realText(2000),
            'parent_id' => $this->faker->randomElement([null, 1]), // Randomly assign null or 1 as a parent
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
