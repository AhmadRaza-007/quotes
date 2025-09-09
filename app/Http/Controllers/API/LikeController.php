<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wallpaper;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LikeController extends Controller
{
    // POST /api/like
    public function like(Request $request)
    {
        // require authenticated user
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $request->validate([
            'wallpaper_id' => 'required|integer|exists:wallpapers,id',
        ]);

        $wallpaperId = (int)$request->input('wallpaper_id');

        try {
            // simple toggle: delete if exists else create
            $existing = Like::where('wallpaper_id', $wallpaperId)
                ->where('user_id', $user->id)
                ->first();

            if ($existing) {
                $existing->delete();
                $likeCount = Like::where('wallpaper_id', $wallpaperId)->count();
                return response()->json(['liked' => false, 'likeCount' => $likeCount], 200);
            }

            // create new like
            $like = new Like();
            $like->wallpaper_id = $wallpaperId;
            $like->user_id = $user->id;
            $like->save();

            $likeCount = Like::where('wallpaper_id', $wallpaperId)->count();
            return response()->json(['liked' => true, 'likeCount' => $likeCount], 201);
        } catch (\Throwable $e) {
            Log::error('Like error: ' . $e->getMessage());
            return response()->json(['message' => 'Server error'], 500);
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
