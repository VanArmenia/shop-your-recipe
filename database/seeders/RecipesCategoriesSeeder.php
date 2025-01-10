<?php

namespace Database\Seeders;

use App\Models\RecipeCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class RecipesCategoriesSeeder extends Seeder
{
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
                    RecipeCategory::updateOrCreate(
                        [
                            'name' => $categoryName,
                            'description' => '',

                        ]
                    );
                }
            } else {
                $this->command->error('No categories data found in the API response.');
            }
        }
    }
}
