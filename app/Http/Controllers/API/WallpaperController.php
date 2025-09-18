<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wallpaper;
use App\Models\Like;
use App\Models\Favourite;
use App\Models\ProfilePost;
use App\Models\WallpaperFavourite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class WallpaperController extends Controller
{
    public function index(Request $request)
    {
        try {
            // return $user = auth('sanctum')->user();
            // return Like::where('wallpaper_id', 31)
            //             ->where('user_id', $user->id)
            //             ->exists();
            $wallpapers = Wallpaper::with('category')
                ->latest()
                ->paginate($request->count ?? 10);

            $wallpapers->getCollection()->transform(function ($wp) {
                // $wp->file_url = $wp->file_path ? url($wp->file_path) : null;
                $wp->file_url = $wp->file_url;
                $wp->thumbnail_url = $wp->thumbnail ? url($wp->thumbnail) : null;
                // Legacy fields (will be ignored by new clients):
                $user = auth('sanctum')->user();
                if ($user) {
                    $wp->is_liked = Like::where('wallpaper_id', $wp->id)
                        ->where('user_id', $user->id)
                        ->exists();
                    $wp->is_favourite = WallpaperFavourite::where('wallpaper_id', $wp->id)
                        ->where('user_id', $user->id)
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

            $user = auth('sanctum')->user();
            if ($user) {
                $wp->is_liked = Like::where('wallpaper_id', $wp->id)
                    ->where('user_id', $user->id)
                    ->exists();
                $wp->is_favourite = Favourite::where('wallpaper_id', $wp->id)
                    ->where('user_id', $user->id)
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

        // Process image with Intervention Image: auto-resize & crop to required dimensions
        $imageFile = $request->file('file');
        $requiredWidth = 1080; // portrait width
        $requiredHeight = 1920; // portrait height

        // create intervention image and fit to required size, maintaining aspect ratio by cropping
        $img = Image::make($imageFile->getRealPath())->orientate()->fit($requiredWidth, $requiredHeight);

        // choose stored extension jpg for consistency
        $fileName = time() . '_' . Str::random(6) . '.jpg';
        $storePath = 'uploads/wallpapers/' . $fileName;

        Storage::disk('public')->put($storePath, (string) $img->encode('jpg', 90));

        // create thumbnail (smaller version)
        $thumbImg = $img->resize(360, 640, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $thumbName = 'thumb_' . time() . '_' . Str::random(6) . '.jpg';
        $thumbPath = 'uploads/wallpapers/thumbnails/' . $thumbName;
        Storage::disk('public')->put($thumbPath, (string) $thumbImg->encode('jpg', 80));

        $wallpaper = Wallpaper::create([
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'file_path' => 'storage/' . $storePath,
            'media_type' => 'image',
            'owner_user_id' => $user->id,
            'is_admin' => 1,
            'thumbnail' => 'storage/' . $thumbPath,
        ]);

        // Create a profile post owned by admin referencing this wallpaper
        ProfilePost::firstOrCreate([
            'owner_user_id' => $user->id,
            'wallpaper_id' => $wallpaper->id,
        ]);

        return response()->json($wallpaper, 201);
    }

    // Authenticated users can upload wallpapers for their profile (creates a ProfilePost)
    public function userUpload(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:jpg,jpeg,png,gif,webp',
            'category_id' => 'nullable|exists:wallpaper_categories,id',
        ]);

        // Process image with Intervention Image: auto-resize & crop to required dimensions for user uploads
        $imageFile = $request->file('file');
        $requiredWidth = 1080;
        $requiredHeight = 1920;

        $img = Image::make($imageFile->getRealPath())->orientate()->fit($requiredWidth, $requiredHeight);

        $fileName = time() . '_' . Str::random(6) . '.jpg';
        $storePath = 'uploads/wallpapers/' . $fileName;
        Storage::disk('public')->put($storePath, (string) $img->encode('jpg', 90));

        // thumbnail
        $thumbImg = $img->resize(360, 640, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $thumbName = 'thumb_' . time() . '_' . Str::random(6) . '.jpg';
        $thumbPath = 'uploads/wallpapers/thumbnails/' . $thumbName;
        Storage::disk('public')->put($thumbPath, (string) $thumbImg->encode('jpg', 80));

        $wallpaper = Wallpaper::create([
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'file_path' => 'storage/' . $storePath,
            'media_type' => 'image',
            'owner_user_id' => $user->id,
            'is_admin' => 0,
            'thumbnail' => 'storage/' . $thumbPath,
        ]);

        // Create a profile post owned by the user referencing this wallpaper
        $post = ProfilePost::firstOrCreate([
            'owner_user_id' => $user->id,
            'wallpaper_id' => $wallpaper->id,
        ], [
            'caption' => null,
        ]);

        return response()->json(['wallpaper' => $wallpaper, 'profile_post' => $post], 201);
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
            $imageFile = $request->file('file');
            $requiredWidth = 1080;
            $requiredHeight = 1920;

            $img = Image::make($imageFile->getRealPath())->orientate()->fit($requiredWidth, $requiredHeight);
            $fileName = time() . '_' . Str::random(6) . '.jpg';
            $storePath = 'uploads/wallpapers/' . $fileName;
            Storage::disk('public')->put($storePath, (string) $img->encode('jpg', 90));

            // create thumbnail
            $thumbImg = $img->resize(360, 640, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $thumbName = 'thumb_' . time() . '_' . Str::random(6) . '.jpg';
            $thumbPath = 'uploads/wallpapers/thumbnails/' . $thumbName;
            Storage::disk('public')->put($thumbPath, (string) $thumbImg->encode('jpg', 80));

            // delete old files? (optional)
            $wallpaper->file_path = 'storage/' . $storePath;
            $wallpaper->thumbnail = 'storage/' . $thumbPath;
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
