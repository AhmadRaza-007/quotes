<?php

namespace App\Http\Controllers;

use App\Models\WallpaperCategory;
use App\Models\QuoteCategory;
use Illuminate\Http\Request;

class QuoteCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = WallpaperCategory::get();
        return view('admin.category', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'category_name' =>'required',
        ]);

        WallpaperCategory::create($data);

        return back()->with('success', 'Category Added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\QuoteCategory  $quoteCategory
     * @return \Illuminate\Http\Response
     */
    public function show(QuoteCategory $quoteCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\QuoteCategory  $quoteCategory
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return WallpaperCategory::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\QuoteCategory  $quoteCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required',
            'category_name' => 'required',
        ]);

        $quoteCategory = QuoteCategory::find($request->category_id);
        $quoteCategory->update($data);

        return back()->with('success', 'Category Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\QuoteCategory  $quoteCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $a = WallpaperCategory::with('wallpapers')->whereId($id)->first();
        $count = $a->wallpapers->count();

        if ($count <= 0) {
            WallpaperCategory::find($id)->delete();
            return back()->with('success', 'Category Deleted Successfully');
        }else {
            return back()->with('error', 'You can not delete this category because it has wallpapers');
        }
    }
}
