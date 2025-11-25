<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\WallpaperCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // In CategoryController - update categories method
    public function categories(Request $request)
    {
        try {
            // Get admin categories (where user_id is null)
            $categories = WallpaperCategory::active()
                ->adminCategories()
                ->with('children')
                ->root()
                ->orderBy('order')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $categories,
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }


    // public function categoriesWithWallpapers(Request $request)
    // {
    //     try {

    //         // Define the two main categories (Wallpapers and Live Wallpapers)
    //         $mainCategoryIds = [11, 12]; // IDs for Wallpapers and Live Wallpapers

    //         // Get pagination parameters
    //         $perPage = $request->get('count', 20);
    //         $page = $request->get('page', 1);

    //         if (isset($id)) {
    //             // Check if the provided ID is one of the main categories
    //             if (!in_array($id, $mainCategoryIds)) {
    //                 return response()->json([
    //                     'status' => 'error',
    //                     'error' => 'Invalid category ID. Only main categories are allowed.',
    //                 ], 400);
    //             }

    //             $category = WallpaperCategory::find($id);

    //             if (!$category) {
    //                 return response()->json([
    //                     'status' => 'error',
    //                     'error' => 'Category not found',
    //                 ], 404);
    //             }

    //             // Get paginated wallpapers including subcategories
    //             $paginatedWallpapers = $category->getAllWallpapersPaginated($perPage);

    //             return response()->json([
    //                 'status' => 'success',
    //                 'data' => [
    //                     'category' => [
    //                         'id' => $category->id,
    //                         'category_name' => $category->category_name,
    //                         'order' => $category->order,
    //                         'is_active' => $category->is_active,
    //                     ],
    //                     'wallpapers' => $paginatedWallpapers->items(),
    //                     'pagination' => [
    //                         'current_page' => $paginatedWallpapers->currentPage(),
    //                         'last_page' => $paginatedWallpapers->lastPage(),
    //                         'per_page' => $paginatedWallpapers->perPage(),
    //                         'total' => $paginatedWallpapers->total(),
    //                         'from' => $paginatedWallpapers->firstItem(),
    //                         'to' => $paginatedWallpapers->lastItem(),
    //                     ]
    //                 ],
    //             ], 200);
    //         } else {
    //             // Get only the two main categories with their direct wallpapers (no children)
    //             $categories = WallpaperCategory::with(['wallpapers'])
    //                 ->whereIn('id', $mainCategoryIds)
    //                 ->active()
    //                 ->root()
    //                 ->orderBy('order')
    //                 ->get();

    //             return response()->json([
    //                 'status' => 'success',
    //                 'data' => $categories,
    //             ], 200);
    //         }
    //     } catch (\Exception $exception) {
    //         return response()->json([
    //             'status' => 'error',
    //             'error' => $exception->getMessage(),
    //         ], 500);
    //     }
    // }

    public function categoriesWithWallpapers(Request $request)
    {
        try {
            $request->validate([
                'category_id' => 'sometimes|nullable|exists:wallpaper_categories,id',
            ]);

            // Main categories = root categories
            $mainCategories = WallpaperCategory::whereNull('parent_id')
                ->active()
                ->orderBy('order')
                ->get();

            $mainCategoryIds = $mainCategories->pluck('id')->toArray();

            // Pagination
            $perPage = $request->get('count', 20);

            // If ID is provided → Show wallpapers inside that main category
            if ($request->has('category_id')) {
                $categoryId = $request->get('category_id');
                // Check if the given ID is actually a root category
                if (!in_array($categoryId, $mainCategoryIds)) {
                    return response()->json([
                        'status' => 'error',
                        'error' => 'Invalid category ID. Only parent categories are allowed.',
                    ], 400);
                }

                $category = WallpaperCategory::find($categoryId);

                if (!$category) {
                    return response()->json([
                        'status' => 'error',
                        'error' => 'Category not found',
                    ], 404);
                }

                // Fetch wallpapers from this category + all deeper children
                $paginatedWallpapers = $category->getAllWallpapersPaginated($perPage);

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'category' => [
                            'id' => $category->id,
                            'category_name' => $category->category_name,
                            'order' => $category->order,
                            'is_active' => $category->is_active,
                        ],
                        'wallpapers' => $paginatedWallpapers->items(),
                        'pagination' => [
                            'current_page' => $paginatedWallpapers->currentPage(),
                            'last_page' => $paginatedWallpapers->lastPage(),
                            'per_page' => $paginatedWallpapers->perPage(),
                            'total' => $paginatedWallpapers->total(),
                            'from' => $paginatedWallpapers->firstItem(),
                            'to' => $paginatedWallpapers->lastItem(),
                        ]
                    ],
                ], 200);
            }

            // No ID → return all main/root categories with their direct wallpapers
            $categories = WallpaperCategory::with('wallpapers')
                ->whereNull('parent_id')
                ->active()
                ->orderBy('order')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $categories,
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function subCategoriesWithWallpapers(Request $request)
    {
        try {
            $request->validate([
                'category_id' => 'sometimes|nullable|exists:wallpaper_categories,id',
            ]);

            // Main categories = root categories
            $mainCategories = WallpaperCategory::whereNotNull('parent_id')
                ->active()
                ->orderBy('order')
                ->get();

            $subCategoryIds = $mainCategories->pluck('id')->toArray();

            // Pagination
            $perPage = $request->get('count', 20);

            // If ID is provided → Show wallpapers inside that main category
            if ($request->has('category_id')) {
                $categoryId = $request->get('category_id');
                // Check if the given ID is actually a root category
                if (!in_array($categoryId, $subCategoryIds)) {
                    return response()->json([
                        'status' => 'error',
                        'error' => 'Invalid category ID. Only Child categories are allowed.',
                    ], 400);
                }

                $category = WallpaperCategory::find($categoryId);

                if (!$category) {
                    return response()->json([
                        'status' => 'error',
                        'error' => 'Category not found',
                    ], 404);
                }

                // Fetch wallpapers from this category + all deeper children
                $paginatedWallpapers = $category->getAllWallpapersPaginated($perPage);

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'category' => [
                            'id' => $category->id,
                            'category_name' => $category->category_name,
                            'order' => $category->order,
                            'is_active' => $category->is_active,
                        ],
                        'wallpapers' => $paginatedWallpapers->items(),
                        'pagination' => [
                            'current_page' => $paginatedWallpapers->currentPage(),
                            'last_page' => $paginatedWallpapers->lastPage(),
                            'per_page' => $paginatedWallpapers->perPage(),
                            'total' => $paginatedWallpapers->total(),
                            'from' => $paginatedWallpapers->firstItem(),
                            'to' => $paginatedWallpapers->lastItem(),
                        ]
                    ],
                ], 200);
            }

            // No ID → return all main/root categories with their direct wallpapers
            $categories = WallpaperCategory::with('wallpapers')
                ->whereNull('parent_id')
                ->active()
                ->orderBy('order')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $categories,
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    // New method to get wallpapers by category including subcategories
    public function getWallpapersByCategory($categoryId)
    {
        try {
            $category = WallpaperCategory::find($categoryId);

            if (!$category) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Category not found',
                ], 404);
            }

            // Get all wallpapers from this category and its subcategories
            $wallpapers = $category->getAllWallpapers();

            return response()->json([
                'status' => 'success',
                'data' => $wallpapers,
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    // New method to get category tree for sidebar dropdown
    public function categoryTree()
    {
        try {
            $categories = WallpaperCategory::active()
                ->with(['allChildren' => function ($query) {
                    $query->active()->orderBy('order');
                }])
                ->root()
                ->orderBy('order')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $categories,
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    // New method to get subcategories of a specific category
    public function getSubcategories($categoryId)
    {
        try {
            $category = WallpaperCategory::with(['children' => function ($query) {
                $query->active()->orderBy('order');
            }])->find($categoryId);

            if (!$category) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Category not found',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $category->children,
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }
}
