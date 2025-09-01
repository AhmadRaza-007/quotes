<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wallpaper;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ThumbnailController extends Controller
{
    public function show($id)
    {
        $wallpaper = Wallpaper::findOrFail($id);

        $thumbnailPath = $wallpaper->thumbnail;

        if (!$thumbnailPath) {
            return response()->json(['error' => 'Thumbnail not found'], 404);
        }

        $path = public_path($thumbnailPath);

        if (!file_exists($path)) {
            return response()->json(['error' => 'Thumbnail file not found'], 404);
        }

        return new BinaryFileResponse(
            $path,
            200,
            ['Content-Type' => mime_content_type($path)]
        );
    }

}
