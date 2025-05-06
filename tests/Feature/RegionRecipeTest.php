<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\RecipeCategory;
use App\Models\RecipeImage;
use App\Models\Region;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegionRecipeTest extends TestCase
{

    public function test_it_shows_recipes_from_region_and_its_children_with_recipes()
    {
        $region = Region::factory()->create();
        $childWithRecipes = Region::factory()->create(['parent_id' => $region->id]);
        $childWithoutRecipes = Region::factory()->create(['parent_id' => $region->id]);

        $recipe1 = Recipe::factory()->create(['region_id' => $region->id]);
        $recipe2 = Recipe::factory()->create(['region_id' => $childWithRecipes->id]);

        $response = $this->get(route('recipe.region', $region));

        $response->assertStatus(200);
        $response->assertViewIs('recipes.region');
        $response->assertViewHas('recipes', function ($recipes) use ($recipe1, $recipe2) {
            return $recipes->contains($recipe1) && $recipes->contains($recipe2);
        });

        $response->assertViewHas('region', $region);
    }

    public function test_it_excludes_children_without_recipes()
    {
        $region = Region::factory()->create();
        $childWithRecipes = Region::factory()->create(['parent_id' => $region->id]);
        $childWithoutRecipes = Region::factory()->create(['parent_id' => $region->id]);

        Recipe::factory()->create(['region_id' => $childWithRecipes->id]);

        $this->get(route('recipe.region', $region))
            ->assertViewHas('region', function ($r) use ($childWithRecipes, $childWithoutRecipes) {
                return $r->children->contains($childWithRecipes)
                    && !$r->children->contains($childWithoutRecipes);
            });
    }

    public function test_it_returns_only_parent_region_recipes_if_no_children_have_recipes()
    {
        $region = Region::factory()->create();
        $child = Region::factory()->create(['parent_id' => $region->id]);
        $parentRecipe = Recipe::factory()->create(['region_id' => $region->id]);

        $response = $this->get(route('recipe.region', $region));

        $response->assertViewHas('recipes', function ($recipes) use ($parentRecipe) {
            return $recipes->contains($parentRecipe) && $recipes->count() === 1;
        });
    }

    public function test_it_returns_no_recipes_if_region_and_children_have_none()
    {
        $region = Region::factory()->create();
        Region::factory()->create(['parent_id' => $region->id]);

        $response = $this->get(route('recipe.region', $region));

        $response->assertViewHas('recipes', function ($recipes) {
            return $recipes->isEmpty();
        });
    }

}
