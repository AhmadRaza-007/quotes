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
| - Users create ProfilePosts by favoriting/reposting a wallpaper.
| - Likes/comments attach to ProfilePosts (not to Wallpapers).
| - A user's total likes = sum of likes on their ProfilePosts.
| - No private accounts, reporting, or blocking.
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'api'], function () {
    // Public routes (no authentication required)
    Route::get('/wallpapers', [WallpaperController::class, 'index']); // Home feed: admin wallpapers only
    Route::get('/wallpapers/{id}', [WallpaperController::class, 'show']);

    Route::get('/categories', [CategoryController::class, 'categories']);
    Route::get('/categories/{id}/wallpapers', [CategoryController::class, 'categoriesWithWallpapers']);

    // Public profile access
    Route::get('/users/{userId}', [UserController::class, 'showPublicProfile']);
    Route::get('/users/{userId}/profile-posts', [ProfilePostController::class, 'listByUser']);
    Route::get('/users/{userId}/stats', [UserController::class, 'stats']);

    // Public access to a specific ProfilePost
    Route::get('/profile-posts/{postId}', [ProfilePostController::class, 'show']);
    Route::get('/profile-posts/{postId}/comments', [PostCommentController::class, 'index']);
    Route::get('/profile-posts/{postId}/likes', [PostLikeController::class, 'index']);

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
        // User account
        Route::post('/logout', [UserController::class, 'logout']);
        Route::post('/delete-user', [UserController::class, 'deleteUser']);
        Route::get('/profile', [UserController::class, 'profile']);

        // Follow system
        Route::post('/users/{userId}/follow', [FollowController::class, 'follow']);
        Route::delete('/users/{userId}/follow', [FollowController::class, 'unfollow']);
        Route::get('/users/{userId}/followers', [FollowController::class, 'followers']);
        Route::get('/users/{userId}/following', [FollowController::class, 'following']);

        // ProfilePosts (favorites/reposts referencing admin wallpapers)
        Route::post('/profile-posts', [ProfilePostController::class, 'store']);
        Route::patch('/profile-posts/{postId}', [ProfilePostController::class, 'update']);
        Route::delete('/profile-posts/{postId}', [ProfilePostController::class, 'destroy']);

        // Likes on ProfilePosts (idempotent)
        Route::post('/profile-posts/{postId}/like', [PostLikeController::class, 'like']);
        Route::delete('/profile-posts/{postId}/like', [PostLikeController::class, 'unlike']);

        // Comments on ProfilePosts
        Route::post('/profile-posts/{postId}/comments', [PostCommentController::class, 'store']);
        Route::patch('/comments/{commentId}', [PostCommentController::class, 'update']);
        Route::delete('/comments/{commentId}', [PostCommentController::class, 'destroy']);

        // Following feed (ProfilePosts by followed users)
        Route::get('/feed/following', [ProfilePostController::class, 'followingFeed']);

        // Admin-only wallpaper management
        // IMPORTANT: Protect these with an 'admin' middleware or policy in your app
        Route::post('/wallpapers', [WallpaperController::class, 'store']);   // Admin upload
        Route::patch('/wallpapers/{id}', [WallpaperController::class, 'update']); // Admin edit
        Route::delete('/wallpapers/{id}', [WallpaperController::class, 'destroy']); // Admin delete

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
