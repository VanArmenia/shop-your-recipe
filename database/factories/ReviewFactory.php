<?php

namespace Database\Factories;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $user = User::factory()->create();

        // Get a random reviewable entity (e.g., Recipe or other types)
        $reviewable = Recipe::factory()->create();
        return [
            'user_id' => $user->id,
            'rating' => $this->faker->numberBetween(1, 5), // Random rating between 1 and 5
            'review_text' => $this->faker->optional()->text(), // Optional review text (nullable in DB)
            'reviewable_type' => get_class($reviewable), // This stores the class name of the reviewable model
            'reviewable_id' => $reviewable->id, // The ID of the specific reviewed item
            'created_at' => $this->faker->dateTimeThisYear(), // Random creation date within the current year
            'updated_at' => $this->faker->dateTimeThisYear(), // Random update date within the current year
        ];
    }
}
