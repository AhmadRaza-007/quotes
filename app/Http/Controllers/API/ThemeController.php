<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\Theme;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function index()
    {
        $themes = Theme::with('category')->paginate(10);
        return view('admin.theme', compact('themes'));
    }
}
