<?php

use App\Models\QuoteCategory;
use App\Models\User;
use App\Models\Wallpaper;
use App\Models\WallpaperCategory;

if (!function_exists('getUsers')) {
    function getUsers()
    {
        return User::get();
    }
}

if (!function_exists('getCategories')) {
    function getCategories()
    {
        return WallpaperCategory::get();
    }
}

if (!function_exists('getCategoryById')) {
    function getCategoryById($id)
    {
        return QuoteCategory::whereId($id)->get();
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
