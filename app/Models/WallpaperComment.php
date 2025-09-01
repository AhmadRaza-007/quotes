<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WallpaperComment extends Model
{
    use HasFactory;

    protected $table = 'wallpaper_comments';

    protected $fillable = [
        'wallpaper_id',
        'user_id',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
