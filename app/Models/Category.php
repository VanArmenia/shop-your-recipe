<?php

namespace App\Models;

use App\Traits\HasBreadcrumbs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    use HasBreadcrumbs;

    protected $casts = [
        'id' => 'integer',  // Ensure that 'id' is always an integer
    ];

    protected $table = 'categories';

    protected $guarded = [];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function getUnitRoute()
    {
        // Check if this category has any products directly in the database
        if ($this->products()->exists()) {
            return route('product.category', $this);
        }

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
