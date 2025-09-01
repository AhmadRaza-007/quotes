<?php

namespace App\Http\Controllers;

use App\Models\Wallpaper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WallpaperController extends Controller
{
    public function index()
    {
        $wallpapers = Wallpaper::with('category')->paginate(10);
        return view('admin.wallpapers', compact('wallpapers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'file' => 'required|file|mimes:jpg,jpeg,png,gif,mp4,webm,webp,mov,avi,m4v|max:102400',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'category_id' => 'required',
            'media_type' => 'nullable|in:image,video,live',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
        $uploadDir = public_path('uploads/wallpapers');
        if (!is_dir($uploadDir)) @mkdir($uploadDir, 0775, true);
        $mimeType = $file->getClientMimeType();
        $fileSize = $file->getSize();
        $file->move($uploadDir, $fileName);


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
            $thumb = $request->file('thumbnail');
            $thumbName = 'thumb_' . time() . '_' . Str::random(6) . '.' . $thumb->getClientOriginalExtension();
            $thumbDir = public_path('uploads/wallpapers/thumbnails');
            if (!is_dir($thumbDir)) @mkdir($thumbDir, 0775, true);
            $thumb->move($thumbDir, $thumbName);
            $thumbnailPath = 'uploads/wallpapers/thumbnails/' . $thumbName;
        }

        // If the uploaded file is a video and no thumbnail provided, try to auto-generate one using ffmpeg
        if ($mediaType === 'video' && !$thumbnailPath) {
            $videoFullPath = public_path('uploads/wallpapers/' . $fileName);
            $thumbDir = public_path('uploads/wallpapers/thumbnails');
            if (!is_dir($thumbDir)) @mkdir($thumbDir, 0775, true);
            $autoName = 'thumb_' . time() . '_' . Str::random(6) . '.jpg';
            $autoFullPath = $thumbDir . '/' . $autoName;

            // Use ffmpeg if available
            $ffmpeg = trim(shell_exec('which ffmpeg'));
            if ($ffmpeg) {
                // extract frame at 1 second
                $cmd = escapeshellcmd("$ffmpeg -i " . escapeshellarg($videoFullPath) . " -ss 00:00:01.000 -vframes 1 " . escapeshellarg($autoFullPath) . " 2>&1");
                @shell_exec($cmd);
                if (file_exists($autoFullPath)) {
                    $thumbnailPath = 'uploads/wallpapers/thumbnails/' . $autoName;
                }
            }
        }

        Wallpaper::create([
            'title' => $request->title,
            'category_id' => $request->category_id,
            'file_path' => 'uploads/wallpapers/' . $fileName,
            'media_type' => $mediaType,
            'thumbnail' => $thumbnailPath,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
        ]);

        return redirect()->route('wallpapers')->with('success', 'Wallpaper saved successfully');
    }

    public function edit($id)
    {
        $wallpaper = Wallpaper::with('category')->findOrFail($id);
        return response()->json($wallpaper);
    }

    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,webm,mov,avi,m4v|max:102400',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'category_id' => 'required',
            'media_type' => 'nullable|in:image,video,live',
        ]);

        $wallpaper = Wallpaper::findOrFail($request->id);
        $wallpaper->title = $request->title;
        $wallpaper->category_id = $request->category_id;
        $mediaType = $request->input('media_type', $wallpaper->media_type);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $uploadDir = public_path('uploads/wallpapers');
            if (!is_dir($uploadDir)) @mkdir($uploadDir, 0775, true);
            $mimeType = $file->getClientMimeType();
            $fileSize = $file->getSize();
            $file->move($uploadDir, $fileName);


            if (!$request->filled('media_type')) {
                if (str_starts_with($mimeType, 'video/')) {
                    $mediaType = 'video';
                } elseif ($mimeType === 'image/gif') {
                    $mediaType = 'live';
                } else {
                    $mediaType = 'image';
                }
            }

            $wallpaper->file_path = 'uploads/wallpapers/' . $fileName;
            $wallpaper->mime_type = $mimeType;
            $wallpaper->file_size = $fileSize;

            // If video and no thumbnail provided, attempt to auto-generate
            if ($mediaType === 'video' && !$request->hasFile('thumbnail')) {
                $videoFullPath = public_path('uploads/wallpapers/' . $fileName);
                $thumbDir = public_path('uploads/wallpapers/thumbnails');
                if (!is_dir($thumbDir)) @mkdir($thumbDir, 0775, true);
                $autoName = 'thumb_' . time() . '_' . Str::random(6) . '.jpg';
                $autoFullPath = $thumbDir . '/' . $autoName;
                $ffmpeg = trim(shell_exec('which ffmpeg'));
                if ($ffmpeg) {
                    $cmd = escapeshellcmd("$ffmpeg -i " . escapeshellarg($videoFullPath) . " -ss 00:00:01.000 -vframes 1 " . escapeshellarg($autoFullPath) . " 2>&1");
                    @shell_exec($cmd);
                    if (file_exists($autoFullPath)) {
                        $wallpaper->thumbnail = 'uploads/wallpapers/thumbnails/' . $autoName;
                    }
                }
            }
        }

        if ($request->hasFile('thumbnail')) {
            $thumb = $request->file('thumbnail');
            $thumbName = 'thumb_' . time() . '_' . Str::random(6) . '.' . $thumb->getClientOriginalExtension();
            $thumbDir = public_path('uploads/wallpapers/thumbnails');
            if (!is_dir($thumbDir)) @mkdir($thumbDir, 0775, true);
            $thumb->move($thumbDir, $thumbName);
            $wallpaper->thumbnail = 'uploads/wallpapers/thumbnails/' . $thumbName;
        }

        $wallpaper->media_type = $mediaType;
        $wallpaper->save();

        return redirect()->back()->with('success', 'Wallpaper updated successfully.');
    }

    public function destroy($id)
    {
        Wallpaper::findOrFail($id)->delete();
        return redirect()->route('wallpapers')->with('success', 'Wallpaper deleted successfully');
    }
}
