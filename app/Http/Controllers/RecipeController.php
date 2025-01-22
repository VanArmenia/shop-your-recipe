<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get recipes for Breakfast category
        $breakfast = Recipe::query()
            ->where('category', 'Breakfast')
            ->with('reviews') // Include the 'reviews' relationship
            ->withCount('reviews as review_count') // Add review count
            ->withAvg('reviews as average_rating', 'rating') // Add average rating
            ->orderBy('updated_at', 'desc')
            ->limit(10) // Limit the results to 10 recipes
            ->get();

        // Get recipes for Vegetarian category
        $vegetarian = Recipe::query()
            ->where('category', 'Vegetarian')
            ->with('reviews') // Include the 'reviews' relationship
            ->withCount('reviews as review_count') // Add review count
            ->withAvg('reviews as average_rating', 'rating') // Add average rating
            ->orderBy('updated_at', 'desc')
            ->limit(10) // Limit the results to 10 recipes
            ->get();

        return view('recipes.index', [
            'breakfasts' => $breakfast,
            'vegetarians' => $vegetarian
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
        return view('recipes.view', compact('recipe'));
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
}
