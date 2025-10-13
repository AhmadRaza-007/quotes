<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Wallpaper;
use App\Models\WallpaperCategory;
use Symfony\Component\Process\Process;

class WallpaperController extends Controller
{
    public function index()
    {
        $wallpapers = Wallpaper::with(['category', 'owner'])->paginate(10);
        return view('admin.wallpapers', compact('wallpapers'));
    }

    public function store(Request $request)
    {

        // $request->validate([
        //     'title' => 'nullable|max:255',
        //     'file' => 'required|file|mimes:jpg,jpeg,png,gif,mp4,webm,webp,mov,avi,m4v|max:102400',
        //     'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
        //     'category_id' => 'required',
        //     'media_type' => 'nullable|in:image,video,live',
        // ]);

        $request->validate([
            'title' => 'nullable|max:255',
            'file' => 'required|file|mimes:jpg,jpeg,png,gif,mp4,webm,webp,mov,avi,m4v|max:102400',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'category_id' => 'required',
            'media_type' => 'nullable|in:image,video,live',
            'owner_user_id' => 'nullable|exists:users,id', // Add this line
        ]);

        // return 'hello';
        $uploaded = $request->file('file');
        $extension = $uploaded->getClientOriginalExtension();
        $fileName = time() . '_' . Str::random(6) . '.' . $extension;
        $mimeType = $uploaded->getClientMimeType();
        $fileSize = $uploaded->getSize();

        // determine media type
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

        $tmpDir = sys_get_temp_dir();
        $tmpFile = $tmpDir . DIRECTORY_SEPARATOR . $fileName;

        try {
            $uploaded->move($tmpDir, $fileName);
        } catch (\Throwable $e) {
            Log::error('Failed to move uploaded file to tmp: ' . $e->getMessage());
            return back()->withErrors('Upload failed (move).');
        }

        // Upload file to B2
        $b2Path = 'wallpapers/' . $fileName;
        try {
            $stream = fopen($tmpFile, 'r');
            if ($stream === false) {
                throw new \RuntimeException('Failed to open tmp file for reading: ' . $tmpFile);
            }
            $ok = Storage::disk('b2')->put($b2Path, $stream);
            if (is_resource($stream)) fclose($stream);
            if (!$ok) throw new \RuntimeException('Storage::put returned falsy for ' . $b2Path);
        } catch (\Throwable $e) {
            Log::error('Failed to upload file to B2: ' . $e->getMessage());
            @unlink($tmpFile);
            return back()->withErrors('Upload to storage failed.');
        }

        $fileUrl = null;
        try {
            $fileUrl = Storage::disk('b2')->url($b2Path);
        } catch (\Throwable $e) {
            Log::warning('Could not generate public URL for uploaded file: ' . $e->getMessage());
        }

        // --- Thumbnail testing section ---
        $thumbnailPath = null;
        $thumbnailUrl  = null;


        // 1) explicit thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $thumb = $request->file('thumbnail');
            $thumbName = 'thumb_' . time() . '_' . Str::random(6) . '.' . $thumb->getClientOriginalExtension();

            try {
                $putResult = Storage::disk('b2')->putFileAs('wallpapers/thumbnails', $thumb, $thumbName);
                if ($putResult) {
                    $thumbnailPath = 'wallpapers/thumbnails/' . $thumbName;
                    $thumbnailUrl = Storage::disk('b2')->url($thumbnailPath);
                }
            } catch (\Throwable $e) {
                Log::error('Failed to upload explicit thumbnail to B2: ' . $e->getMessage());
            }
        }

        // 2) auto-generate thumbnail if video
        if ($mediaType === 'video' && !$thumbnailPath) {
            // $ffmpegBin = trim(shell_exec('which ffmpeg'));
            $ffmpegBin = '/opt/homebrew/bin/ffmpeg';
            //  $ffmpegBin = '/usr/bin/ffmpeg';

            if (!$ffmpegBin) {
                Log::error('FFmpeg not found on server!');
            } else {
                $autoName = 'thumb_' . time() . '_' . Str::random(6) . '.jpg';
                $localThumbDir = public_path('uploads/wallpapers/thumbnails');

                if (!file_exists($localThumbDir)) {
                    mkdir($localThumbDir, 0777, true);
                }

                $tmpThumb = $localThumbDir . DIRECTORY_SEPARATOR . $autoName;
                \Log::info(escapeshellarg($tmpFile));
                $cmd = $ffmpegBin . ' -y -i ' . escapeshellarg($tmpFile) . ' -ss 00:00:01 -vframes 1 -q:v 2 ' . escapeshellarg($tmpThumb) . ' 2>&1';

                $output = shell_exec($cmd);
                info($output);

                if (file_exists($tmpThumb)) {
                    $thumbB2Path = 'wallpapers/thumbnails/' . $autoName;

                    try {
                        $stream = fopen($tmpThumb, 'r');
                        if ($stream === false) throw new \RuntimeException('Failed to open thumbnail for upload');

                        $ok = Storage::disk('b2')->put($thumbB2Path, $stream);
                        if (is_resource($stream)) fclose($stream);

                        if ($ok) {
                            $thumbnailPath = $thumbB2Path;
                            $thumbnailUrl  = Storage::disk('b2')->url($thumbB2Path);
                            Log::info("Thumbnail uploaded to B2: " . $thumbB2Path);
                        } else {
                            Log::error("Failed to upload thumbnail to B2: " . $thumbB2Path);
                        }
                    } catch (\Throwable $e) {
                        Log::error("Error uploading thumbnail to B2: " . $e->getMessage());
                    }

                    @unlink($tmpThumb); // clean up local tmp file
                }
            }
        }


        @unlink($tmpFile);

        // Wallpaper::create([
        //     'title'             => $request->title,
        //     'category_id'       => $request->category_id,
        //     'file_path'         => $b2Path,
        //     'media_type'        => $mediaType,
        //     'mime_type'         => $mimeType,
        //     'file_size'         => $fileSize,
        //     'file_url'          => $fileUrl,
        //     'thumbnail_url'     => $thumbnailUrl,
        // ]);

        Wallpaper::create([
            'title'             => $request->title,
            'category_id'       => $request->category_id,
            'file_path'         => $b2Path,
            'media_type'        => $mediaType,
            'mime_type'         => $mimeType,
            'file_size'         => $fileSize,
            'file_url'          => $fileUrl,
            'thumbnail_url'     => $thumbnailUrl,
            'owner_user_id'     => $request->owner_user_id, // Add this line
            'is_admin'          => 1, // Since admin is uploading
        ]);

        return redirect()->route('wallpapers')->with('success', 'Wallpaper saved successfully');
    }



    public function edit($id)
    {
        $wallpaper = Wallpaper::with('category')->findOrFail($id);
        return response()->json($wallpaper);
    }

    public function update(Request $request, $id)
    {
        // route now provides {id} in URL, merge into request for validation
        $request->merge(['id' => $id]);
        $request->validate([
            'id'         => 'required|exists:wallpapers,id',
            'title'      => 'required|max:255',
            'file'       => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,webm,webp,mov,avi,m4v|max:102400',
            'thumbnail'  => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'category_id' => 'required',
            'media_type' => 'nullable|in:image,video,live',
            'owner_user_id' => 'nullable|exists:users,id', // Add this line
        ]);

        $wallpaper = Wallpaper::findOrFail($request->id);

        $wallpaper->title       = $request->title;
        $wallpaper->category_id = $request->category_id;

        $mediaType = $request->input('media_type', $wallpaper->media_type);

        // ----------------------
        // File replacement
        // ----------------------
        if ($request->hasFile('file')) {
            $uploaded  = $request->file('file');
            $extension = $uploaded->getClientOriginalExtension();
            $fileName  = time() . '_' . Str::random(6) . '.' . $extension;
            $mimeType  = $uploaded->getClientMimeType();
            $fileSize  = $uploaded->getSize();

            // detect media type if not passed
            if (!$request->filled('media_type')) {
                if (str_starts_with($mimeType, 'video/')) {
                    $mediaType = 'video';
                } elseif ($mimeType === 'image/gif') {
                    $mediaType = 'live';
                } else {
                    $mediaType = 'image';
                }
            }

            $tmpDir  = sys_get_temp_dir();
            $tmpFile = $tmpDir . DIRECTORY_SEPARATOR . $fileName;

            try {
                $uploaded->move($tmpDir, $fileName);
            } catch (\Throwable $e) {
                Log::error('Failed to move uploaded file (update): ' . $e->getMessage());
                return back()->withErrors('Upload failed.');
            }

            $b2Path = 'wallpapers/' . $fileName;
            try {
                $stream = fopen($tmpFile, 'r');
                if ($stream === false) throw new \RuntimeException('Failed to open tmp file');
                $ok = Storage::disk('b2')->put($b2Path, $stream);
                if (is_resource($stream)) fclose($stream);
                if (!$ok) throw new \RuntimeException('Upload failed');
            } catch (\Throwable $e) {
                Log::error('Upload to B2 failed (update): ' . $e->getMessage());
                @unlink($tmpFile);
                return back()->withErrors('Upload to storage failed.');
            }

            // delete old file
            try {
                if ($wallpaper->file_path && Storage::disk('b2')->exists($wallpaper->file_path)) {
                    Storage::disk('b2')->delete($wallpaper->file_path);
                }
            } catch (\Throwable $e) {
                Log::warning('Old file delete failed: ' . $e->getMessage());
            }

            $wallpaper->file_path = $b2Path;
            try {
                $wallpaper->file_url = Storage::disk('b2')->url($b2Path);
            } catch (\Throwable $e) {
                Log::warning('File URL generation failed: ' . $e->getMessage());
            }
            $wallpaper->mime_type = $mimeType;
            $wallpaper->file_size = $fileSize;

            @unlink($tmpFile);
        }

        // ----------------------
        // Thumbnail replacement
        // ----------------------
        $thumbnailPath = $wallpaper->thumbnail_url; // keep current if not replaced
        $thumbnailUrl  = $wallpaper->thumbnail_url;

        // 1) explicit thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $thumb = $request->file('thumbnail');
            $thumbName = 'thumb_' . time() . '_' . Str::random(6) . '.' . $thumb->getClientOriginalExtension();

            try {
                $putResult = Storage::disk('b2')->putFileAs('wallpapers/thumbnails', $thumb, $thumbName);
                if ($putResult) {
                    // delete old
                    try {
                        if ($wallpaper->thumbnail_url && Storage::disk('b2')->exists(str_replace(Storage::disk('b2')->url(''), '', $wallpaper->thumbnail_url))) {
                            Storage::disk('b2')->delete(str_replace(Storage::disk('b2')->url(''), '', $wallpaper->thumbnail_url));
                        }
                    } catch (\Throwable $e) {
                        Log::warning('Could not delete old thumbnail: ' . $e->getMessage());
                    }

                    $thumbnailPath = 'wallpapers/thumbnails/' . $thumbName;
                    $thumbnailUrl  = Storage::disk('b2')->url($thumbnailPath);
                }
            } catch (\Throwable $e) {
                Log::error('Thumbnail upload failed: ' . $e->getMessage());
            }
        }

        // 2) auto-generate thumbnail if video & none provided
        if ($mediaType === 'video' && !$request->hasFile('thumbnail') && !$thumbnailPath) {
            $ffmpegBin = '/opt/homebrew/bin/ffmpeg'; // match store()
            //  $ffmpegBin = '/usr/bin/ffmpeg';
            $autoName  = 'thumb_' . time() . '_' . Str::random(6) . '.jpg';
            $localDir  = public_path('uploads/wallpapers/thumbnails');

            if (!file_exists($localDir)) mkdir($localDir, 0777, true);

            $tmpThumb = $localDir . DIRECTORY_SEPARATOR . $autoName;
            $cmd = $ffmpegBin . ' -y -i ' . escapeshellarg($wallpaper->file_path) . ' -ss 00:00:01 -vframes 1 -q:v 2 ' . escapeshellarg($tmpThumb) . ' 2>&1';
            shell_exec($cmd);

            if (file_exists($tmpThumb)) {
                $thumbB2Path = 'wallpapers/thumbnails/' . $autoName;
                try {
                    $stream = fopen($tmpThumb, 'r');
                    $ok = Storage::disk('b2')->put($thumbB2Path, $stream);
                    if (is_resource($stream)) fclose($stream);

                    if ($ok) {
                        $thumbnailPath = $thumbB2Path;
                        $thumbnailUrl  = Storage::disk('b2')->url($thumbB2Path);
                    }
                } catch (\Throwable $e) {
                    Log::error('Auto thumbnail upload failed: ' . $e->getMessage());
                }
                @unlink($tmpThumb);
            }
        }

        $wallpaper->thumbnail_url = $thumbnailUrl;
        $wallpaper->media_type    = $mediaType;
        $wallpaper->owner_user_id = $request->owner_user_id;
        $wallpaper->save();

        return redirect()->back()->with('success', 'Wallpaper updated successfully.');
    }


    public function destroy($id)
    {
        $wallpaper = Wallpaper::findOrFail($id);

        try {
            // delete main file
            if ($wallpaper->file_path && Storage::disk('b2')->exists($wallpaper->file_path)) {
                Storage::disk('b2')->delete($wallpaper->file_path);
            }

            // delete thumbnail
            if ($wallpaper->thumbnail_url) {
                // convert URL back to relative path
                $baseUrl = Storage::disk('b2')->url('');
                $thumbPath = str_replace($baseUrl, '', $wallpaper->thumbnail_url);

                if ($thumbPath && Storage::disk('b2')->exists($thumbPath)) {
                    Storage::disk('b2')->delete($thumbPath);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Error deleting files from B2 on destroy: ' . $e->getMessage());
        }

        $wallpaper->delete();

        return redirect()->route('wallpapers')->with('success', 'Wallpaper deleted successfully');
    }

    public function getWallpapersByCategory(WallpaperCategory $category)
    {
        $allWallpapers = $category->getAllWallpapers();
        $perPage = 10;
        $currentPage = request()->get('page', 1);
        $currentPageWallpapers = $allWallpapers->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $wallpapers = new LengthAwarePaginator($currentPageWallpapers, count($allWallpapers), $perPage, $currentPage, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);

        return view('admin.wallpapers', compact('wallpapers'));
    }
}
