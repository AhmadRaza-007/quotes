<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function wallpapers()
    {
        return $this->hasMany(Wallpaper::class, 'owner_user_id');
    }

    // app/Models/User.php
    public function categories()
    {
        return $this->hasMany(WallpaperCategory::class, 'owner_user_id');
    }

    // API Keys relationship
    public function apiKeys()
    {
        return $this->hasMany(ApiKey::class);
    }

    /**
     * Generate a new API key for the user
     */
    public function createApiKey($name, $expiresAt = null)
    {
        $key = Str::random(64);

        return $this->apiKeys()->create([
            'name' => $name,
            'key' => $key,
            'expires_at' => $expiresAt,
        ]);
    }
}
