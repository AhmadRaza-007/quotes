<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wallpaper;
use App\Models\WallpaperFavourite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FavouriteController extends Controller
{
    // POST /api/favourite  (toggle favourite) (protected)
    public function favourite(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Backward compatibility: accept quote_id as alias for wallpaper_id
        if ($request->has('quote_id') && ! $request->has('wallpaper_id')) {
            $request->merge(['wallpaper_id' => $request->quote_id]);
        }
        $validated = $request->validate([
            'wallpaper_id' => 'required|integer|exists:wallpapers,id',
        ]);

        $wallpaperId = (int) $validated['wallpaper_id'];

        try {
            // check existing
            $existing = WallpaperFavourite::where('wallpaper_id', $wallpaperId)
                ->where('user_id', $user->id)
                ->first();

            if ($existing) {
                $existing->delete();
                return response()->json([
                    'message' => 'wallpaper favourite removed',
                    'favourited' => false,
            ], 200);
        }

            $fav = WallpaperFavourite::create([
                'wallpaper_id' => $wallpaperId,
                'user_id' => $user->id,
            ]);
            return response()->json([
                'message' => 'wallpaper favourited successfully',
                'favourited' => true,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('FavouriteController::favourite error: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'wallpaper_id' => $wallpaperId ?? null,
            ]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    // GET /api/get-favourites  (protected) - returns user's favourites
    public function getFavourite(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
}

        try {
            $favourites = WallpaperFavourite::where('user_id', $user->id)->get();
            return response()->json(['favourites' => $favourites], 200);
        } catch (\Throwable $e) {
            Log::error('FavouriteController::getFavourite error: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
            ]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    // GET /api/get-favourite/{wallpaperId}  (protected) - checks if current user favourited wallpaper
    public function getFavouriteByUser(Request $request, $wallpaperId)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        try {
            $exists = WallpaperFavourite::where('wallpaper_id', (int)$wallpaperId)
                ->where('user_id', $user->id)
                ->exists();

            return response()->json([
                'favourited' => $exists,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('FavouriteController::getFavouriteByUser error: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'wallpaper_id' => $wallpaperId,
            ]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}

