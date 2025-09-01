<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WallpaperFavourite extends Model
{
    use HasFactory;

    protected $table = 'wallpaper_favourites';

    protected $fillable = [
        'wallpaper_id',
        'user_id',
    ];
}
