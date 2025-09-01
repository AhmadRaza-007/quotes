<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wallpaper;
use App\Models\Like;
use App\Models\Favourite;
use Illuminate\Http\Request;

class WallpaperController extends Controller
{
    public function index(Request $request)
    {
        try {
            $wallpapers = Wallpaper::with('category')
                ->paginate($request->count ?? 10);

            $wallpapers->getCollection()->transform(function ($wp) {
                $wp->file_url = $wp->file_path ? url($wp->file_path) : null;
                $wp->thumbnail_url = $wp->thumbnail ? url($wp->thumbnail) : null;

                // Add interaction data if user is authenticated
                if (auth()->check()) {
                    $wp->is_liked = Like::where('wallpaper_id', $wp->id)
                        ->where('user_id', auth()->id())
                        ->exists();
                    $wp->is_favourite = Favourite::where('wallpaper_id', $wp->id)
                        ->where('user_id', auth()->id())
                        ->exists();
                } else {
                    $wp->is_liked = false;
                    $wp->is_favourite = false;
                }

                $wp->like_count = Like::where('wallpaper_id', $wp->id)->count();
                $wp->comment_count = $wp->comments()->count();

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
        try {
            $wp = Wallpaper::with(['category', 'comments.user'])->findOrFail($id);
            $wp->file_url = $wp->file_path ? url($wp->file_path) : null;
            $wp->thumbnail_url = $wp->thumbnail ? url($wp->thumbnail) : null;

            // Add interaction data if user is authenticated
            if (auth()->check()) {
                $wp->is_liked = Like::where('wallpaper_id', $wp->id)
                    ->where('user_id', auth()->id())
                    ->exists();
                $wp->is_favourite = Favourite::where('wallpaper_id', $wp->id)
                    ->where('user_id', auth()->id())
                    ->exists();
            } else {
                $wp->is_liked = false;
                $wp->is_favourite = false;
            }

            $wp->like_count = Like::where('wallpaper_id', $wp->id)->count();

            return response()->json([
                'status' => 'success',
                'data' => $wp
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Wallpaper not found'
            ], 404);
        }
    }
}
