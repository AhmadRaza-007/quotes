<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wallpaper;
use App\Models\WallpaperComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    // POST /api/comment  (protected)
    public function comment(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'wallpaper_id' => 'required|integer|exists:wallpapers,id',
            'comment' => 'required|string|max:1000',
        ]);

        try {
            $validated['user_id'] = $user->id;

            $comment = WallpaperComment::create($validated);

            return response()->json([
                'status' => 'success',
                'comment' => $comment,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('CommentController::comment error: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'wallpaper_id' => $request->input('wallpaper_id'),
            ]);
            return response()->json(['status' => 'error', 'message' => 'Server error'], 500);
        }
    }

    // GET /api/get-comment/{wallpaperId}  (public)
    public function getComment($wallpaperId)
    {
        try {
            $comments = WallpaperComment::with(['user' => function ($query) {
                $query->select('id', 'name', 'avatar');
            }])
                ->where('wallpaper_id', (int)$wallpaperId)
                ->latest()
                ->get();

            return response()->json([
                'status' => 'success',
                'comments' => $comments,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('CommentController::getComment error: ' . $e->getMessage(), [
                'wallpaper_id' => $wallpaperId,
            ]);
            return response()->json(['status' => 'error', 'message' => 'Server error'], 500);
        }
    }
}
