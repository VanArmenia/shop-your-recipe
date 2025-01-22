<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rating',
        'review_text',
    ];

    public function reviewable()
    {
        return $this->morphTo();
    }

    // Relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
