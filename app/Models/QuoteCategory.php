<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_name',
    ];

    public function quote(){
        return $this->hasMany(Quote::class, 'category_id');
    }

    public function theme(){
        return $this->hasMany(Theme::class, 'category_id');
    }
}
