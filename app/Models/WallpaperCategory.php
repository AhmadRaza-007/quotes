<?php

namespace App\Models;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WallpaperCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_name',
        'parent_id',
        'order',
        'depth',
        'user_id',
        'is_admin',
        'is_active',
    ];

    // Relationship with user who owns this category
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship with wallpapers in this category
    public function wallpapers()
    {
        return $this->hasMany(Wallpaper::class, 'category_id');
    }

    // Relationship with child categories
    public function children()
    {
        return $this->hasMany(WallpaperCategory::class, 'parent_id');
    }

    // Relationship with parent category
    public function parent()
    {
        return $this->belongsTo(WallpaperCategory::class, 'parent_id');
    }

    // Get all wallpapers including those in subcategories
    public function getAllWallpapers()
    {
        $wallpapers = $this->wallpapers;

        foreach ($this->children as $child) {
            $wallpapers = $wallpapers->merge($child->getAllWallpapers());
        }

        return $wallpapers;
    }

    // Get all wallpapers including subcategories with pagination
    public function getAllWallpapersPaginated($perPage = 20)
    {
        // Get all category IDs including subcategories
        $categoryIds = $this->getAllCategoryIds();

        // Return paginated wallpapers from all categories
        return Wallpaper::whereIn('category_id', $categoryIds)
            ->with(['category' => function ($query) {
                $query->with('parent');
            }])
            ->inRandomOrder()
            ->paginate($perPage);
    }

    // Get all category IDs including subcategories
    public function getAllCategoryIds()
    {
        if ($this->is_active != 1) {
            return [];
        }

        $categoryIds = [$this->id];

        foreach ($this->children as $child) {
            $categoryIds = array_merge($categoryIds, $child->getAllCategoryIds());
        }

        return $categoryIds;
    }

    // In app/Models/WallpaperCategory.php

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeAdminCategories($query)
    {
        return $query->whereNull('user_id');
    }

    public function scopeUserCategories($query, $userId = null)
    {
        if ($userId) {
            return $query->where('user_id', $userId);
        }
        return $query->whereNotNull('user_id');
    }
}
