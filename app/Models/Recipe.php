<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'category', 'prep_time', 'image'];

    public function images()
    {
        return $this->hasMany(RecipeImage::class)->orderBy('position');
    }

    public function category()
    {
        return $this->belongsTo(RecipeCategory::class, 'category_id');
    }

    public function getImageAttribute()
    {
        return $this->images->count() > 0 ? $this->images->get(0)->url : null;
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

}
