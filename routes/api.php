<?php

use App\Http\Controllers\API\FavouriteController;
use App\Http\Controllers\API\LikeController;
use App\Http\Controllers\Api\QuoteCommentController;
use App\Http\Controllers\API\QuoteController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
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

Route::group(['namespace' => 'API'], function () {

    Route::group(['middleware' => 'auth:sanctum'], function () {

        Route::get('categories', [QuoteController::class, 'categories']);
        Route::post('/logout', [UserController::class, 'logout']);
        Route::post('/delete-user', [UserController::class, 'deleteUser']);

        Route::post('/like', [LikeController::class, 'like']);
        Route::get('/get-like/{quoteId}', [LikeController::class, 'getLikedByUser']);
        Route::post('/favourite', [FavouriteController::class, 'favourite']);
        Route::get('/get-favourites', [FavouriteController::class, 'getFavourite']);
        Route::get('/get-favourite/{quoteId}', [FavouriteController::class, 'getFavouriteByUser']);

        Route::post('/comment', [QuoteCommentController::class, 'comment']);
    });
    Route::get('/get-comment/{quoteId}', [QuoteCommentController::class, 'getComment']);

    Route::post('/login', [UserController::class, 'login']);
    Route::post('/signup', [UserController::class, 'register']);
    Route::post('/forget-password', [UserController::class, 'forgotPassword']);
    Route::post('/change-password', [UserController::class, 'changePassword']);

    Route::get('/categories', [QuoteController::class, 'categories']);
    Route::get('/categories-with-quotes/{id?}', [QuoteController::class, 'categoriesWithQuotes']);
    Route::get('/quotes/{user_id?}', [QuoteController::class, 'quotes']);

    // Route::get('login/google', [UserController::class, 'redirectToGoogleProvider']);
    Route::get('login/google', [UserController::class, 'handleProviderCallback']);
    // Route::get('google/callback', [UserController::class, 'handleProviderCallback']);

    Route::get('login/facebook', [UserController::class, 'handleFacebookCallback']);
});
