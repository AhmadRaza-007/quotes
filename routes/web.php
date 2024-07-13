<?php

use App\Http\Controllers\QuoteController;
use App\Http\Controllers\QuoteCategoryController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');

    return "Cache cleared successfully";
});

Route::redirect('/', '/admin/dashboard', 302);
Route::group(['middleware' => 'auth'], function () {
    Route::prefix('admin')->group(function () {

        Route::get('/dashboard', function () {
            return view('admin.main');
        })->name('admin.dashboard');

        Route::get('category', [QuoteCategoryController::class, 'index'])->name('category');
        Route::post('category/store', [QuoteCategoryController::class, 'store'])->name('category.store');
        Route::get('category/edit/{id}', [QuoteCategoryController::class, 'edit'])->name('category.edit');
        Route::post('category/update', [QuoteCategoryController::class, 'update'])->name('category.update');
        Route::get('category/delete/{id}', [QuoteCategoryController::class, 'destroy'])->name('category.delete');

        Route::get('themes', [ThemeController::class, 'index'])->name('themes');
        Route::post('/store-themes', [ThemeController::class, 'store'])->name('themes.store');
        Route::get('/theme/edit/{id}', [ThemeController::class, 'edit'])->name('themes.edit');
        Route::post('/theme/update', [ThemeController::class, 'update'])->name('themes.update');
        Route::get('/theme/delete/{id}', [ThemeController::class, 'destroy'])->name('themes.delete');

        Route::get('/quotes', [QuoteController::class, 'index'])->name('quotes');
        Route::post('/storeQuotes', [QuoteController::class, 'store'])->name('quotes.store');
        Route::get('/quote/edit/{id}', [QuoteController::class, 'edit'])->name('quotes.edit');
        Route::post('/quote/update', [QuoteController::class, 'update'])->name('quotes.update');
        Route::get('/quote/delete/{id}', [QuoteController::class, 'destroy'])->name('quotes.delete');
    });
});

Route::get('/login', [UserController::class, 'index'])->name('admin.login');
Route::post('/login', [UserController::class, 'login'])->name('post.login');
Route::get('/logout', [UserController::class, 'logout'])->name('post.logout');

Route::get('login/google', [UserController::class, 'redirectToGoogleProvider'])->name('google.login');
Route::get('google/callback', [UserController::class, 'handleProviderCallback']);

Route::get('login/facebook', [UserController::class, 'redirectToFacebook'])->name('facebook.login');
Route::get('facebook/callback', [UserController::class, 'handleFacebookCallback']);
