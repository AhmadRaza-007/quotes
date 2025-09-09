<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Intervention\Image\ImageManagerStatic as Image;

class ImageServiceProvider extends ServiceProvider
{
    public function register()
    {
        // no-op
    }

    public function boot()
    {
        // Set driver if needed (gd or imagick). Default autodetect is fine; you may override:
        Image::configure(['driver' => env('IMAGE_DRIVER', 'gd')]);
    }
}
