<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'theme',
        'media_type',
        'thumbnail',
        'mime_type',
        'file_size',
    ];

    public function category()
    {
        return $this->belongsTo(QuoteCategory::class);
    }
}
