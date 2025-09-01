<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wallpaper;
use App\Models\WallpaperFavourite;
use Illuminate\Http\Request;

class FavouriteController extends Controller
{
    public function favourite(Request $request)
    {
        try {
            // Backward compatibility: accept quote_id as alias for wallpaper_id
            if ($request->has('quote_id') && !$request->has('wallpaper_id')) {
                $request->merge(['wallpaper_id' => $request->quote_id]);
            }

            $request->validate([
                'wallpaper_id' => 'required',
            ]);

            $wallpaper = Wallpaper::find($request->wallpaper_id);

            if (!$wallpaper) {
                return response()->json([
                  'message' => 'wallpaper not found',
                ], 400);
            }

            $existing = WallpaperFavourite::where('wallpaper_id', $request->wallpaper_id)
                ->where('user_id', auth()->user()->id)
                ->first();
            if ($existing) {
                $existing->delete();
                return response()->json([
                    'message' => 'wallpaper favourite removed',
                    'favourite' => $existing,
                ], 200);
            }

            $fav = new WallpaperFavourite();
            $fav->wallpaper_id = $request->wallpaper_id;
            $fav->user_id = auth()->user()->id;
            $fav->save();

            return response()->json([
                'message' => 'wallpaper favourited successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getFavourite(){
        try {
            $favourites = WallpaperFavourite::where('user_id', auth()->user()->id)->get();
            return response()->json([
                'favourites' => $favourites,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getFavouriteByUser($wallpaperId){
        try {
            $favourites = WallpaperFavourite::where('wallpaper_id', $wallpaperId)
                ->where('user_id', auth()->user()->id)
                ->get();
            return response()->json([
                'favouriteQuotes' => $favourites, // keeping key for backward compatibility
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
