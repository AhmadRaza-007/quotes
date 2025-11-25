<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ApiKey;
use App\Models\ApiKeyApp;

class ApiKeyAppController extends Controller
{
    /**
     * Display a listing of the categories for the authenticated user
     */
    public function index()
    {
        $categories = ApiKeyApp::latest()->get();
        return view('admin.api-keys.apps.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('admin.api-keys.apps.create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        ApiKeyApp::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.api-keys.apps.index')
            ->with('success', 'Category created successfully');
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit($id)
    {
        $category = ApiKeyApp::findOrFail($id);
        return view('admin.api-keys.apps.edit', compact('category'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);
        $category = ApiKeyApp::findOrFail($id);

        $category->update($request->only(['name', 'description']));

        return redirect()->route('admin.api-keys.apps.index')
            ->with('success', 'Category updated successfully');
    }

    /**
     * Remove the specified category
     */
    public function destroy($id)
    {
        $category = ApiKeyApp::findOrFail($id);

        // Check if category has API keys
        if ($category->apiKeys()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete category that has API keys. Please delete or move the API keys first.');
        }

        $category->delete();

        return redirect()->route('admin.api-keys.apps.index')
            ->with('success', 'Category deleted successfully');
    }
}
