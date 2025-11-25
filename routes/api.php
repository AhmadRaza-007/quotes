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

// API Key management
use App\Http\Controllers\API\ApiKeyController;
use App\Http\Controllers\API\NotificationController;

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

Route::group(['middleware' => ['api', 'api.key']], function () {
    // All API endpoints now require API key authentication

    // Authentication routes (now require API key)
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/signup', [UserController::class, 'register']);
    Route::post('/forget-password', [UserController::class, 'forgotPassword']);
    Route::post('/reset-password', [UserController::class, 'resetPassword']);
    Route::post('/change-password', [UserController::class, 'changePassword']);

    // Social authentication (now require API key)
    Route::post('/auth/google', [SocialAuthController::class, 'handleGoogleAuth']);
    Route::post('/auth/facebook', [SocialAuthController::class, 'handleFacebookAuth']);

    // Public routes (now require API key)
    Route::get('/wallpapers', [WallpaperController::class, 'index']); // Home feed: admin wallpapers only
    Route::get('/wallpapers/search', [WallpaperController::class, 'search']); // Search by id or title
    Route::get('/wallpapers/details', [WallpaperController::class, 'show']);
    Route::get('/user/{id}/wallpapers', [WallpaperController::class, 'getWallpapersByUser']);
    Route::get('/user/{id}/categories', [WallpaperController::class, 'getCategoriessByUser']);
    Route::get('/usercategories_by_userid', [WallpaperController::class, 'getCategoriessByUserId']);

    // Public profile access
    Route::get('/users/{userId}', [UserController::class, 'showPublicProfile']);
    Route::get('/public-profiles', [UserController::class, 'publicProfiles']);
    Route::get('/users/{userId}/stats', [UserController::class, 'stats']);

    // New endpoint for profiles sorted by followers
    Route::get('/profiles/by-followers', [UserController::class, 'profilesByFollowers']);

    Route::get('/categories', [CategoryController::class, 'categories']);
    Route::get('/wallpapersbycat', [CategoryController::class, 'categoriesWithWallpapers']);
    Route::get('/wallpapersbysubcat', [CategoryController::class, 'subCategoriesWithWallpapers']);


    Route::get('/users/{userId}/followers', [FollowController::class, 'followers']);
    Route::get('/users/{userId}/following', [FollowController::class, 'following']);

    // Legacy endpoints (DEPRECATED): likes/favourites/comments directly on wallpapers
    // Keep temporarily for backward compatibility; new apps should use ProfilePost endpoints above

    Route::get('/get-like/{wallpaperId}', [LegacyLikeController::class, 'getLikedByUser']);

    Route::get('/get-comment/{wallpaperId}', [LegacyCommentController::class, 'getComment']);

    // Protected routes (user authentication required - for API key management)
    Route::group(['middleware' => 'auth:sanctum'], function () {
        // User account
        Route::post('/logout', [UserController::class, 'logout']);
        Route::post('/delete-user', [UserController::class, 'deleteUser']);
        Route::get('/profile', [UserController::class, 'profile']);

        // API Key management routes
        Route::prefix('api-keys')->group(function () {
            Route::get('/', [ApiKeyController::class, 'index']);
            Route::post('/', [ApiKeyController::class, 'store']);
            Route::get('/{id}', [ApiKeyController::class, 'show']);
            Route::put('/{id}', [ApiKeyController::class, 'update']);
            Route::delete('/{id}', [ApiKeyController::class, 'destroy']);
            Route::post('/{id}/regenerate', [ApiKeyController::class, 'regenerate']);
        });
    });

    // Admin-only wallpaper management
    // IMPORTANT: Protect these with an 'admin' middleware or policy in your app
    Route::post('/wallpapers', [WallpaperController::class, 'store']);   // Admin upload
    // Admin edit (use POST instead of PATCH)
    Route::post('/wallpapers/{id}/update', [WallpaperController::class, 'update']); // Admin edit
    // Admin delete (use POST instead of DELETE)
    Route::post('/wallpapers/{id}/delete', [WallpaperController::class, 'destroy']); // Admin delete

    Route::post('/like', [LegacyLikeController::class, 'like']);

    Route::post('/comment', [LegacyCommentController::class, 'comment']);

    Route::post('/favourite', [LegacyFavouriteController::class, 'favourite']);
    Route::get('/get-favourites', [LegacyFavouriteController::class, 'getFavourite']);
    Route::get('/get-favourite/{wallpaperId}', [LegacyFavouriteController::class, 'getFavouriteByUser']);

    // Follow system
    Route::post('/users/{userId}/follow', [FollowController::class, 'follow']);
    // Use POST for unfollow (no DELETE)
    Route::post('/users/{userId}/unfollow', [FollowController::class, 'unfollow']);

    // Authenticated users can upload wallpapers for their profile (creates a ProfilePost)
    Route::post('/wallpapers/upload', [WallpaperController::class, 'userUpload']);

    // Push notification routes
    Route::prefix('notifications')->group(function () {
        Route::post('/register-device', [NotificationController::class, 'registerDevice']);
        Route::post('/unregister-device', [NotificationController::class, 'unregisterDevice']);
        Route::post('/test', [NotificationController::class, 'sendTestNotification']);
        Route::get('/devices', [NotificationController::class, 'getUserDevices']);

    });
});
