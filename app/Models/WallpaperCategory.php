<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class WallpaperCategory extends Model
// {
//     use HasFactory;

//     protected $fillable = [
//         'category_name',
//         'parent_id',
//         'user_id',
//         'depth',
//         'order',
//         'is_active',
//     ];

//     public function wallpapers()
//     {
//         return $this->hasMany(Wallpaper::class, 'category_id');
//     }

//     public function parent()
//     {
//         return $this->belongsTo(WallpaperCategory::class, 'parent_id');
//     }

//     public function children()
//     {
//         return $this->hasMany(WallpaperCategory::class, 'parent_id');
//     }

//     public function user()
//     {
//         return $this->belongsTo(User::class, 'user_id');
//     }

//     public function allChildren()
//     {
//         return $this->children()->with('allChildren');
//     }

//     // Scope for active categories
//     public function scopeActive($query)
//     {
//         return $query->where('is_active', true);
//     }

//     // Scope for root categories (no parent)
//     public function scopeRoot($query)
//     {
//         return $query->whereNull('parent_id');
//     }

//     // Get all descendants (useful for getting wallpapers from all subcategories)
//     public function getDescendants()
//     {
//         $descendants = collect();

//         foreach ($this->children as $child) {
//             $descendants->push($child);
//             $descendants = $descendants->merge($child->getDescendants());
//         }

//         return $descendants;
//     }

//     // Get all wallpapers including those from subcategories
//     public function getAllWallpapers()
//     {
//         $categoryIds = $this->getDescendants()->pluck('id')->prepend($this->id);
//         return Wallpaper::whereIn('category_id', $categoryIds)->get();
//     }
// }


// app/Models/WallpaperCategory.php


namespace App\Models;

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
        'owner_user_id',
        'is_admin'
    ];

    // Relationship with user who owns this category
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
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
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    // Get all category IDs including subcategories
    public function getAllCategoryIds()
    {
        $categoryIds = [$this->id];

        foreach ($this->children as $child) {
            $categoryIds = array_merge($categoryIds, $child->getAllCategoryIds());
        }

        return $categoryIds;
    }

    // In app/Models/WallpaperCategory.php

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeAdminCategories($query)
    {
        return $query->whereNull('owner_user_id');
    }

    public function scopeUserCategories($query, $userId = null)
    {
        if ($userId) {
            return $query->where('owner_user_id', $userId);
        }
        return $query->whereNotNull('owner_user_id');
    }
}
