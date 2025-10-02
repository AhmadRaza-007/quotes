// app/Services/WallpaperService.php
<?php

namespace App\Services;

use App\Models\Wallpaper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WallpaperService
{
    public function uploadWallpaper($request, $ownerUserId = null)
    {
        // Copy the entire upload logic from WallpaperController here
        // Then call it from both controllers

        $uploaded = $request->file('file');
        $extension = $uploaded->getClientOriginalExtension();
        $fileName = time() . '_' . Str::random(6) . '.' . $extension;
        $mimeType = $uploaded->getClientMimeType();
        $fileSize = $uploaded->getSize();

        // ... rest of your upload logic

        return Wallpaper::create([
            'title'             => $request->title,
            'category_id'       => $request->category_id,
            'file_path'         => $b2Path,
            'media_type'        => $mediaType,
            'mime_type'         => $mimeType,
            'file_size'         => $fileSize,
            'file_url'          => $fileUrl,
            'thumbnail_url'     => $thumbnailUrl,
            'owner_user_id'     => $ownerUserId,
            'is_admin'          => 1,
        ]);
    }
}
