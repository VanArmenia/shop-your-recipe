<?php

namespace Database\Seeders;

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
        // Fetch all categories
        $categoriesResponse = Http::get('https://www.themealdb.com/api/json/v1/1/categories.php');

        // Check if the response is successful
        if ($categoriesResponse->successful()) {
            $categoriesData = $categoriesResponse->json();

            if (isset($categoriesData['categories']) && is_array($categoriesData['categories'])) {
                // Iterate over the categories
                foreach ($categoriesData['categories'] as $category) {
                    $categoryName = $category['strCategory'];

                    // Fetch meals for each category
                    $mealsResponse = Http::get("https://www.themealdb.com/api/json/v1/1/filter.php?c={$categoryName}");

                    if ($mealsResponse->successful()) {
                        $mealsData = $mealsResponse->json();

                        if (isset($mealsData['meals']) && is_array($mealsData['meals'])) {
                            // Iterate over the meals
                            foreach ($mealsData['meals'] as $meal) {
                                // Fetch details for each meal
                                $mealId = $meal['idMeal'];
                                $mealDetailsResponse = Http::get("https://www.themealdb.com/api/json/v1/1/lookup.php?i={$mealId}");

                                if ($mealDetailsResponse->successful()) {
                                    $mealDetailsData = $mealDetailsResponse->json();
                                    $mealDetails = $mealDetailsData['meals'][0] ?? [];

                                    // Translate `strArea` to country name
                                    $areaName = $mealDetails['strArea'] ?? null;
                                    $countryName = $this->areaToCountryMapping[$areaName] ?? null;
                                    $regionId = null;

                                    var_dump($areaName);
                                    var_dump($countryName);
                                    if ($countryName) {
                                        $region = DB::table('regions')->where('name', $countryName)->first();
                                        $regionId = $region ? $region->id : null;
                                    }

                                    Recipe::updateOrCreate(
                                        ['id' => $mealId],
                                        [
                                            'name' => $mealDetails['strMeal'],
                                            'description' => $mealDetails['strInstructions'], // Using instructions as a placeholder for description
                                            'ingredients' => $this->getIngredients($mealDetails), // Combine ingredients into a single string
                                            'instructions' => $mealDetails['strInstructions'],
                                            'prep_time' => null, // API does not provide prep time
                                            'cook_time' => null, // API does not provide prep time
                                            'servings' => $mealDetails['strServings'] ?? null,
                                            'difficulty' => $mealDetails['strDifficulty'] ?? null,
                                            'category' => $mealDetails['strCategory'] ?? $categoryName,
                                            'image_url' => $mealDetails['strMealThumb'],
                                            'rating' => null, // API does not provide rating
                                            'region_id' => $regionId,
                                            'created_by' => 1,
                                        ]
                                    );
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
            // Handle the error
            $this->command->error('Failed to fetch categories from the API.');
        }
    }

    /**
     * Get formatted ingredients from the API item.
     *
     * @param array $item
     * @return string
     */
    private function getIngredients($item)
    {
        $ingredients = [];

        // Loop through possible ingredient fields
        for ($i = 1; $i <= 20; $i++) {
            $ingredient = $item["strIngredient$i"];
            $measure = $item["strMeasure$i"];

            if (!empty($ingredient)) {
                $ingredients[] = trim("$ingredient - $measure");
            }
        }

        // Join all ingredients into a single string
        return implode("\n", $ingredients);
    }
}
