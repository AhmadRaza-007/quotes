<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'quote',
    ];

    public function category()
    {
        return $this->belongsTo(QuoteCategory::class);
    }

    public function likes()
    {
        return $this->hasMany(QuoteLike::class);
    }

    public function comments()
    {
        return $this->hasMany(QuoteComment::class);
    }

    public function favourites()
    {
        return $this->hasMany(QuoteFavourite::class);
    }

    public function userLikes($user_id)
    {
        return $this->hasMany(QuoteLike::class)->where('user_id', $user_id);
    }

    public function userFavourites($user_id)
    {
        return $this->hasMany(QuoteFavourite::class)->where('user_id', $user_id);
    }
}
