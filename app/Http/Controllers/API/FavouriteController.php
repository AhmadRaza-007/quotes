<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Favourite;
use App\Models\Quote;
use App\Models\QuoteFavourite;
use Illuminate\Http\Request;

class FavouriteController extends Controller
{
    public function favourite(Request $request)
    {
        try {
            $request->validate([
                'quote_id' => 'required',
            ]);

            $quote = Quote::find($request->quote_id);

            if (!$quote) {
                return response()->json([
                  'message' => 'quote not found',
                ], 400);
            }

            $favourite = quoteFavourite::where('quote_id', $request->quote_id)->first();
            if ($favourite) {
                $favourite->delete();
                return response()->json([
                    'message' => 'quote favourite removed',
                    'favourite' => $favourite,

                ], 200);
            }

            $like = new quoteFavourite();
            $like->quote_id = $request->quote_id;
            $like->user_id = auth()->user()->id;
            $like->save();

            return response()->json([
                'message' => 'quote favourite successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getFavourite(){
        try {
            $favourites = quoteFavourite::where('user_id', auth()->user()->id)->get();
            return response()->json([
                'favourites' => $favourites,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getFavouriteByUser($quoteId){
        try {
            $favourites = quoteFavourite::where('quote_id', $quoteId)->where('user_id', auth()->user()->id)->get();
            return response()->json([
                'favouriteQuotes' => $favourites,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
