<?php

use Illuminate\Support\Facades\Route;

// Existing controllers
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\SocialAuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\WallpaperController;

// Legacy interaction controllers (wallpaper-level) — kept for backward compatibility
use App\Http\Controllers\API\LikeController as LegacyLikeController;
use App\Http\Controllers\API\FavouriteController as LegacyFavouriteController;
use App\Http\Controllers\API\CommentController as LegacyCommentController;

// New controllers for TikTok-style behavior (likes/comments on ProfilePosts)
// Note: You will need to implement these controllers accordingly
use App\Http\Controllers\API\ProfilePostController;
use App\Http\Controllers\API\PostLikeController;
use App\Http\Controllers\API\PostCommentController;
use App\Http\Controllers\API\FollowController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Rules confirmed by product:
| - Only admins can upload/edit/delete wallpapers.
| - Home shows admin wallpapers only (no user posts there).
| - A user's total likes = sum of likes on their ProfilePosts.
| - No private accounts, reporting, or blocking.
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'api'], function () {
    // Public profile access
    Route::get('/users/{userId}', [UserController::class, 'showPublicProfile']);
    Route::get('/public-profiles', [UserController::class, 'publicProfiles']);
    Route::get('/users/{userId}/stats', [UserController::class, 'stats']);

    // New endpoint for profiles sorted by followers
    Route::get('/profiles/by-followers', [UserController::class, 'profilesByFollowers']);

    // Authentication routes
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/signup', [UserController::class, 'register']);
    Route::post('/forget-password', [UserController::class, 'forgotPassword']);
    Route::post('/reset-password', [UserController::class, 'resetPassword']);
    Route::post('/change-password', [UserController::class, 'changePassword']);

    // Social authentication
    Route::post('/auth/google', [SocialAuthController::class, 'handleGoogleAuth']);
    Route::post('/auth/facebook', [SocialAuthController::class, 'handleFacebookAuth']);

    // Public routes (no authentication required)
    Route::get('/wallpapers', [WallpaperController::class, 'index']); // Home feed: admin wallpapers only
    Route::get('/wallpapers/{id}', [WallpaperController::class, 'show']);

    Route::get('/categories', [CategoryController::class, 'categories']);
    Route::get('/categories/{id}/wallpapers', [CategoryController::class, 'categoriesWithWallpapers']);

    // Protected routes (authentication required)
    Route::group(['middleware' => 'auth:sanctum'], function () {
        // User account
        Route::post('/logout', [UserController::class, 'logout']);
        Route::post('/delete-user', [UserController::class, 'deleteUser']);
        Route::get('/profile', [UserController::class, 'profile']);

        // // Public routes (no authentication required)
        // Route::get('/wallpapers', [WallpaperController::class, 'index']); // Home feed: admin wallpapers only
        // Route::get('/wallpapers/{id}', [WallpaperController::class, 'show']);

        // Follow system
        Route::post('/users/{userId}/follow', [FollowController::class, 'follow']);
        // Use POST for unfollow (no DELETE)
        Route::post('/users/{userId}/unfollow', [FollowController::class, 'unfollow']);
        Route::get('/users/{userId}/followers', [FollowController::class, 'followers']);
        Route::get('/users/{userId}/following', [FollowController::class, 'following']);


        // Admin-only wallpaper management
        // IMPORTANT: Protect these with an 'admin' middleware or policy in your app
        Route::post('/wallpapers', [WallpaperController::class, 'store']);   // Admin upload
        // Admin edit (use POST instead of PATCH)
        Route::post('/wallpapers/{id}/update', [WallpaperController::class, 'update']); // Admin edit
        // Admin delete (use POST instead of DELETE)
        Route::post('/wallpapers/{id}/delete', [WallpaperController::class, 'destroy']); // Admin delete

        // Authenticated users can upload wallpapers for their profile (creates a ProfilePost)
        Route::post('/wallpapers/upload', [WallpaperController::class, 'userUpload']);

        // Legacy endpoints (DEPRECATED): likes/favourites/comments directly on wallpapers
        // Keep temporarily for backward compatibility; new apps should use ProfilePost endpoints above
        Route::post('/like', [LegacyLikeController::class, 'like']);
        Route::get('/get-like/{wallpaperId}', [LegacyLikeController::class, 'getLikedByUser']);

        Route::post('/favourite', [LegacyFavouriteController::class, 'favourite']);
        Route::get('/get-favourites', [LegacyFavouriteController::class, 'getFavourite']);
        Route::get('/get-favourite/{wallpaperId}', [LegacyFavouriteController::class, 'getFavouriteByUser']);

        Route::post('/comment', [LegacyCommentController::class, 'comment']);
        Route::get('/get-comment/{wallpaperId}', [LegacyCommentController::class, 'getComment']);
    });
});
