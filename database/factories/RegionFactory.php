<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class RegionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->country(1, true),
            'description' => fake()->realText(2000),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'parent_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
