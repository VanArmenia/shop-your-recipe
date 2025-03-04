<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'normalized_name'];

    public function recipes()
    {
        return $this->belongsToMany(Recipe::class)->withPivot('measurement');
    }

    public function getAllRelatedRecipes()
    {
        return Recipe::whereHas('ingredients', function ($query) {
            $query->where('normalized_name', $this->normalized_name);
        })->get();
    }

    // Helper method to get a URL-friendly version of the normalized name
    public function getSlug()
    {
        return str_slug($this->normalized_name);
    }
}
