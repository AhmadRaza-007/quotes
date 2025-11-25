<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class Wallpaper extends Model
{
    use HasFactory;
    use HasEagerLimit;

    protected $fillable = [
        'category_id',
        'title',
        'file_path',
        'file_url',
        'thumbnail_url',
        'thumbnail_path',
        'media_type',
        'mime_type',
        'file_size',
        'user_id',
        'is_admin',
    ];

    protected $hidden = ['file_url', 'thumbnail_url'];

    public function category()
    {
        return $this->belongsTo(WallpaperCategory::class, 'category_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(WallpaperComment::class);
    }

    public function favourites()
    {
        return $this->hasMany(Favourite::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeFromActiveCategories($query)
    {
        return $query->whereHas('category', function ($query) {
            $query->active();
        });
    }
}
