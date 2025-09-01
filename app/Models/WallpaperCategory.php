<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WallpaperCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_name',
    ];

    public function wallpapers()
    {
        return $this->hasMany(Wallpaper::class, 'category_id');
    }
}
