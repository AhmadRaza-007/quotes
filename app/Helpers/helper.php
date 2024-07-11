<?php

use App\Models\QuoteCategory;
use App\Models\Quote;
use App\Models\User;

if (!function_exists('getUsers')) {
    function getUsers()
    {
        return User::get();
    }
}

if (!function_exists('getCategories')) {
    function getCategories()
    {
        return QuoteCategory::get();
    }
}

if (!function_exists('getCategoryById')) {
    function getCategoryById($id)
    {
        return QuoteCategory::whereId($id)->get();
    }
}

if (!function_exists('getQuates')) {
    function getQuotes()
    {
        return Quote::get();
    }
}

if (!function_exists('getQuateById')) {
    function getQuoteById($id)
    {
        return Quote::whereId($id)->get();
    }
}
