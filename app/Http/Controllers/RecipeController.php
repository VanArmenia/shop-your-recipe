<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\RecipeCategory;
use App\Models\Region;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categoryBr = RecipeCategory::where('name', 'Breakfast')->first();
        // Get recipes for Breakfast category
        $breakfast = Recipe::query()
            ->where('category_id', $categoryBr->id)
            ->with('reviews') // Include the 'reviews' relationship
            ->withCount('reviews as review_count') // Add review count
            ->withAvg('reviews as average_rating', 'rating') // Add average rating
            ->orderBy('updated_at', 'desc')
            ->limit(10) // Limit the results to 10 recipes
            ->get();

        $categoryVg = RecipeCategory::where('name', 'Vegetarian')->first();
        // Get recipes for Vegetarian category
        $vegetarian = Recipe::query()
            ->where('category_id', $categoryVg->id)
            ->with('reviews') // Include the 'reviews' relationship
            ->withCount('reviews as review_count') // Add review count
            ->withAvg('reviews as average_rating', 'rating') // Add average rating
            ->orderBy('updated_at', 'desc')
            ->limit(10) // Limit the results to 10 recipes
            ->get();

        // Get count of all recipes
        $countRecipes = Recipe::count();

        // Get count of all reviews
        $countReviews = Review::count();

        $latestRecipes = Recipe::latest()->take(5)->get();

        $rootRegions = Region::whereNull('parent_id') // Get root regions
        ->with(['children' => function ($query) {
            $query->whereHas('recipes'); // Only include children that have recipes
        }])
        ->get();

        $ingredients = Ingredient::select('normalized_name')
            ->whereNotNull('normalized_name')
            ->distinct()
            ->get();

//        dd($ingredients);

        return view('recipes.index', [
            'breakfasts' => $breakfast,
            'vegetarians' => $vegetarian,
            'countRecipes' => $countRecipes,
            'countReviews' => $countReviews,
            'latestRecipes' => $latestRecipes,
            'rootRegions' => $rootRegions,
            'ingredients' => $ingredients,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe)
    {
        $breadcrumbs = $recipe->category->getBreadcrumbs();
        return view('recipes.view', compact('recipe', 'breadcrumbs'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function storeReview(Request $request, $recipeId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
        ]);

        // Ensure the product exists
        $recipe = Recipe::findOrFail($recipeId);

        // Create the review
        $recipe->reviews()->create([
            'user_id' => auth()->id(),  // Assuming the user is authenticated
            'rating' => $request->input('rating'),
            'review_text' => $request->input('review_text'),
        ]);

        // Return a JSON response for Alpine.js handling
        return response()->json([
            'message' => 'Review submitted successfully!',
        ]);
    }

    // Method to fetch reviews
    public function fetchReviews($recipeId)
    {
        $recipe = Recipe::findOrFail($recipeId);
        $reviews = $recipe->reviews()->with('user.customer')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'reviews' => $reviews,
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('q');

        $recipes = Recipe::query()
            ->where('name', 'like', '%' . $query . '%')
            ->with('images') // Load images relationship
            ->paginate(10) // Paginate before using map()
            ->through(function ($recipe) {
                return [
                    'id' => $recipe->id,
                    'name' => $recipe->name,
                    'category' => $recipe->category,
                    'image' => $recipe->images->first()?->url // Get the first image URL
                ];
            });

        return response()->json($recipes);
    }

    public function category(RecipeCategory $category)
    {
        $recipes = $category->recipes()
            ->orderBy('updated_at', 'desc')
            ->paginate(5);
        return view('recipes.category', compact('recipes','category'));
    }

    public function region(Region $region)
    {
        $breadcrumbs = $region->getBreadcrumbs();
        // Get the region's children IDs
        $regionIds = $region->children()->pluck('id')->toArray();

        // Include the parent region's ID in the list
        $regionIds[] = $region->id;

        // Fetch recipes from the current region and its children
        $recipes = Recipe::whereIn('region_id', $regionIds)
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('recipes.region', compact('recipes', 'region', 'breadcrumbs'));
    }

    public function ingredient(Ingredient $ingredient)
    {
        return view('recipes.ingredient', compact('ingredient'));
    }

    public function showRecipesByIngredient(Ingredient $ingredient)
    {

        $recipes = $ingredient->getAllRelatedRecipes();

        return view('recipes.ingredient', compact('recipes', 'ingredient'));
    }

}
