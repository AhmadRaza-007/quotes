<?php

namespace App\Http\View\Composers;

use App\Models\WallpaperCategory;
use Illuminate\View\View;

class CategoryComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $categories = WallpaperCategory::with('children')->whereNull('parent_id')->get();
        $view->with('categories', $categories);
    }
}
