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
        'media_type',
        'thumbnail',
        'mime_type',
        'file_size',
    ];

    public function category()
    {
        return $this->belongsTo(WallpaperCategory::class, 'category_id');
    }
}
