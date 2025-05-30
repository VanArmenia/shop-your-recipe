<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'category', 'prep_time', 'image', 'cook_time', 'servings', 'calories', 'protein', 'carbohydrates', 'fats', 'created_by', 'updated_by', 'category_id', 'region_id'];

    public function images()
    {
        return $this->hasMany(RecipeImage::class)->orderBy('position');
    }

    public function category()
    {
        return $this->belongsTo(RecipeCategory::class, 'category_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function getImageAttribute()
    {
        return $this->images->count() > 0 ? $this->images->first()->url : null;
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function getReviewCountAttribute()
    {
        return $this->reviews()->count(); // Returns the total number of reviews
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0; // Returns average rating, or 0 if no reviews
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class)->withPivot('measurement');
    }
}
