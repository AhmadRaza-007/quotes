<?php

use App\Models\User;
use App\Models\Wallpaper;
use App\Models\WallpaperCategory;

if (!function_exists('getUsers')) {
    function getUsers()
    {
        return User::get();
    }
}

if (!function_exists('getUserCategories')) {
    function getUserCategories($userId)
    {
        return WallpaperCategory::where('user_id', $userId)->get();
    }
}

if (!function_exists('getAdminCategories')) {
    function getAdminCategories()
    {
        return WallpaperCategory::whereNull('user_id')->get();
    }
}

if (!function_exists('getCategories')) {
    function getCategories($userId = null)
    {
        if ($userId) {
            // return getUserCategories($userId);
            return WallpaperCategory::where('user_id', $userId)
                ->whereNotNull('parent_id')
                ->get();
        }
        // return getAdminCategories();
        return WallpaperCategory::whereNull('user_id')
            ->whereNotNull('parent_id')
            ->get();
    }
}

if (!function_exists('getParentCategories')) {
    function getParentCategories($userId = null)
    {
        if ($userId) {
            // return getUserCategories($userId);
            return WallpaperCategory::where('user_id', $userId)
                ->whereNull('parent_id')
                ->get();
        }
        // return getAdminCategories();
        return WallpaperCategory::whereNull('user_id')
            ->whereNull('parent_id')
            ->get();
    }
}

if (!function_exists('getCategoriesHierarchical')) {
    function getCategoriesHierarchical()
    {
        $categories = WallpaperCategory::with('children')->whereNull('parent_id')->get();
        $hierarchical = [];

        foreach ($categories as $category) {
            $hierarchical[] = [
                'id' => $category->id,
                'name' => $category->category_name,
                'children' => $category->children->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->category_name
                    ];
                })->toArray()
            ];
        }

        return $hierarchical;
    }
}

if (!function_exists('getCategoriesFlat')) {
    function getCategoriesFlat()
    {
        $categories = WallpaperCategory::orderBy('parent_id')->orderBy('order')->get();
        $flat = [];

        foreach ($categories as $category) {
            $prefix = $category->depth > 0 ? str_repeat('-- ', $category->depth) : '';
            $flat[$category->id] = $prefix . $category->category_name;
        }

        return $flat;
    }
}

if (!function_exists('getCategoryById')) {
    function getCategoryById($id)
    {
        return WallpaperCategory::whereId($id)->get();
    }
}

if (!function_exists('getWallpapers')) {
    function getWallpapers()
    {
        return Wallpaper::get();
    }
}

if (!function_exists('getQuotes')) {
    // Backwards compatibility: alias for wallpapers
    function getQuotes()
    {
        return Wallpaper::get();
    }
}

if (!function_exists('getQuoteById')) {
    function getQuoteById($id)
    {
        return Wallpaper::whereId($id)->get();
    }
}
