<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiKeyApp extends Model
{
    use HasFactory;
    protected $table = 'api_key_app';

    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function apiKeys()
    {
        return $this->hasMany(ApiKey::class, 'app_id');
    }
}
