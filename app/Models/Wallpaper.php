<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallpaper extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'file_path',
        'file_url',
        'thumbnail_url',
        'media_type',
        'mime_type',
        'file_size',
        'owner_user_id',
        'is_admin',
    ];

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
        return $this->belongsTo(User::class, 'owner_user_id');
    }
}
