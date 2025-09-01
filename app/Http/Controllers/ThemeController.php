<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            'theme' => 'required|file|mimes:jpg,jpeg,png,gif,mp4,webm,mov,avi,m4v|max:51200', // up to 50MB
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'category_id' => 'required',
            'media_type' => 'nullable|in:image,video,live',
        ]);

        try {
            $themeFile = $request->file('theme');
            $fileName = time() . '_' . Str::random(6) . '.' . $themeFile->getClientOriginalExtension();
            $uploadDir = public_path('uploads/themes');
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0775, true);
            }
            $themeFile->move($uploadDir, $fileName);

            $mimeType = $themeFile->getClientMimeType();
            $fileSize = $themeFile->getSize();

            $mediaType = $request->input('media_type');
            if (!$mediaType) {
                if (str_starts_with($mimeType, 'video/')) {
                    $mediaType = 'video';
                } elseif ($mimeType === 'image/gif') {
                    $mediaType = 'live';
                } else {
                    $mediaType = 'image';
                }
            }

            $thumbnailPath = null;
            if ($request->hasFile('thumbnail')) {
                $thumbFile = $request->file('thumbnail');
                $thumbName = 'thumb_' . time() . '_' . Str::random(6) . '.' . $thumbFile->getClientOriginalExtension();
                $thumbDir = public_path('uploads/themes/thumbnails');
                if (!is_dir($thumbDir)) {
                    @mkdir($thumbDir, 0775, true);
                }
                $thumbFile->move($thumbDir, $thumbName);
                $thumbnailPath = 'uploads/themes/thumbnails/' . $thumbName;
            }

            $theme = new Theme();
            $theme->name = $request->title;
            $theme->category_id = $request->category_id;
            $theme->theme = 'uploads/themes/' . $fileName;
            $theme->media_type = $mediaType;
            $theme->thumbnail = $thumbnailPath;
            $theme->mime_type = $mimeType;
            $theme->file_size = $fileSize;
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
            'theme' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,webm,mov,avi,m4v|max:51200',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'category_id' => 'required',
            'media_type' => 'nullable|in:image,video,live',
        ]);

        $theme = Theme::findOrFail($request->theme_id);
        $theme->name = $request->title;
        $theme->category_id = $request->category_id;

        $mediaType = $request->input('media_type', $theme->media_type);

        $themeFile = $request->file('theme');
        if ($themeFile) {
            $fileName = time() . '_' . Str::random(6) . '.' . $themeFile->getClientOriginalExtension();
            $uploadDir = public_path('uploads/themes');
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0775, true);
            }
            $themeFile->move($uploadDir, $fileName);

            $mimeType = $themeFile->getClientMimeType();
            $fileSize = $themeFile->getSize();

            if (!$request->filled('media_type')) {
                if (str_starts_with($mimeType, 'video/')) {
                    $mediaType = 'video';
                } elseif ($mimeType === 'image/gif') {
                    $mediaType = 'live';
                } else {
                    $mediaType = 'image';
                }
            }

            $theme->theme = 'uploads/themes/' . $fileName;
            $theme->mime_type = $mimeType;
            $theme->file_size = $fileSize;
        }

        if ($request->hasFile('thumbnail')) {
            $thumbFile = $request->file('thumbnail');
            $thumbName = 'thumb_' . time() . '_' . Str::random(6) . '.' . $thumbFile->getClientOriginalExtension();
            $thumbDir = public_path('uploads/themes/thumbnails');
            if (!is_dir($thumbDir)) {
                @mkdir($thumbDir, 0775, true);
            }
            $thumbFile->move($thumbDir, $thumbName);
            $theme->thumbnail = 'uploads/themes/thumbnails/' . $thumbName;
        }

        $theme->media_type = $mediaType;
        $theme->save();

        return redirect()->back()->with('success', 'Theme has been successfully updated.');
    }

    public function destroy($id)
    {
        $theme = Theme::find($id);
        $theme->delete();

        return redirect()->route('themes')->with('success', 'Theme deleted successfully');
    }
}
