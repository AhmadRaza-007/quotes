<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\QuoteComment;
use Illuminate\Http\Request;

class QuoteCommentController extends Controller
{
    public function comment()
    {
        try {
            $inputs = request()->validate([
                'quote_id' => 'required',
                'comment' => 'required',
            ]);

            $inputs['user_id'] = auth()->user()->id;

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

    public function getComment($quoteId)
    {
        try {
            $comments = QuoteComment::with(['user' => function ($query) {
                $query->select('id', 'name', 'avatar');
            }])->where('quote_id', $quoteId)->get();

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
