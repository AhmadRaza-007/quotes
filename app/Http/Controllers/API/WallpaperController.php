<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wallpaper;
use App\Models\Like;
use App\Models\Favourite;
use App\Models\ProfilePost;
use App\Models\WallpaperCategory;
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
            $wallpapers = Wallpaper::with(['category', 'category.parent'])
                ->fromActiveCategories() // Add this scope
                // ->latest()
                ->inRandomOrder()
                ->paginate($request->count ?? 10);

            $wallpapers->getCollection()->transform(function ($wp) {
                // $wp->file_url = $wp->file_path ? url($wp->file_path) : null;
                $wp->file_url = $wp->file_path;
                // $wp->thumbnail_url = $wp->thumbnail ? url($wp->thumbnail) : null;
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

    // public function show($id)
    // {
    //     try {
    //         $wp = Wallpaper::with(['category', 'category.parent', 'comments.user'])->findOrFail($id);
    //         $wp->file_url = $wp->file_path;
    //         $wp->thumbnail_url = $wp->thumbnail_url;

    //         $user = auth('sanctum')->user();
    //         if ($user) {
    //             $wp->is_liked = Like::where('wallpaper_id', $wp->id)
    //                 ->where('user_id', $user->id)
    //                 ->exists();
    //             $wp->is_favourite = Favourite::where('wallpaper_id', $wp->id)
    //                 ->where('user_id', $user->id)
    //                 ->exists();
    //         } else {
    //             $wp->is_liked = false;
    //             $wp->is_favourite = false;
    //         }

    //         $wp->like_count = Like::where('wallpaper_id', $wp->id)->count();

    //         // Get related wallpapers (same category, excluding current wallpaper)
    //         $relatedWallpapers = Wallpaper::where('category_id', $wp->category_id)
    //             ->where('id', '!=', $wp->id)
    //             ->fromActiveCategories() // Add this scope
    //             ->with('category')
    //             ->latest()
    //             ->limit(6)
    //             ->get()
    //             ->map(function ($relatedWp) use ($user) {
    //                 $relatedWp->file_url = $relatedWp->file_path;
    //                 $relatedWp->thumbnail_url = $relatedWp->thumbnail_url;

    //                 if ($user) {
    //                     $relatedWp->is_liked = Like::where('wallpaper_id', $relatedWp->id)
    //                         ->where('user_id', $user->id)
    //                         ->exists();
    //                     $relatedWp->is_favourite = Favourite::where('wallpaper_id', $relatedWp->id)
    //                         ->where('user_id', $user->id)
    //                         ->exists();
    //                 } else {
    //                     $relatedWp->is_liked = false;
    //                     $relatedWp->is_favourite = false;
    //                 }

    //                 $relatedWp->like_count = Like::where('wallpaper_id', $relatedWp->id)->count();
    //                 $relatedWp->comment_count = $relatedWp->comments()->count();

    //                 return $relatedWp;
    //             });

    //         return response()->json([
    //             'status' => 'success',
    //             'data' => [
    //                 'wallpaper' => $wp,
    //                 'related_wallpapers' => $relatedWallpapers
    //             ]
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Wallpaper not found'
    //         ], 404);
    //     }
    // }

    public function show(Request $request)
    {
        try {
            $request->validate([
                'wallpaper_id' => 'required|exists:wallpapers,id',
            ]);
            $user = auth('sanctum')->user();

            // Pagination input
            $perPage = $request->get('count', 15);
            $page = $request->get('page', 1);

            // Fetch selected wallpaper
            $selected = Wallpaper::with(['category', 'category.parent'])->findOrFail($request->wallpaper_id);

            // Format selected wallpaper
            $selected = $this->formatWallpaper($selected, $user);

            // Fetch paginated related wallpapers
            $query = Wallpaper::where('category_id', $selected->category_id)
                ->where('id', '!=', $selected->id)
                ->fromActiveCategories()
                ->with(['category', 'category.parent'])
                ->latest();

            $paginated = $query->paginate($perPage, ['*'], 'page', $page);

            // Format each related wallpaper
            $related = $paginated->getCollection()->map(function ($w) use ($user) {
                return $this->formatWallpaper($w, $user);
            });

            // Combine selected wallpaper + related wallpapers
            $wallpapers = collect([$selected])->merge($related);

            return response()->json([
                "status" => "success",
                "data" => [
                    "category" => [
                        "id" => $selected->category->id,
                        "category_name" => $selected->category->category_name,
                        "order" => $selected->category->order,
                        "is_active" => $selected->category->is_active,
                    ],
                    "wallpapers" => $wallpapers,
                    "pagination" => [
                        "current_page" => $paginated->currentPage(),
                        "last_page" => $paginated->lastPage(),
                        "per_page" => $paginated->perPage(),
                        "total" => $paginated->total(),
                        "from" => $paginated->firstItem(),
                        "to" => $paginated->lastItem(),
                    ]
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Wallpaper not found"
            ], 404);
        }
    }


    // Helper method to standardize wallpaper response
    private function formatWallpaper($wp, $user)
    {
        $wp->file_url = $wp->file_path;
        $wp->thumbnail_url = $wp->thumbnail_path;

        $wp->is_liked = $user
            ? Like::where('wallpaper_id', $wp->id)->where('user_id', $user->id)->exists()
            : false;

        $wp->is_favourite = $user
            ? Favourite::where('wallpaper_id', $wp->id)->where('user_id', $user->id)->exists()
            : false;

        $wp->like_count = Like::where('wallpaper_id', $wp->id)->count();
        $wp->comment_count = $wp->comments()->count();

        return $wp;
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
            'user_id' => $user->id,
            'is_admin' => 1,
            'thumbnail' => 'storage/' . $thumbPath,
        ]);

        // Create a profile post owned by admin referencing this wallpaper
        ProfilePost::firstOrCreate([
            'user_id' => $user->id,
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
            'user_id' => $user->id,
            'is_admin' => 0,
            'thumbnail' => 'storage/' . $thumbPath,
        ]);

        // Create a profile post owned by the user referencing this wallpaper
        $post = ProfilePost::firstOrCreate([
            'user_id' => $user->id,
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

    // Get wallpapers by user ID
    public function getWallpapersByUser(Request $request, $userId)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $page = $request->get('page', 1);

            // Get wallpapers for the specified user
            $wallpapers = Wallpaper::where('user_id', $userId)
                ->fromActiveCategories() // Add this scope
                ->with(['category', 'category.parent'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            $wallpapers->getCollection()->transform(function ($wp) {
                $wp->file_url = $wp->file_path;

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
                'data' => $wallpapers->items(),
                'pagination' => [
                    'current_page' => $wallpapers->currentPage(),
                    'per_page' => $wallpapers->perPage(),
                    'total' => $wallpapers->total(),
                    'last_page' => $wallpapers->lastPage(),
                    'from' => $wallpapers->firstItem(),
                    'to' => $wallpapers->lastItem(),
                ]
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function getCategoriessByUser(Request $request, $userId)
    {
        try {
            // Get distinct category IDs from wallpapers uploaded by the user
            // $categoryIds = Wallpaper::where('user_id', $userId)
            //     ->distinct()
            //     ->pluck('category_id')
            //     ->filter(); // Remove nulls

            // // Fetch categories
            $categories = WallpaperCategory::where('user_id', $userId)
                ->with('wallpapers')
                ->where('is_active', 1)
                // ->orderBy('order', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $categories
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function getCategoriessByUserId(Request $request)
    {
        try {
            $userId = $request->get('user_id');
            if (!$userId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'user_id parameter is required.'
                ], 400);
            }

            // Get the limit, default to 5 if not provided
            $wallpaperLimit = $request->get('wallpaper_limit', 5);

            // 1. Fetch categories
            $categories = WallpaperCategory::where('user_id', $userId)
                ->where('is_active', 1)
                ->get();

            // 2. Map over categories to fetch a limited number of wallpapers for each
            $categories = $categories->map(function ($category) use ($wallpaperLimit) {

                // --- Optimized Wallpaper Fetching within the map() ---
                // Use the relationship (assuming it's named 'wallpapers')
                // to build a query, apply limits, and then execute it.
                $category->wallpapers = $category->wallpapers()
                    ->latest('id') // Order by latest, or whatever criteria you need
                    ->limit($wallpaperLimit)
                    ->get();

                // If you need the `children` relationship as well, ensure it's loaded here
                // $category->children = $category->children;

                return $category;
            });

            return response()->json([
                'status' => 'success',
                'data' => $categories
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    // Search wallpapers by id or by title (q). Returns paginated results when searching by title.
    // public function search(Request $request)
    // {
    //     try {
    //         $q = $request->get('q');
    //         $id = $request->get('id');
    //         $perPage = $request->get('per_page', 15);

    //         $user = auth('sanctum')->user();

    //         // If id supplied, return single wallpaper with details (similar to show)
    //         if ($id) {
    //             $wp = Wallpaper::with(['category', 'comments.user'])->findOrFail($id);
    //             $wp->file_url = $wp->file_path ? url($wp->file_path) : null;
    //             $wp->thumbnail_url = $wp->thumbnail ? url($wp->thumbnail) : null;

    //             if ($user) {
    //                 $wp->is_liked = Like::where('wallpaper_id', $wp->id)
    //                     ->where('user_id', $user->id)
    //                     ->exists();
    //                 $wp->is_favourite = WallpaperFavourite::where('wallpaper_id', $wp->id)
    //                     ->where('user_id', $user->id)
    //                     ->exists();
    //             } else {
    //                 $wp->is_liked = false;
    //                 $wp->is_favourite = false;
    //             }

    //             $wp->like_count = Like::where('wallpaper_id', $wp->id)->count();

    //             return response()->json([
    //                 'status' => 'success',
    //                 'data' => $wp
    //             ], 200);
    //         }

    //         // Otherwise search by query string on title
    //         // $query = Wallpaper::with('category')->latest();
    //         $query = Wallpaper::with('category')
    //             ->fromActiveCategories() // Add this scope
    //             ->latest();
    //         if ($q) {
    //             $query->where('title', 'like', '%' . $q . '%');
    //         }

    //         $wallpapers = $query->paginate($perPage);

    //         $wallpapers->getCollection()->transform(function ($wp) use ($user) {
    //             $wp->file_url = $wp->file_url;

    //             if ($user) {
    //                 $wp->is_liked = Like::where('wallpaper_id', $wp->id)
    //                     ->where('user_id', $user->id)
    //                     ->exists();
    //                 $wp->is_favourite = WallpaperFavourite::where('wallpaper_id', $wp->id)
    //                     ->where('user_id', $user->id)
    //                     ->exists();
    //             } else {
    //                 $wp->is_liked = false;
    //                 $wp->is_favourite = false;
    //             }

    //             $wp->like_count = Like::where('wallpaper_id', $wp->id)->count();
    //             $wp->comment_count = $wp->comments()->count();

    //             return $wp;
    //         });

    //         return response()->json([
    //             'status' => 'success',
    //             'data' => $wallpapers
    //         ], 200);
    //     } catch (\Exception $exception) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $exception->getMessage()
    //         ], 500);
    //     }
    // }

    public function search(Request $request)
    {
        try {
            $q = trim($request->get('q'));
            $perPage = $request->get('per_page', 15);

            if (!$q) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Search query is required.'
                ], 400);
            }

            $user = auth('sanctum')->user();

            /*
        |--------------------------------------------------------------------------
        | CASE 1 → NUMERIC SEARCH (WALLPAPER ID)
        |--------------------------------------------------------------------------
        */
            if (ctype_digit($q)) {

                $wallpaper = Wallpaper::where('id', $q)
                    ->with(['category', 'category.parent'])
                    ->first();

                if (!$wallpaper) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Wallpaper not found.'
                    ], 404);
                }

                // Attach URLs
                $wallpaper->file_url = $wallpaper->file_path;
                $wallpaper->thumbnail_url = $wallpaper->thumbnail_url;

                // User-specific flags
                if ($user) {
                    $wallpaper->is_liked = Like::where('wallpaper_id', $wallpaper->id)
                        ->where('user_id', $user->id)
                        ->exists();

                    $wallpaper->is_favourite = WallpaperFavourite::where('wallpaper_id', $wallpaper->id)
                        ->where('user_id', $user->id)
                        ->exists();
                } else {
                    $wallpaper->is_liked = false;
                    $wallpaper->is_favourite = false;
                }

                // Counts
                $wallpaper->like_count = Like::where('wallpaper_id', $wallpaper->id)->count();
                $wallpaper->comment_count = $wallpaper->comments()->count();

                // Response
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'wallpapers' => [$wallpaper]
                    ]
                ]);
            }

            /*
        |--------------------------------------------------------------------------
        | CASE 2 → TEXT SEARCH (CATEGORY NAME)
        |--------------------------------------------------------------------------
        */

            $category = WallpaperCategory::where('is_active', 1)
                ->where('category_name', 'like', '%' . $q . '%')
                ->first();

            if (!$category) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Category not found.'
                ], 404);
            }

            // Get all active category + subcategory IDs
            $categoryIds = $category->getAllCategoryIds();

            // Fetch wallpapers inside this category tree
            $wallpapers = Wallpaper::whereIn('category_id', $categoryIds)
                ->with(['category', 'category.parent'])
                ->latest()
                ->paginate($perPage);

            // Transform each wallpaper
            $wallpapers->getCollection()->transform(function ($wp) use ($user) {

                $wp->file_url = $wp->file_path;
                $wp->thumbnail_url = $wp->thumbnail_url;

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

            // Response
            return response()->json([
                'status' => 'success',
                'data' => [
                    'category' => [
                        'id' => $category->id,
                        'category_name' => $category->category_name,
                        'order' => $category->order,
                        'is_active' => $category->is_active,
                    ],
                    'wallpapers' => $wallpapers->items(),
                    'pagination' => [
                        'current_page' => $wallpapers->currentPage(),
                        'last_page' => $wallpapers->lastPage(),
                        'per_page' => (string) $wallpapers->perPage(),
                        'total' => $wallpapers->total(),
                        'from' => $wallpapers->firstItem(),
                        'to' => $wallpapers->lastItem(),
                    ]
                ]
            ], 200);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], 500);
        }
    }
}
