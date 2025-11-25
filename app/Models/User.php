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
        'first_name',
        'last_name',
        'email',
        'password',
        'avatar',
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
        return $this->hasMany(Wallpaper::class, 'user_id');
    }

    // app/Models/User.php
    public function categories()
    {
        return $this->hasMany(WallpaperCategory::class, 'user_id');
    }

    // API Keys relationship
    public function apiKeys()
    {
        return $this->hasMany(ApiKey::class);
    }

    public function apiKeyCategories()
    {
        return $this->hasMany(ApiKeyApp::class);
    }

    public function createApiKey($name, $expiresAt = null, $categoryId = null)
    {
        $key = Str::random(64);

        return $this->apiKeys()->create([
            'name' => $name,
            'key' => $key,
            'expires_at' => $expiresAt,
            'category_id' => $categoryId,
        ]);
    }

    // User devices relationship for push notifications
    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }
}
