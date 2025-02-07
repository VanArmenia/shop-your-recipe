<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Manufacturer;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::query()
            ->where('published', '=', 1)
            ->orderBy('updated_at', 'desc')
            ->paginate(5);
        return view('product.index', [
            'products' => $products
        ]);
    }

    public function view(Product $product)
    {
        $simProducts = $product->category->products()
            ->orderBy('updated_at', 'desc')
            ->paginate(5);
        $breadcrumbs = $product->category->getBreadcrumbs();
        $categories = Category::with('children')->whereNull('parent_id')->get();
        return view('product.view', compact('product', 'simProducts', 'breadcrumbs', 'categories'));
    }

    public function category(Category $category)
    {
        $products = $category->products()
            ->orderBy('updated_at', 'desc')
            ->paginate(5);
        $manufacturers = Manufacturer::All();
        $categories = Category::with('children')->whereNull('parent_id')->get();
        return view('product.category', compact('products','categories', 'manufacturers','category'));
    }

    public function shop()
    {
        $products = Product::query()
            ->where('published', '=', 1)
            ->orderBy('updated_at', 'desc')
            ->paginate(10);
        $categories = Category::with('children')->whereNull('parent_id')->get();
        $manufacturers = Manufacturer::All();
        return view('shop.index', compact('products','categories', 'manufacturers'));
    }

    public function storeReview(Request $request, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
        ]);

        // Ensure the product exists
        $product = Product::findOrFail($productId);

        // Create the review
        $product->reviews()->create([
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
    public function fetchReviews($productId)
    {
        $product = Product::findOrFail($productId);
        $reviews = $product->reviews()->with('user.customer')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'reviews' => $reviews,
        ]);
    }
}
