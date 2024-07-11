<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'user_id',
        'comment'
    ];

    public function user()
    {
        return $this->belongsto(User::class);
    }
}
