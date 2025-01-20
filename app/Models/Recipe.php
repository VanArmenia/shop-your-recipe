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
        return $this->belongsTo(RecipeCategory::class);
    }

    public function getImageAttribute()
    {
        return $this->images->count() > 0 ? $this->images->get(0)->url : null;
    }


}
