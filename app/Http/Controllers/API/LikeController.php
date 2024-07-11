<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\QuoteLike;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function like(Request $request)
    {
        try {
            $request->validate([
                'quote_id' => 'required',
            ]);

            $quote = Quote::find($request->quote_id);

            if (!$quote) {
                return response()->json([
                  'message' => 'quote not found',
                ], 200);
            }

            $like = QuoteLike::where('quote_id', $request->quote_id)->first();
            if ($like) {
                $like->delete();
                return response()->json([
                    'message' => 'quote like removed',
                    'like' => $like,
                ], 200);
            }

            $like = new QuoteLike();
            $like->quote_id = $request->quote_id;
            $like->user_id = auth()->user()->id;
            $like->save();

            return response()->json([
                'message' => 'quote liked successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getLikedByUser($quoteId)
    {
        try {
            $likedQuotes = QuoteLike::where('quote_id', $quoteId)->where('user_id', auth()->user()->id)->get();

            return response()->json([
                'status' => 'success',
                'quoteLikes' => $likedQuotes->count(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
