<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RecipeRequest;
use App\Http\Resources\RecipeListResource;
use App\Http\Resources\RecipeResource;
use App\Models\Api\Recipe;
use App\Models\ProductImage;
use App\Models\RecipeImage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $perPage = $request->query('per_page', 10);
        $sortField = $request->query('sort_field', 'name'); // Default sorting by recipe name
        $sortDirection = $request->query('sort_direction', 'asc');

        // Check if sorting by category
        if ($sortField === 'category_name') {
            $sortField = 'recipe_categories.name'; // Change to correct column
        } else {
            $sortField = 'recipes.' . $sortField; // Use the recipes table for other fields
        }

        $recipes = Recipe::select('recipes.*')
            ->leftJoin('recipe_categories', 'recipes.category_id', '=', 'recipe_categories.id') // Join categories
            ->when($search, function ($query) use ($search) {
                return $query->where('recipes.name', 'like', "%$search%");
            })
            ->orderBy($sortField, $sortDirection) // Apply sorting
            ->paginate($perPage);

        return RecipeListResource::collection($recipes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(RecipeRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        /** @var \Illuminate\Http\UploadedFile $image */
        $images = $data['images'] ?? [];

        $recipe = Recipe::create($data);
        $this->saveImages($images, $recipe);

        return new RecipeResource($recipe);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show(Recipe $recipe)
    {
        return new RecipeResource($recipe);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product      $product
     * @return \Illuminate\Http\Response
     */
    public function update(RecipeRequest $request, Recipe $recipe)
    {
        $data = $request->validated();
        $data['updated_by'] = $request->user()->id;

        /** @var \Illuminate\Http\UploadedFile[] $images */
        $images = $data['images'] ?? [];
        $deletedImages = $data['deleted_images'] ?? [];

        // Check if image was given and save on local file system
        $this->saveImages($images, $recipe);
        $this->deleteImages($deletedImages, $recipe);

        $recipe->update($data);
        return new RecipeResource($recipe);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Recipe $recipe)
    {
        $recipe->delete();

        return response()->noContent();
    }


    private function saveImages($images, Recipe $recipe)
    {
        foreach ($images as $i => $image) {
            $path = 'images/' . Str::random();
            if (!Storage::exists($path)) {
                Storage::makeDirectory($path);
            }
            if (!Storage::putFileAS('public/' . $path, $image, $image->getClientOriginalName())) {
                throw new \Exception("Unable to save file \"{$image->getClientOriginalName()}\"");
            }

            $relativePath = $path . '/' . $image->getClientOriginalName();

            RecipeImage::create([
                'recipe_id' => $recipe->id,
                'path' => $relativePath,
                'url' => URL::to(Storage::url($relativePath)),
                'mime' => $image->getClientMimeType(),
                'size' => $image->getSize(),
                'position' => $i + 1
            ]);
        }
    }

    public function deleteImages($imageIds, Recipe $recipe)
    {
        $images = RecipeImage::query()
            ->where('recipe_id', $recipe->id)
            ->whereIn('id', $imageIds)
            ->get();

        foreach ($images as $image) {
            if ($image->path) {
                Storage::deleteDirectory('/public/' . dirname($image->path));
            }
            $image->delete();
        }
    }
}
