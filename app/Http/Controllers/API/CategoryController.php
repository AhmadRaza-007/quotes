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
            // Get admin categories (where owner_user_id is null)
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


    public function categoriesWithWallpapers($id = null)
    {
        try {
            if (isset($id)) {
                $category = WallpaperCategory::with(['wallpapers', 'children.wallpapers'])->find($id);

                if (!$category) {
                    return response()->json([
                        'status' => 'error',
                        'error' => 'Category not found',
                    ], 404);
                }

                return response()->json([
                    'status' => 'success',
                    'data' => $category,
                ], 200);
            } else {
                // Get all root categories with their wallpapers and children
                $categories = WallpaperCategory::with(['wallpapers', 'children.wallpapers'])
                    ->active()
                    ->root()
                    ->orderBy('order')
                    ->get();

                return response()->json([
                    'status' => 'success',
                    'data' => $categories,
                ], 200);
            }
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
