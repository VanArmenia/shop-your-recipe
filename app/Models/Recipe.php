<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'category', 'prep_time', 'images'];

    public function images()
    {
        return $this->hasMany(RecipeImage::class)->orderBy('position');
    }

    public function category()
    {
        return $this->belongsTo(RecipeCategory::class);
    }

}
