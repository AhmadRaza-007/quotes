<?php


use App\Http\Controllers\QuoteCategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WallpaperController;
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
        // Wallpapers CRUD
        Route::get('/wallpapers', [WallpaperController::class, 'index'])->name('wallpapers');
        Route::post('/wallpaper/store', [WallpaperController::class, 'store'])->name('wallpapers.store');
        Route::get('/wallpapers/edit/{id}', [WallpaperController::class, 'edit'])->name('wallpapers.edit');
        Route::post('/wallpaper/update', [WallpaperController::class, 'update'])->name('wallpapers.update');
        Route::get('/wallpaper/delete/{id}', [WallpaperController::class, 'destroy'])->name('wallpapers.delete');
        // Route::post('/storeWallpapers', [QuoteController::class, 'store'])->name('wallpapers.store');
        // Route::get('/quote/edit/{id}', [QuoteController::class, 'edit'])->name('wallpapers.edit');
        // Route::post('/quote/update', [QuoteController::class, 'update'])->name('wallpapers.update');
        // Route::get('/quote/delete/{id}', [QuoteController::class, 'destroy'])->name('wallpapers.delete');

        Route::get('/users', [UserController::class, 'userList'])->name('admin.users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');

        Route::get('/profile', [UserController::class, 'profile'])->name('admin.profile');
        Route::post('/profile', [UserController::class, 'updateProfile'])->name('admin.profile.update');
        Route::post('/change-password', [UserController::class, 'changePassword'])->name('admin.change.password');
    });
});

Route::get('/login', [UserController::class, 'index'])->name('admin.login');
Route::post('/login', [UserController::class, 'login'])->name('post.login');
Route::get('/logout', [UserController::class, 'logout'])->name('post.logout');

// Social login routes (optional). Remove if not used.
Route::get('login/google', [UserController::class, 'redirectToGoogleProvider'])->name('google.login');
Route::get('google/callback', [UserController::class, 'handleProviderCallback']);
Route::get('login/facebook', [UserController::class, 'redirectToFacebook'])->name('facebook.login');
Route::get('facebook/callback', [UserController::class, 'handleFacebookCallback']);

