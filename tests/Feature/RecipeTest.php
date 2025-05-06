<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\RecipeCategory;
use App\Models\RecipeImage;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RecipeTest extends TestCase
{
    public function test_authenticated_user_can_submit_review()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();

        $this->actingAs($user)
            ->postJson("/recipes/{$recipe->id}/reviews", [
                'rating' => 4,
                'review_text' => 'Nice!',
            ])
            ->assertStatus(200)
            ->assertJson(['message' => 'Review submitted successfully!']);

        // ** Different scenarios for handling validation testing //
        $this->actingAs($user)
            ->postJson("/recipes/{$recipe->id}/reviews", []) // Send empty payload
            ->assertStatus(422)
            ->assertJsonValidationErrors(['rating']);

        $this->actingAs($user)
            ->postJson("/recipes/{$recipe->id}/reviews", [
                'rating' => 6, // too high
                'review_text' => 'Too good to be true!',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['rating']);

        $longText = str_repeat('a', 1001); // 1 character too many

        $this->actingAs($user)
            ->postJson("/recipes/{$recipe->id}/reviews", [
                'rating' => 4,
                'review_text' => $longText,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['review_text']);
        // Different scenarios for handling validation testing ** //

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'reviewable_id' => $recipe->id,
            'reviewable_type' => \App\Models\Recipe::class, // Important to match the polymorphic type
            'rating' => 4,
            'review_text' => 'Nice!',
        ]);
    }

    public function test_index_page_loads_successfully()
    {
        // Create categories and associated recipes for testing
        $categoryBr = RecipeCategory::factory()->create(['name' => 'Breakfast']);
        $categoryVg = RecipeCategory::factory()->create(['name' => 'Vegetarian']);

        // Optionally create a recipe under each category to test with
        Recipe::factory()->create(['category_id' => $categoryBr->id]);
        Recipe::factory()->create(['category_id' => $categoryVg->id]);

        $response = $this->get('/recipes'); // Adjust if your route is different

        $response->assertStatus(200);
        $response->assertViewIs('recipes.index');
    }

    public function test_index_view_receives_expected_data()
    {
        $categoryBr = RecipeCategory::factory()->create(['name' => 'Breakfast']);
        $categoryVg = RecipeCategory::factory()->create(['name' => 'Vegetarian']);

        $recipe1 = Recipe::factory()->create(['category_id' => $categoryBr->id]);
        $recipe2 = Recipe::factory()->create(['category_id' => $categoryVg->id]);
        $review = Review::factory()->create(['reviewable_id' => $recipe1->id, 'reviewable_type' => Recipe::class, 'rating' => 5]);

        $response = $this->get('/recipes');

        $response->assertViewHasAll([
            'breakfasts',
            'vegetarians',
            'countRecipes',
            'countReviews',
            'latestRecipes',
            'rootRegions',
            'ingredients',
        ]);

        $response->assertSee($recipe1->title); // Example: see recipe in rendered HTML
    }

    public function test_recipe_search_endpoint_filters_and_returns_expected_data()
    {
        // Create recipes
        $recipeWithImage = Recipe::factory()
            ->has(RecipeImage::factory()->count(1), 'images') // use the correct factory and relationship name
            ->create([
                'name' => 'Spaghetti',
                'difficulty' => 'easy',
            ]);


        $recipeWithoutImage = Recipe::factory()
            ->create(['name' => 'Salad', 'difficulty' => 'moderate']);

        $nonMatchingRecipe = Recipe::factory()
            ->create(['name' => 'Burger']);

        // ✅ Test: Search with matching query
        $response = $this->getJson('/recipes/search?q=spa');
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Spaghetti']);
        $response->assertJsonMissing(['name' => 'Burger']);

        // ✅ Test: Recipe with image returns correct image URL
        $this->assertEquals(
            $recipeWithImage->images->first()->url,
            $response['data'][0]['image']
        );

        // ✅ Test: Recipe with no image returns image => null
        $response2 = $this->getJson('/recipes/search?q=salad');
        $response2->assertStatus(200);
        $response2->assertJsonFragment(['name' => 'Salad']);
        $this->assertNull($response2['data'][0]['image']);

        // ✅ Test: Invalid query (too long string)
        $longQuery = str_repeat('a', 101);
        $response4 = $this->getJson('/recipes/search?q=' . $longQuery);
        $response4->assertStatus(422); // Only works if you add validation
    }

}
