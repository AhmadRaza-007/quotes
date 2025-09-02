<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_post_id', 'user_id', 'text'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profilePost()
    {
        return $this->belongsTo(ProfilePost::class);
    }
}
