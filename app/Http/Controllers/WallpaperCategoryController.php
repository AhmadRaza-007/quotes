<?php

namespace App\Http\Controllers;

use App\Models\WallpaperCategory;
use App\Models\User;
use App\Models\Wallpaper;
use Illuminate\Http\Request;

class WallpaperCategoryController extends Controller
{
    /**
     * Display a listing of the categories (Admin categories only)
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return WallpaperCategory::whereNull('owner_user_id')
        //     ->whereNull('parent_id')
        //     ->get();
        // Show only admin-created categories (where owner_user_id is null)
        $categories = WallpaperCategory::whereNull('owner_user_id')
            ->with('children', 'parent')
            ->get();

        return view('admin.category', compact('categories'));
    }

    /**
     * Show the form for creating a new category (Admin categories only)
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Only show admin categories for parent selection
        $categories = WallpaperCategory::whereNull('owner_user_id')->get();

        return view('admin.categories.create', compact('categories'));
    }

    /**
     * Store a newly created category in storage (Admin categories only)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required',
            'parent_id' => 'required|exists:wallpaper_categories,id',
            'is_active' => 'nullable|boolean',
            'order' => 'nullable|integer',
        ]);

        // Verify parent is an admin category if provided
        if ($request->parent_id) {
            $parent = WallpaperCategory::whereNull('owner_user_id')
                ->findOrFail($request->parent_id);
        }

        // Determine the depth based on parent
        $depth = 0;
        if ($request->parent_id) {
            $parent = WallpaperCategory::find($request->parent_id);
            $depth = $parent->depth + 1;
        }

        WallpaperCategory::create([
            'category_name' => $request->category_name,
            'parent_id' => $request->parent_id,
            'owner_user_id' => null, // Admin category
            'is_active' => $request->is_active ?? true,
            'depth' => $depth,
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('category')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified category.
     *
     * @param  \App\Models\WallpaperCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function show(WallpaperCategory $category)
    {
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category (Admin categories only)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = WallpaperCategory::whereNull('owner_user_id')
            ->findOrFail($id);

        // Only show admin categories for parent selection
        $categories = WallpaperCategory::whereNull('owner_user_id')
            ->where('id', '!=', $id)
            ->get();

        return view('admin.categories.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified category in storage (Admin categories only)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $category = WallpaperCategory::whereNull('owner_user_id')
            ->findOrFail($id);

        $request->validate([
            'category_name' => 'required',
            'parent_id' => 'required|exists:wallpaper_categories,id',
            'is_active' => 'nullable|boolean',
            'order' => 'nullable|integer',
        ]);

        // Verify parent is an admin category if provided
        if ($request->parent_id) {
            $parent = WallpaperCategory::whereNull('owner_user_id')
                ->findOrFail($request->parent_id);
        }

        $category->update([
            'category_name' => $request->category_name,
            'parent_id' => $request->parent_id,
            'is_active' => $request->is_active ?? true,
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('category')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Get wallpapers by category (for admin categories)
     *
     * @param  int  $categoryId
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = WallpaperCategory::whereNull('owner_user_id')
            ->findOrFail($id);

        // Check if category has user subcategories
        $userSubcategoriesCount = WallpaperCategory::where('parent_id', $id)
            ->whereNotNull('owner_user_id')
            ->count();

        if ($userSubcategoriesCount > 0) {
            return redirect()->route('category')
                ->with('error', 'Cannot delete category that has user subcategories. Delete user subcategories first.');
        }

        // Delete all wallpapers in this admin category
        Wallpaper::where('category_id', $id)
            ->where('is_admin', 1)
            ->delete();

        $category->delete();

        return redirect()->route('category')
            ->with('success', 'Category and all related wallpapers deleted successfully.');
    }
}
