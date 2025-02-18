<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class RecipesSeeder extends Seeder
{
    private $areaToCountryMapping = [
        'American'  => 'USA',
        'British'   => 'United Kingdom',
        'Canadian'  => 'Canada',
        'Chinese'   => 'China',
        'Dutch'     => 'Netherlands',
        'Egyptian'  => 'Egypt',
        'French'    => 'France',
        'German'    => 'Germany',
        'Greek'     => 'Greece',
        'Indian'    => 'India',
        'Irish'     => 'Ireland',
        'Italian'   => 'Italy',
        'Jamaican'  => 'Jamaica',
        'Japanese'  => 'Japan',
        'Kenyan'    => 'Kenya',
        'Lebanese'  => 'Lebanon',
        'Malaysian' => 'Malaysia',
        'Mexican'   => 'Mexico',
        'Moroccan'  => 'Morocco',
        'Polish'    => 'Poland',
        'Portuguese'=> 'Portugal',
        'Russian'   => 'Russia',
        'Spanish'   => 'Spain',
        'Thai'      => 'Thailand',
        'Tunisian'  => 'Tunisia',
        'Turkish'   => 'Turkey',
        'Ukrainian' => 'Ukraine',
        'Vietnamese' => 'Vietnam',
        'Filipino' => 'Philippines',
        'Croatian' => 'Croatia',
        'Unknown' => 'Asia',
    ];


    /**
     * Run the database seeds.
     */

    public function run()
    {
        $categoriesResponse = Http::get('https://www.themealdb.com/api/json/v1/1/categories.php');

        if ($categoriesResponse->successful()) {
            $categoriesData = $categoriesResponse->json();

            if (isset($categoriesData['categories']) && is_array($categoriesData['categories'])) {
                foreach ($categoriesData['categories'] as $category) {
                    $categoryName = $category['strCategory'];

                    $mealsResponse = Http::get("https://www.themealdb.com/api/json/v1/1/filter.php?c={$categoryName}");

                    if ($mealsResponse->successful()) {
                        $mealsData = $mealsResponse->json();

                        if (isset($mealsData['meals']) && is_array($mealsData['meals'])) {
                            foreach ($mealsData['meals'] as $meal) {
                                $mealId = $meal['idMeal'];
                                $mealDetailsResponse = Http::get("https://www.themealdb.com/api/json/v1/1/lookup.php?i={$mealId}");

                                if ($mealDetailsResponse->successful()) {
                                    $mealDetailsData = $mealDetailsResponse->json();
                                    $mealDetails = $mealDetailsData['meals'][0] ?? [];

                                    // Translate `strArea` to country name
                                    $areaName = $mealDetails['strArea'] ?? null;
                                    $countryName = $this->areaToCountryMapping[$areaName] ?? null;
                                    $regionId = null;

                                    if ($countryName) {
                                        $region = DB::table('regions')->where('name', $countryName)->first();
                                        $regionId = $region ? $region->id : null;
                                    }

                                    // Create Recipe
                                    $recipe = Recipe::updateOrCreate(
                                        ['id' => $mealId],
                                        [
                                            'name' => $mealDetails['strMeal'],
                                            'description' => $mealDetails['strInstructions'],
                                            'instructions' => $mealDetails['strInstructions'],
                                            'image_url' => $mealDetails['strMealThumb'],
                                            'region_id' => $regionId,
                                            'created_by' => 1,
                                        ]
                                    );

                                    // Process and Store Ingredients
                                    $ingredients = $this->getIngredients($mealDetails);
                                    foreach ($ingredients as $ingredientName => $measurement) {
                                        if (!empty($ingredientName)) {

                                            $normalizedIngredient = $this->normalizeIngredient($ingredientName);

                                            // Check if the ingredient already exists and create if not
                                            $ingredient = Ingredient::updateOrCreate(
                                                ['name' => $ingredientName], // Store the original name
                                                ['normalized_name' => $normalizedIngredient] // Store the normalized name
                                            );

                                            // Attach the ingredient to the recipe with measurement
                                            DB::table('ingredient_recipe')->updateOrInsert(
                                                [
                                                    'recipe_id' => $recipe->id,
                                                    'ingredient_id' => $ingredient->id,
                                                ],
                                                ['measurement' => $measurement]
                                            );
                                        }
                                    }
                                } else {
                                    $this->command->error('Failed to fetch meal details for ID: ' . $mealId);
                                }
                            }
                        } else {
                            $this->command->error('No meals data found for category: ' . $categoryName);
                        }
                    } else {
                        $this->command->error('Failed to fetch meals for category: ' . $categoryName);
                    }
                }
            } else {
                $this->command->error('No categories data found in the API response.');
            }
        } else {
            $this->command->error('Failed to fetch categories from the API.');
        }
    }

    /**
     * Get formatted ingredients as an associative array.
     *
     * @param array $item
     * @return array
     */
    private function getIngredients($item)
    {
        $ingredients = [];

        for ($i = 1; $i <= 20; $i++) {
            $ingredient = trim($item["strIngredient$i"] ?? '');
            $measure = trim($item["strMeasure$i"] ?? '');

            if (!empty($ingredient)) {
                $ingredients[$ingredient] = $measure; // Store ingredient as key and measurement as value
            }
        }

        return $ingredients;
    }

    private function normalizeIngredient($ingredient)
    {
        $mapping = [
            'Sun-Dried Tomatoes' => 'Tomatoes',
            'baby plum tomatoes' => 'Tomatoes',
            'Chicken Thighs' => 'Chicken',
            'Chicken Breast' => 'Chicken',
            'Parmesan cheese' => 'Cheese',
            'Cheddar cheese' => 'Cheese',
            'Mozzarella cheese' => 'Cheese',
            'Red Bell Pepper' => 'Bell Pepper',
            'Green Bell Pepper' => 'Bell Pepper',
            'Yellow Bell Pepper' => 'Bell Pepper',
        ];

        return $mapping[$ingredient] ?? $ingredient; // Default to itself if no match
    }

}
