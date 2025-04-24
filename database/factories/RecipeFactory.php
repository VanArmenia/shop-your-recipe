<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Recipe;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recipe>
 */
class RecipeFactory extends Factory
{
    protected $model = Recipe::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph,
            'prep_time' => $this->faker->numberBetween(5, 30),
            'cook_time' => $this->faker->numberBetween(10, 60),
            'servings' => $this->faker->numberBetween(1, 8),
            'difficulty' => $this->faker->randomElement(['easy', 'medium', 'hard']),
            'tags' => implode(',', $this->faker->words(3)),
            'image_url' => $this->faker->imageUrl(),
            'video_url' => $this->faker->url(),
            'rating' => $this->faker->numberBetween(1, 5),
            'calories' => $this->faker->numberBetween(100, 800),
            'protein' => $this->faker->numberBetween(5, 50),
            'carbohydrates' => $this->faker->numberBetween(10, 100),
            'fats' => $this->faker->numberBetween(5, 50),
            'is_vegan' => $this->faker->boolean,
            'is_vegetarian' => $this->faker->boolean,
            'is_gluten_free' => $this->faker->boolean,
            'season' => $this->faker->randomElement(['spring', 'summer', 'autumn', 'winter']),
            'occasion' => $this->faker->randomElement(['birthday', 'holiday', 'everyday']),
            'gallery_images' => json_encode([$this->faker->imageUrl(), $this->faker->imageUrl()]),
            'steps_video_urls' => json_encode([$this->faker->url(), $this->faker->url()]),
            'created_by' => User::factory(),
            'updated_by' => null,
            'is_featured' => $this->faker->boolean,
            'region_id' => null,
            'category_id' => null,
        ];
    }
}
