<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function index()
    {
        $themes = Theme::with('category')->paginate(10);
        return view('admin.theme', compact('themes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'theme' => 'required',
            'category_id' => 'required',
        ]);

        try {
            $themeFile = $request->file('theme');
            $themerName = time() . '.' . $themeFile->getClientOriginalExtension();
            $themeFile->move(public_path('uploads/themes'), $themerName);

            $theme = new Theme();
            $theme->name = $request->title;
            $theme->category_id = $request->category_id;
            $theme->theme = 'uploads/themes/' . $themerName;
            $theme->save();

            return redirect()->route('themes')->with('success', 'Theme saved successfully');
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return redirect()->route('themes')->with('error', 'Failed to save theme. ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $theme = Theme::with('category')->find($id);
        return response()->json($theme);
    }

    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'theme' => 'nullable',
            'category_id' => 'required',
        ]);

        $themeFile = $request->file('theme');
        if ($themeFile) {
            $themerName = time() . '.' . $themeFile->getClientOriginalExtension();
            $themeFile->move(public_path('uploads/themes'), $themerName);

            $theme = Theme::find($request->theme_id);
            $theme->name = $request->title;
            $theme->category_id = $request->category_id;
            $theme->theme = 'uploads/themes/' . $themerName;
            $theme->save();
        } else {
            $theme = Theme::find($request->theme_id);
            $theme->name = $request->title;
            $theme->category_id = $request->category_id;
            $theme->save();
        }

        return redirect()->back()->with('success', 'Themes has been successfully updated.');
    }

    public function destroy($id)
    {
        $theme = Theme::find($id);
        $theme->delete();

        return redirect()->route('themes')->with('success', 'Theme deleted successfully');
    }
}
