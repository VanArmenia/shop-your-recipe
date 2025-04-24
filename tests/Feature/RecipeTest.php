<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RecipeTest extends TestCase
{
    /**
     * A basic feature test example.
     */
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

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'reviewable_id' => $recipe->id,
            'reviewable_type' => \App\Models\Recipe::class, // Important to match the polymorphic type
            'rating' => 4,
            'review_text' => 'Nice!',
        ]);
    }

}
