<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wallpaper;
use App\Models\Like;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function like(Request $request)
    {
        try {
            $request->validate([
                'wallpaper_id' => 'required',
            ]);

            $wallpaper = Wallpaper::find($request->wallpaper_id);

            if (!$wallpaper) {
                return response()->json([
                    'message' => 'Wallpaper not found',
                ], 404);
            }

            $like = Like::where('wallpaper_id', $request->wallpaper_id)
                ->where('user_id', auth()->user()->id ?? 1)
                ->first();

            if ($like) {
                $like->delete();
                return response()->json([
                    'message' => 'Wallpaper like removed',
                    'liked' => false,
                ], 200);
            }

            $like = new Like();
            $like->wallpaper_id = $request->wallpaper_id;
            $like->user_id = auth()->user()->id ?? 1;
            $like->save();

            return response()->json([
                'message' => 'Wallpaper liked successfully',
                'liked' => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getLikedByUser($wallpaperId)
    {
        try {
            $liked = Like::where('wallpaper_id', $wallpaperId)
                ->where('user_id', auth()->user()->id ?? 1)
                ->count();

            return response()->json([
                'status' => 'success',
                'liked' => $liked > 0,
                'likeCount' => Like::where('wallpaper_id', $wallpaperId)->count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
