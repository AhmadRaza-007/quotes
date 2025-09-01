<?php

use App\Http\Controllers\API\FavouriteController;
use App\Http\Controllers\API\LikeController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\WallpaperController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\SocialAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'API', 'middleware' => 'api'], function () {

    // Public routes (no authentication required)
    Route::get('/wallpapers', [WallpaperController::class, 'index']);
    Route::get('/wallpapers/{id}', [WallpaperController::class, 'show']);
    Route::get('/categories', [CategoryController::class, 'categories']);
    Route::get('/categories/{id}/wallpapers', [CategoryController::class, 'categoriesWithWallpapers']);
    // Route::get('/thumbnail/{id}', [ThumbnailController::class, 'show']);

    // Authentication routes
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/signup', [UserController::class, 'register']);
    Route::post('/forget-password', [UserController::class, 'forgotPassword']);
    Route::post('/reset-password', [UserController::class, 'resetPassword']);
    Route::post('/change-password', [UserController::class, 'changePassword']);

    // Social authentication
    Route::post('/auth/google', [SocialAuthController::class, 'handleGoogleAuth']);
    Route::post('/auth/facebook', [SocialAuthController::class, 'handleFacebookAuth']);

    // Protected routes (authentication required)
    Route::group(['middleware' => 'auth:sanctum'], function () {
        // User routes
        Route::post('/logout', [UserController::class, 'logout']);
        Route::post('/delete-user', [UserController::class, 'deleteUser']);
        Route::get('/profile', [UserController::class, 'profile']);

        // Interaction routes
        Route::post('/like', [LikeController::class, 'like']);
        Route::get('/get-like/{wallpaperId}', [LikeController::class, 'getLikedByUser']);
        Route::post('/favourite', [FavouriteController::class, 'favourite']);
        Route::get('/get-favourites', [FavouriteController::class, 'getFavourite']);
        Route::get('/get-favourite/{wallpaperId}', [FavouriteController::class, 'getFavouriteByUser']);
        Route::post('/comment', [CommentController::class, 'comment']);
        Route::get('/get-comment/{wallpaperId}', [CommentController::class, 'getComment']);
    });
});
