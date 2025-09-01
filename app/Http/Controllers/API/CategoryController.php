<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\WallpaperCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function categories(Request $request)
    {
        try {
            $categories = WallpaperCategory::get();
            return response()->json([
                'status' => 'success',
                'data' => $categories,
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function categoriesWithWallpapers($id = null)
    {
        try {
            if (isset($id)) {
                $categories = WallpaperCategory::with('wallpapers')->whereId($id)->get();
            } else {
                $categories = WallpaperCategory::with('wallpapers')->get();
            }
            return response()->json([
                'status' => 'success',
                'data' => $categories,
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }
}
