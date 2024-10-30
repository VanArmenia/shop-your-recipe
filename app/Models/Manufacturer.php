<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',  // Ensure that 'id' is always an integer
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
