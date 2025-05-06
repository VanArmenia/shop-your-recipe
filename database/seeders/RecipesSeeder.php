<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\RecipeCategory;
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

                    $categoryCreated = RecipeCategory::updateOrCreate(
                        [
                            'name' => $categoryName,
                            'description' => '',
                        ]
                    );

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
                                            'image_url' => $mealDetails['strMealThumb'],
                                            'region_id' => $regionId,
                                            'created_by' => 1,
                                            'category_id' => $categoryCreated->id,
                                        ]
                                    );

                                    // Process and Store Ingredients
                                    $ingredients = $this->getIngredients($mealDetails);
                                    foreach ($ingredients as $ingredientName => $measurement) {
                                        if (!empty($ingredientName)) {

                                            $normalizedIngredient = $this->normalizeIngredient($ingredientName);

                                            // Normalize before searching & storing
                                            $ingredient = Ingredient::firstOrCreate(
                                                ['name' => strtolower($ingredientName)] // Store in lowercase
                                            );
                                            $ingredient->update(['normalized_name' => $normalizedIngredient]);

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
            $ingredient = strtolower(trim($item["strIngredient$i"] ?? '')); // Convert to lowercase
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
            'baby plum tomatoes' => 'Tomatoes',
            'plum tomatoes' => 'Tomatoes',
            'diced tomatoes' => 'Tomatoes',
            'cherry tomatoes' => 'Tomatoes',
            'grape tomatoes' => 'Tomatoes',
            'chopped tomatoes' => 'Tomatoes',
            'sun-dried tomatoes' => 'Tomatoes',
            'tomato puree' => 'Tomatoes',
            'tomato' => 'Tomatoes',

            'chopped onion' => 'Onion',
            'red onion' => 'Onion',
            'onion' => 'Onion',
            'onions' => 'Onion',
            'spring onions' => 'Onion',
            'shallots' => 'Onion',

            'freshly chopped parsley' => 'Herbs',
            'parsley' => 'Herbs',
            'chopped parsley' => 'Herbs',
            'rosemary' => 'Herbs',
            'thyme' => 'Herbs',

            'cubed feta cheese' => 'Cheese',
            'shredded monterey jack cheese' => 'Cheese',
            'parmesan' => 'Cheese',
            'gouda cheese' => 'Cheese',
            'cheese' => 'Cheese',
            'parmesan cheese' => 'Cheese',
            'gruyere cheese' => 'Cheese',
            'shredded mexican cheese' => 'Cheese',
            'cream cheese' => 'Cheese',
            'cheese curds' => 'Cheese',
            'goats cheese' => 'Cheese',
            'monterey jack cheese' => 'Cheese',
            'colby jack cheese' => 'Cheese',
            'stilton cheese' => 'Cheese',
            'mozzarella' => 'Cheese',
            'ricotta' => 'Cheese',
            'mascarpone' => 'Cheese',
            'cheddar cheese' => 'Cheese',
            'parmigiano-reggiano' => 'Cheese',
            'paneer' => 'Cheese',


            'free-range egg, beaten' => 'Eggs',
            'egg yolks' => 'Eggs',
            'eggs' => 'Eggs',
            'egg' => 'Eggs',
            'egg white' => 'Eggs',
            'free-range eggs, beaten' => 'Eggs',
            'egg rolls' => 'Eggs',
            'flax eggs' => 'Eggs',

            'bramley apples' => 'Apples',
            'apples' => 'Apples',
            'braeburn apples' => 'Apples',

            'chilled butter' => 'Butter',
            'butter, softened' => 'Butter',

            'noodles' => 'Noodles',
            'rice stick noodles' => 'Noodles',
            'rice noodles' => 'Noodles',
            'udon noodles' => 'Noodles',

            'green beans' => 'Beans',
            'borlotti beans' => 'Beans',
            'cannellini beans' => 'Beans',
            'fermented black beans' => 'Beans',
            'broad beans' => 'Beans',
            'kidney beans' => 'Beans',
            'refried beans' => 'Beans',
            'black beans' => 'Beans',
            'baked beans' => 'Beans',
            'haricot beans' => 'Beans',

            'chestnuts' => 'Nuts',
            'pine nuts' => 'Nuts',
            'peanuts' => 'Nuts',
            'ground almonds' => 'Nuts',
            'flaked almonds' => 'Nuts',
            'pecan nuts' => 'Nuts',
            'almond extract' => 'Nuts',
            'almonds' => 'Nuts',
            'cashew' => 'Nuts',
            'cashew nuts' => 'Nuts',
            'hazlenuts' => 'Nuts',

            'penne rigate' => 'Pasta',
            'bowtie pasta' => 'Pasta',


            'chestnut mushroom' => 'Mushroom',
            'mushroom' => 'Mushroom',
            'shiitake mushroom' => 'Mushroom',
            'wood ear mushroom' => 'Mushroom',
            'wild mushroom' => 'Mushroom',
            'mushrooms' => 'Mushroom',


            'chickpeas' => 'Pulses',
            'french lentils' => 'Pulses',
            'green red lentils' => 'Pulses',
            'lentils' => 'Pulses',

            'semi-skimmed milk' => 'Milk',
            'whole milk' => 'Milk',
            'condensed milk' => 'Milk',
            'milk' => 'Milk',

            'carrots' => 'Vegetables',
            'cucumber' => 'Vegetables',
            'celeriac' => 'Vegetables',
            'green pepper' => 'Vegetables',
            'red pepper' => 'Vegetables',
            'celery' => 'Vegetables',
            'leek' => 'Vegetables',
            'swede' => 'Vegetables',
            'broccoli' => 'Vegetables',
            'iceberg lettuce' => 'Vegetables',
            'lettuce' => 'Vegetables',
            'cabbage' => 'Vegetables',
            'courgettes' => 'Vegetables',
            'aubergine' => 'Vegetables',



        ];

        return $mapping[$ingredient] ??  null; // Default to itself if no match
    }

}
