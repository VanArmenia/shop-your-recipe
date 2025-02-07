<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasBreadcrumbs;

class RecipeCategory extends Model
{
    use HasFactory;
    use HasBreadcrumbs;

    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'category_id');
    }

    public function getCategoryRoute()
    {
        return route('recipe.category', $this);
    }
}
