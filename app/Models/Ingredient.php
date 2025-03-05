<?php

namespace App\Models;

use App\Traits\HasBreadcrumbs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;
    use HasBreadcrumbs;

    protected $fillable = ['name', 'normalized_name'];

    public function recipes()
    {
        return $this->belongsToMany(Recipe::class)->withPivot('measurement');
    }

    public function getAllRelatedRecipes()
    {
        return Recipe::whereHas('ingredients', function ($query) {
            $query->where('normalized_name', $this->normalized_name);
        })->paginate(10);;
    }

    // Helper method to get a URL-friendly version of the normalized name
    public function getSlug()
    {
        return str_slug($this->normalized_name);
    }

    public function getUnitRoute()
    {
        return route('recipe.ingredient', $this);
    }
}
