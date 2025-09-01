<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\WallpaperComment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function comment(Request $request)
    {
        try {
            $inputs = $request->validate([
                'wallpaper_id' => 'required',
                'comment' => 'required',
            ]);

            $inputs['user_id'] = auth()->user()->id;

            $comment = WallpaperComment::create($inputs);

            return response()->json([
                'status' => 'success',
                'comment' => $comment
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getComment($wallpaperId)
    {
        try {
            $comments = WallpaperComment::with(['user' => function ($query) {
                $query->select('id', 'name', 'avatar');
            }])->where('wallpaper_id', $wallpaperId)
              ->latest()
              ->get();

            return response()->json([
                'status' => 'success',
                'comments' => $comments
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
