<?php

namespace App\Models;

use App\Traits\HasBreadcrumbs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipeCategory extends Model
{
    use HasFactory;
    use HasBreadcrumbs;

    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'category_id');
    }

    public function getUnitRoute()
    {
        // Check if this category has any recipes directly in the database
        if ($this->recipes()->exists()) {
            return route('recipe.category', $this);
        }

    }

    public function getUnitName()
    {
        return $this->name;
    }
}
