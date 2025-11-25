<?php

use App\Http\Controllers\ApiKeyAppController;
use App\Http\Controllers\QuoteCategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WallpaperCategoryController;
use App\Http\Controllers\WallpaperController;
use App\Http\Controllers\Admin\NotificationController;
use App\Services\FirebaseTestService;
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
    // Route::prefix('admin')->group(function () {

    //     Route::get('/dashboard', function () {
    //         return view('admin.main');
    //     })->name('admin.dashboard');

    //     // Category routes
    //     Route::get('category', [WallpaperCategoryController::class, 'index'])->name('category');
    //     Route::post('category/store', [WallpaperCategoryController::class, 'store'])->name('category.store');
    //     Route::get('category/edit/{id}', [WallpaperCategoryController::class, 'edit'])->name('category.edit');
    //     Route::post('category/update', [WallpaperCategoryController::class, 'update'])->name('category.update');
    //     Route::get('category/delete/{id}', [WallpaperCategoryController::class, 'destroy'])->name('category.delete');

    //     Route::get('/categories/{category}/wallpapers', [WallpaperController::class, 'getWallpapersByCategory'])->name('category.wallpapers');

    //     // Wallpapers CRUD
    //     Route::get('/wallpapers', [WallpaperController::class, 'index'])->name('wallpapers');
    //     Route::post('/wallpaper/store', [WallpaperController::class, 'store'])->name('wallpapers.store');
    //     Route::get('/wallpapers/edit/{id}', [WallpaperController::class, 'edit'])->name('wallpapers.edit');
    //     Route::post('/wallpaper/update', [WallpaperController::class, 'update'])->name('wallpapers.update');
    //     Route::get('/wallpaper/delete/{id}', [WallpaperController::class, 'destroy'])->name('wallpapers.delete');
    //     // Route::post('/storeWallpapers', [QuoteController::class, 'store'])->name('wallpapers.store');
    //     // Route::get('/quote/edit/{id}', [QuoteController::class, 'edit'])->name('wallpapers.edit');
    //     // Route::post('/quote/update', [QuoteController::class, 'update'])->name('wallpapers.update');
    //     // Route::get('/quote/delete/{id}', [QuoteController::class, 'destroy'])->name('wallpapers.delete');

    //     Route::get('/users', [UserController::class, 'userList'])->name('admin.users.index');
    //     Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
    //     Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
    //     Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    //     Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
    //     Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    //     // Password reset route
    //     Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('admin.users.reset-password');

    //     // User wallpaper management routes
    //     Route::get('/users/{id}/wallpapers/create', [UserController::class, 'createWallpaper'])->name('admin.users.wallpapers.create');
    //     Route::post('/users/{id}/wallpapers', [UserController::class, 'storeWallpaper'])->name('admin.users.wallpapers.store');
    //     Route::delete('/users/{userId}/wallpapers/{wallpaperId}', [UserController::class, 'deleteWallpaper'])->name('admin.users.wallpapers.delete');


    //     Route::get('/profile', [UserController::class, 'profile'])->name('admin.profile');
    //     Route::post('/profile', [UserController::class, 'updateProfile'])->name('admin.profile.update');
    //     Route::post('/change-password', [UserController::class, 'changePassword'])->name('admin.change.password');
    // });

    Route::group(['middleware' => 'auth'], function () {
        Route::prefix('admin')->group(function () {

            Route::get('/test-firebase', function () {
                try {
                    $firebaseTest = new FirebaseTestService();
                    $result = $firebaseTest->testConnection();

                    return response()->json($result);
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error: ' . $e->getMessage()
                    ], 500);
                }
            });

            Route::get('/test-notification', function () {
                try {
                    $notificationService = new \App\Services\NotificationService();

                    // Test with empty device tokens array to test Firebase connection
                    $result = $notificationService->sendToMultipleDevices(
                        [], // Empty array to test connection without sending
                        'Test Notification',
                        'This is a test notification from Firebase'
                    );

                    return response()->json([
                        'success' => true,
                        'message' => 'Firebase connection successful',
                        'data' => $result
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error: ' . $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ], 500);
                }
            });

            Route::get('/dashboard', function () {
                return view('admin.main');
            })->name('admin.dashboard');

            Route::get('/test', [UserController::class, 'test'])->name('test');

            // Category routes (admin-only categories)
            // Route::get('category', [WallpaperCategoryController::class, 'index'])->name('category');
            // Route::post('category/store', [WallpaperCategoryController::class, 'store'])->name('category.store');
            // Route::get('category/edit/{id}', [WallpaperCategoryController::class, 'edit'])->name('category.edit');
            // Route::post('category/update', [WallpaperCategoryController::class, 'update'])->name('category.update');
            // Route::get('category/delete/{id}', [WallpaperCategoryController::class, 'destroy'])->name('category.delete');
            Route::get('category', [WallpaperCategoryController::class, 'index'])->name('category');
            Route::post('category/store', [WallpaperCategoryController::class, 'store'])->name('category.store');
            Route::get('category/edit/{id}', [WallpaperCategoryController::class, 'edit'])->name('category.edit');
            Route::post('category/update', [WallpaperCategoryController::class, 'update'])->name('category.update');
            Route::get('category/delete/{id}', [WallpaperCategoryController::class, 'destroy'])->name('category.delete');
            Route::get('/categories/{category}/wallpapers', [WallpaperCategoryController::class, 'getWallpapersByCategory'])->name('category.wallpapers');


            Route::get('/categories/{category}/wallpapers', [WallpaperController::class, 'getWallpapersByCategory'])->name('category.wallpapers');

            // Wallpapers CRUD
            Route::get('/wallpapers', [WallpaperController::class, 'index'])->name('wallpapers');
            Route::post('/wallpaper/store', [WallpaperController::class, 'store'])->name('wallpapers.store');
            Route::get('/wallpapers/edit/{id}', [WallpaperController::class, 'edit'])->name('wallpapers.edit');
            Route::post('/wallpaper/update', [WallpaperController::class, 'update'])->name('wallpapers.update');
            Route::get('/wallpaper/delete/{id}', [WallpaperController::class, 'destroy'])->name('wallpapers.delete');

            // User management routes
            Route::get('/users', [UserController::class, 'userList'])->name('admin.users.index');
            Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
            Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
            Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
            Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
            Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');

            // In routes/web.php - Add these routes

            // API Key Category management routes
            Route::get('/api-keys/apps', [ApiKeyAppController::class, 'index'])->name('admin.api-keys.apps.index');
            Route::get('/api-keys/apps/create', [ApiKeyAppController::class, 'create'])->name('admin.api-keys.apps.create');
            Route::post('/api-keys/apps', [ApiKeyAppController::class, 'store'])->name('admin.api-keys.apps.store');
            Route::get('/api-keys/apps/{id}/edit', [ApiKeyAppController::class, 'edit'])->name('admin.api-keys.apps.edit');
            Route::put('/api-keys/apps/{id}', [ApiKeyAppController::class, 'update'])->name('admin.api-keys.apps.update');
            Route::delete('/api-keys/apps/{id}', [ApiKeyAppController::class, 'destroy'])->name('admin.api-keys.apps.destroy');


            // API Key management routes
            Route::get('/api-keys/{id}', [UserController::class, 'apiKeys'])->name('admin.api-keys');
            Route::post('/api-keys/generate', [UserController::class, 'generateApiKey'])->name('admin.api-keys.generate');
            Route::delete('/api-keys/{id}', [UserController::class, 'deleteApiKey'])->name('admin.api-keys.delete');
            Route::get('/api-keys/app/{userId}', [UserController::class, 'userApiKeys'])->name('admin.api-keys.user');
            // Route::get('/api-keys/user/{id}', [UserController::class, 'getUserApiKeys']);

            // API Keys management routes
            Route::get('/api-keys/manage', [UserController::class, 'manageApiKeys'])->name('admin.api-keys.manage');
            Route::post('/api-keys/{id}/regenerate', [UserController::class, 'regenerateApiKey'])->name('admin.api-keys.regenerate');



            // Route::post('/users/{id}/categories', [UserController::class, 'storeCategory'])->name('admin.users.categories.store');

            // NEW: User category and wallpaper management routes
            // Route::post('/users/{id}/categories', [UserController::class, 'storeCategory'])->name('admin.users.categories.store');
            Route::get('/users/{id}/categories', [UserController::class, 'userCategories'])->name('admin.users.categories.index');
            Route::patch('/admin/users/{userId}/categories/{categoryId}/toggle', [UserController::class, 'toggleCategory'])
                ->name('admin.users.categories.toggle');
            Route::post('/users/{id}/categories/store', [UserController::class, 'storeCategory'])->name('admin.users.categories.store');
            Route::get('/users/{id}/categories/create', [UserController::class, 'createCategory'])->name('admin.users.categories.create');
            Route::get('/users/{userId}/categories/{categoryId}/wallpapers', [UserController::class, 'categoryWallpapers'])->name('admin.users.categories.wallpapers');
            Route::get('/users/{userId}/categories/{categoryId}/wallpapers/create', [UserController::class, 'createCategoryWallpaper'])->name('admin.users.categories.wallpapers.create');
            Route::post('/users/{userId}/categories/{categoryId}/wallpapers', [UserController::class, 'storeCategoryWallpaper'])->name('admin.users.categories.wallpapers.store');
            Route::delete('/users/{userId}/categories/{categoryId}', [UserController::class, 'destroyCategory'])->name('admin.users.categories.destroy');

            Route::get('/users/{id}/wallpapers', [UserController::class, 'userWallpapers'])->name('admin.users.wallpapers.index');
            Route::get('/users/{id}/wallpapers/create', [UserController::class, 'createWallpaper'])->name('admin.users.wallpapers.create');
            Route::post('/users/{id}/wallpapers', [UserController::class, 'storeWallpaper'])->name('admin.users.wallpapers.store');
            Route::delete('/users/{userId}/wallpapers/{wallpaperId}', [UserController::class, 'deleteWallpaper'])->name('admin.users.wallpapers.delete');

            Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('admin.users.reset-password');

            Route::get('/profile', [UserController::class, 'profile'])->name('admin.profile');
            Route::post('/profile', [UserController::class, 'updateProfile'])->name('admin.profile.update');
            Route::post('/change-password', [UserController::class, 'changePassword'])->name('admin.change.password');

            // Push Notification Management Routes
            Route::get('/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
            Route::post('/notifications/send-to-all', [NotificationController::class, 'sendToAll'])->name('admin.notifications.send-to-all');
            Route::post('/notifications/send-to-user', [NotificationController::class, 'sendToUser'])->name('admin.notifications.send-to-user');
            Route::post('/notifications/send-wallpaper-notification', [NotificationController::class, 'sendWallpaperNotification'])->name('admin.notifications.send-wallpaper-notification');
            Route::get('/notifications/users', [NotificationController::class, 'getUsers'])->name('admin.notifications.users');
            Route::get('/notifications/stats', [NotificationController::class, 'getStats'])->name('admin.notifications.stats');
        });
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
