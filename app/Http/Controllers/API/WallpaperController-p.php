<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wallpaper;
use Illuminate\Http\Request;

class WallpaperController extends Controller
{
    public function index(Request $request, $user_id = 0)
    {
        try {
            $userId = $user_id;

            $wallpapers = Wallpaper::with('category')->paginate($request->count ?? 10);

            $wallpapers->getCollection()->transform(function ($wp) use ($userId) {
                // prepare fields for mobile response
                $wp->file_url = $wp->file_path ? url($wp->file_path) : null;
                $wp->thumbnail_url = $wp->thumbnail ? url($wp->thumbnail) : null;
                // include likes/favourites flags if you later add relationships
                return $wp;
            });

            return response()->json([
                'status' => 'success',
                'data' => $wallpapers
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $wp = Wallpaper::with('category')->findOrFail($id);
        $wp->file_url = $wp->file_path ? url($wp->file_path) : null;
        $wp->thumbnail_url = $wp->thumbnail ? url($wp->thumbnail) : null;
        return response()->json(['status' => 'success', 'data' => $wp], 200);
    }
}
