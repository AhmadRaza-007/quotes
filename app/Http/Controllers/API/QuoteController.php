<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wallpaper;
use App\Models\WallpaperCategory;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function wallpapers(Request $request, $user_id = 0)
    {
        try {
            $wallpapers = Wallpaper::with('category')->paginate($request->count ?? 10);

            $wallpapers->getCollection()->transform(function ($wp) {
                $wp->file_url = $wp->file_path ? url($wp->file_path) : null;
                $wp->thumbnail_url = $wp->thumbnail ? url($wp->thumbnail) : null;
                return $wp;
            });

            return response()->json([
                'status' => 'success',
                'data' => $wallpapers
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

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
