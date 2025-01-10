<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\CategoryListResource;
use App\Http\Resources\ProductResource;
use App\Models\Api\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\RecipeCategory;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $categories = Category::all();

        return CategoryListResource::collection($categories);
    }

    public function recipeCategories()
    {
        $categories = RecipeCategory::all();

        return CategoryListResource::collection($categories);
    }
}
