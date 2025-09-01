<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\WallpaperComment as QuoteComment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class QuoteCommentController extends Controller
{
    public function comment()
    {
        try {
            $inputs = request()->validate([
                'wallpaper_id' => 'required',
                'comment' => 'required',
            ]);

            $inputs['user_id'] = auth()->id();

            $comment = QuoteComment::create($inputs);

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
            $comments = QuoteComment::with(['user' => function ($query) {
                $query->select('id', 'name');
            }])->where('wallpaper_id', $wallpaperId)->latest()->get();

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
