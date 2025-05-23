<?php

namespace App\Models;

use App\Traits\HasBreadcrumbs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;
    use HasBreadcrumbs;

    protected $fillable = ['latitude', 'longitude', 'map_url'];

    public function recipes () {
        return $this->hasMany(Recipe::class);
    }

    public function parent()
    {
        return $this->belongsTo(Region::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Region::class, 'parent_id');
    }

    public function getUnitRoute()
    {
        return route('recipe.region', $this);
    }

    public function getUnitName()
    {
        return $this->name;
    }
}
