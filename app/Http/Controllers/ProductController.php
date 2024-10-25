<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
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
        $breadcrumbs = $this->getCategoryBreadcrumbs($product->category);
        $categories = Category::with('children')->whereNull('parent_id')->get();
        return view('product.view', compact('product', 'simProducts', 'breadcrumbs', 'categories'));
    }

    public function category(Category $category)
    {
        $products = $category->products()
            ->orderBy('updated_at', 'desc')
            ->paginate(5);
        return view('product.category', [
            'products' => $products
        ]);
    }

    public function getCategoryBreadcrumbs($category)
    {
        $breadcrumbs = [];
        while ($category) {
            $breadcrumbs[] = [
                'name' => $category->name,
                'url' => route('category', $category)
            ];
            $category = $category->parent; // Assuming parent relationship exists
        }
        return array_reverse($breadcrumbs); // Reverse to get root-to-child order
    }

    public function shop()
    {
        $products = Product::query()
            ->where('published', '=', 1)
            ->orderBy('updated_at', 'desc')
            ->paginate(5);
        $categories = Category::with('children')->whereNull('parent_id')->get();
        return view('shop.index', compact('products','categories'));
    }

}
