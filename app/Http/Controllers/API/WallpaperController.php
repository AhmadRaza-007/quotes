<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wallpaper;
use App\Models\Like;
use App\Models\Favourite;
use App\Models\ProfilePost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WallpaperController extends Controller
{
    public function index(Request $request)
    {
        try {
            $wallpapers = Wallpaper::with('category')
                ->latest()
                ->paginate($request->count ?? 10);

            $wallpapers->getCollection()->transform(function ($wp) {
                $wp->file_url = $wp->file_path ? url($wp->file_path) : null;
                $wp->thumbnail_url = $wp->thumbnail ? url($wp->thumbnail) : null;
                // Legacy fields (will be ignored by new clients):
                if (auth()->check()) {
                    $wp->is_liked = Like::where('wallpaper_id', $wp->id)
                        ->where('user_id', auth()->id())
                        ->exists();
                    $wp->is_favourite = Favourite::where('wallpaper_id', $wp->id)
                        ->where('user_id', auth()->id())
                        ->exists();
                } else {
                    $wp->is_liked = false;
                    $wp->is_favourite = false;
                }
                $wp->like_count = Like::where('wallpaper_id', $wp->id)->count();
                $wp->comment_count = $wp->comments()->count();
                return $wp;
            });

            return response()->json([
                'status' => 'success',
                'data' => $wallpapers
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $wp = Wallpaper::with(['category', 'comments.user'])->findOrFail($id);
            $wp->file_url = $wp->file_path ? url($wp->file_path) : null;
            $wp->thumbnail_url = $wp->thumbnail ? url($wp->thumbnail) : null;

            if (auth()->check()) {
                $wp->is_liked = Like::where('wallpaper_id', $wp->id)
                    ->where('user_id', auth()->id())
                    ->exists();
                $wp->is_favourite = Favourite::where('wallpaper_id', $wp->id)
                    ->where('user_id', auth()->id())
                    ->exists();
            } else {
                $wp->is_liked = false;
                $wp->is_favourite = false;
            }

            $wp->like_count = Like::where('wallpaper_id', $wp->id)->count();

            return response()->json([
                'status' => 'success',
                'data' => $wp
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Wallpaper not found'
            ], 404);
        }
    }

    // Admin-only: upload wallpaper and create an admin-owned ProfilePost reference
    public function store(Request $request)
    {
        $user = $request->user();
        abort_unless($user && isset($user->user_type) && (int)$user->user_type === 1, 403, 'Admin only');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:jpg,jpeg,png,gif,webp',
            'category_id' => 'nullable|exists:wallpaper_categories,id',
        ]);

        $path = $request->file('file')->store('uploads/wallpapers', 'public');

        $wallpaper = Wallpaper::create([
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'file_path' => 'storage/' . $path,
            'media_type' => 'image',
        ]);

        // Create a profile post owned by admin referencing this wallpaper
        ProfilePost::firstOrCreate([
            'owner_user_id' => $user->id,
            'wallpaper_id' => $wallpaper->id,
        ]);

        return response()->json($wallpaper, 201);
    }

    // Admin-only: update wallpaper metadata
    public function update($id, Request $request)
    {
        $user = $request->user();
        abort_unless($user && isset($user->user_type) && (int)$user->user_type === 1, 403, 'Admin only');

        $wallpaper = Wallpaper::findOrFail($id);
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|nullable|exists:wallpaper_categories,id',
            'file' => 'sometimes|file|mimes:jpg,jpeg,png,gif,webp',
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('uploads/wallpapers', 'public');
            $wallpaper->file_path = 'storage/' . $path;
        }

        if (array_key_exists('title', $validated)) $wallpaper->title = $validated['title'];
        if (array_key_exists('category_id', $validated)) $wallpaper->category_id = $validated['category_id'];
        $wallpaper->save();

        return response()->json($wallpaper);
    }

    // Admin-only: delete wallpaper and cascade profile posts (via FK constraints if set)
    public function destroy($id, Request $request)
    {
        $user = $request->user();
        abort_unless($user && isset($user->user_type) && (int)$user->user_type === 1, 403, 'Admin only');

        $wallpaper = Wallpaper::findOrFail($id);
        $wallpaper->delete();
        return response()->json([], 204);
    }
}
