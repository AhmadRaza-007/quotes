<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use App\Models\ApiKeyApp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Validator;
use App\Models\Wallpaper;
use App\Models\WallpaperCategory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class UserController extends Controller
{
    public function test()
    {
        // return 'Test route works!';

        return view('test');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return User::create(['name' => 'test', 'email' => 'test@test.com', 'password' => Hash::make('123123123'), 'user_type' => 2]);
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        try {
            $credentials = [
                'email' => $request->email,
                'password' => $request->password,
                'user_type' => 1,
            ];
            if (Auth::attempt($credentials)) {
                return redirect()->route('admin.dashboard');
            } else {
                return back()->withInput();
            }
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function redirectToGoogleProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleProviderCallback()
    {
        try {
            $user = Socialite::driver('google')->stateless()->user();

            // echo '<pre>';
            // print_r($user);
            // echo '</pre>';

            $existingUser = User::where('email', $user->email)->first();

            if ($existingUser) {
                Auth::login($existingUser);
            } else {
                $newUser = new User();
                $newUser->name = $user->name;
                $newUser->email = $user->email;
                $newUser->password = Hash::make(str_replace('@gmail.com', '', $user->email));
                $newUser->save();

                Auth::login($newUser);
            }

            return redirect()->route('admin.dashboard');
        } catch (\Exception $e) {
            \Log::error("error:" . $e->getMessage() . $e->getLine());
            return redirect()->route('admin.login')->with('error', 'Unable to login with Google.');
        }
    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')
            ->setScopes(['email', 'public_profile'])
            ->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();

            // echo '<pre>';
            // print_r($facebookUser);
            // echo '</pre>';

            $existingUser = User::where('email', $facebookUser->email)->first();

            if ($existingUser) {
                Auth::login($existingUser);
            } else {
                $newUser = new User();
                $newUser->name = $facebookUser->name;
                $newUser->email = $facebookUser->email;
                $newUser->password = Hash::make(str_replace('@gmail.com', '', $facebookUser->email));

                $newUser->save();
                Auth::login($newUser);
            }

            return redirect()->route('admin.dashboard');
        } catch (\Exception $e) {
            \Log::error("error:" . $e->getMessage() . $e->getLine());
            return redirect()->route('admin.login')->with('error', 'Unable to login with Facebook.');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }

    /**
     * Display a listing of users (Admin only)
     */
    public function userList(Request $request)
    {
        $users = User::withCount('categories')
            ->with([
                'categories' => function ($query) {
                    $query->withCount('wallpapers')
                        ->with('parent');
                }
            ]);

        // Search functionality
        if ($request->has('search')) {
            $users->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhere('id', 'like', '%' . $request->search . '%');
        }

        $users = $users->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {


        return view('admin.users.create');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'user_type' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }


    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {


        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'user_type' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'user_type' => $request->user_type,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Show user profile
     */
    public function profile()
    {
        $user = Auth::user();
        return view('admin.users.profile', compact('user'));
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully.');
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('admin.profile')->with('success', 'Password changed successfully.');
    }

    /**
     * Delete a user
     */
    public function destroy($id)
    {


        // Prevent admin from deleting themselves
        if (Auth::id() == $id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function resetPassword(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $user = User::findOrFail($id);
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('admin.users.edit', $id)->with('success', 'Password reset successfully.');
    }

    /**
     * Show user's wallpapers
     */
    public function userWallpapers($id)
    {
        $user = User::with('wallpapers.category')->findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Show form to upload wallpaper for user
     */
    public function createWallpaper($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.wallpapers.create', compact('user'));
    }



    /**
     * Delete user's wallpaper
     */
    public function deleteWallpaper($userId, $wallpaperId)
    {
        $wallpaper = Wallpaper::where('user_id', $userId)->findOrFail($wallpaperId);
        $wallpaper->delete();

        return redirect()->back()->with('success', 'Wallpaper deleted successfully.');
    }

    // Add these methods to UserController

    /**
     * Store category for user
     */
    public function storeCategory(Request $request, $id)
    {
        // return 1;
        $request->validate([
            'category_name' => 'required|max:255',
            'parent_id' => 'required|exists:wallpaper_categories,id'
        ]);

        // Verify parent is one of the default categories
        $parentCategory = WallpaperCategory::whereNull('user_id')
            ->findOrFail($request->parent_id);

        WallpaperCategory::create([
            'category_name' => $request->category_name,
            'parent_id' => $request->parent_id,
            'user_id' => $id, // User category
            'is_active' => 1,
            'depth' => 1,
            'order' => 0,
        ]);

        return redirect()->route('admin.users.categories.index', auth()->user()->id)->with('success', 'Category created successfully.');
    }

    /**
     * Display all API keys for management
     */
    public function manageApiKeys(Request $request)
    {
        // Get all API keys for the authenticated user with their categories
        $apiKeys = Auth::user()->apiKeys()
            ->with('category')
            ->latest()
            ->paginate(20);

        // Get categories for the modal
        $categories = Auth::user()->apiKeyCategories()->get();

        return view('admin.api-keys.manage', compact('apiKeys', 'categories'));
    }

    /**
     * Regenerate an API key
     */
    public function regenerateApiKey($id)
    {
        $apiKey = Auth::user()->apiKeys()->findOrFail($id);

        $newKey = \Illuminate\Support\Str::random(64);

        $apiKey->update([
            'key' => $newKey,
            'last_used_at' => null
        ]);

        return response()->json([
            'success' => true,
            'api_key' => $newKey,
            'message' => 'API key regenerated successfully'
        ]);
    }


    // In app/Http/Controllers/UserController.php
    /**
     * Show wallpapers in a specific category
     */
    public function categoryWallpapers($userId, $categoryId)
    {
        $user = User::findOrFail($userId);
        $category = WallpaperCategory::where('user_id', $userId)
            ->with('parent')
            ->findOrFail($categoryId);

        // Base query
        $wallpapersQuery = Wallpaper::where('category_id', $categoryId)
            ->where('user_id', $userId);

        // Add search functionality
        if (request()->has('search') && !empty(request('search'))) {
            $searchTerm = request('search');
            $wallpapersQuery->where('id', $searchTerm);
        }

        // Show 100 wallpapers per page (10 rows × 10 columns)
        $wallpapers = $wallpapersQuery->orderBy('created_at', 'desc')
            ->paginate(100);

        return view('admin.users.categories.wallpapers', compact('user', 'category', 'wallpapers'));
    }

    public function userCategories($id)
    {
        $user = User::findOrFail($id);

        $categories = WallpaperCategory::where('user_id', $id)
            ->withCount('wallpapers')
            ->with('parent')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.users.categories.index', compact('user', 'categories'));
    }

    public function toggleCategory($userId, $categoryId)
    {
        $category = WallpaperCategory::where('user_id', $userId)
            ->findOrFail($categoryId);

        $category->update([
            'is_active' => !$category->is_active
        ]);

        return redirect()->back()
            ->with('success', 'Category status updated successfully.');
    }


    /**
     * Show form to create subcategory for user
     */
    public function createCategory($id)
    {
        $user = User::findOrFail($id);
        $defaultCategories = WallpaperCategory::whereNull('parent_id')
            ->whereNull('user_id')
            ->get();

        return view('admin.users.categories.create', compact('user', 'defaultCategories'));
    }

    /**
     * Show form to upload wallpaper to specific category
     */
    public function createCategoryWallpaper($userId, $categoryId)
    {
        $user = User::findOrFail($userId);
        $category = WallpaperCategory::where('user_id', $userId)->findOrFail($categoryId);

        return view('admin.users.categories.wallpapers.create', compact('user', 'category'));
    }

    public function storeCategoryWallpaper(Request $request, $userId, $categoryId)
    {
        $user = User::findOrFail($userId);
        $category = WallpaperCategory::where('user_id', $userId)->findOrFail($categoryId);
        $parentCategory = $category->parent;

        // Multiple files
        if ($request->hasFile('files')) {
            if ($parentCategory->category_name != 'Live Wallpapers') {
                $request->validate([
                    'files' => 'required|array|max:20',
                    'files.*' => 'file|mimes:jpg,jpeg,png,webp|max:102400',
                ]);
            } else {
                $request->validate([
                    'files' => 'required|array|max:20',
                    'files.*' => 'file|mimes:mp4,webm,mov,avi,gif|max:102400',
                    'video_thumbnails' => 'nullable|array',
                ]);
            }

            $uploadedFiles = $request->file('files');
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($uploadedFiles as $i => $file) {
                try {
                    $fileRequest = new Request();
                    $fileRequest->files->set('file', $file);
                    $fileRequest->merge([
                        'title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                        'category_id' => $categoryId,
                        'media_type' => $parentCategory->category_name != 'Live Wallpapers' ? 'image' : 'video',
                    ]);

                    // attach the corresponding base64 thumbnail if provided
                    if ($parentCategory->category_name == 'Live Wallpapers' && isset($request->video_thumbnails[$i])) {
                        $fileRequest->merge([
                            'video_thumbnail_base64' => $request->video_thumbnails[$i],
                        ]);
                    }

                    $this->storeWallpaper($fileRequest, $userId, $categoryId);
                    $successCount++;
                } catch (\Throwable $e) {
                    Log::error('Error uploading file ' . $file->getClientOriginalName() . ': ' . $e->getMessage());
                    $errorCount++;
                    $errors[] = "Error with file " . $file->getClientOriginalName() . ": " . $e->getMessage();
                }
            }

            $message = "Upload completed: {$successCount} files uploaded successfully.";
            if ($errorCount > 0) {
                $message .= " {$errorCount} files failed.";
                if (!empty($errors)) session()->flash('upload_errors', $errors);
            }

            return redirect()->route('admin.users.categories.wallpapers', [$userId, $categoryId])
                ->with('success', $message);
        }

        // Single upload fallback
        $request->validate([
            'title' => 'nullable|max:255',
            'file' => 'required|file|mimes:jpg,jpeg,png,gif,mp4,webm,webp,mov,avi,m4v|max:102400',
            'media_type' => 'nullable|in:image,video,live',
        ]);

        $this->storeWallpaper($request, $userId, $categoryId);

        return redirect()->route('admin.users.categories.wallpapers', [$userId, $categoryId])
            ->with('success', 'Wallpaper uploaded successfully.');
    }

    public function storeWallpaper(Request $request, $id, $categoryId)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'title' => 'nullable|max:255',
            'file' => 'required|file|mimes:jpg,jpeg,png,gif,mp4,webm,webp,mov,avi,m4v|max:102400',
            'category_id' => 'required',
            'media_type' => 'nullable|in:image,video,live',
        ]);

        $uploaded = $request->file('file');
        $extension = $uploaded->getClientOriginalExtension();
        $fileName = time() . '_' . Str::random(6) . '.' . $extension;
        $mimeType = $uploaded->getClientMimeType();
        $fileSize = $uploaded->getSize();
        $tmpDir = sys_get_temp_dir();
        $tmpFile = $tmpDir . DIRECTORY_SEPARATOR . $fileName;

        $mediaType = $request->input('media_type') ?? (
            str_starts_with($mimeType, 'video/') ? 'video' : ($mimeType === 'image/gif' ? 'live' : 'image')
        );

        try {
            $uploaded->move($tmpDir, $fileName);
        } catch (\Throwable $e) {
            Log::error('Failed to move uploaded file: ' . $e->getMessage());
            return back()->withErrors('Upload failed.');
        }

        $b2Path = 'wallpapers/' . $fileName;
        try {
            $stream = fopen($tmpFile, 'r');
            Storage::disk('b2')->put($b2Path, $stream);
            fclose($stream);
        } catch (\Throwable $e) {
            Log::error('Failed to upload to B2: ' . $e->getMessage());
            @unlink($tmpFile);
            return back()->withErrors('Storage upload failed.');
        }

        $fileUrl = Storage::disk('b2')->url($b2Path);


        // Handle thumbnail
        $thumbnailUrl = null;
        $thumbPath = null;
        $image = null;

        // (1) If direct thumbnail file is uploaded
        // if ($request->hasFile('thumbnail')) {
        //     try {
        //         $thumb = $request->file('thumbnail');
        //         $thumbName = 'thumb_' . time() . '_' . Str::random(6) . '.' . $thumb->getClientOriginalExtension();
        //         $thumbPath = 'wallpapers/thumbnails/' . $thumbName;
        //         Storage::disk('b2')->put($thumbPath, file_get_contents($thumb->getRealPath()));
        //         $thumbnailUrl = Storage::disk('b2')->url($thumbPath);
        //     } catch (\Throwable $e) {
        //         Log::warning('Thumbnail file upload failed: ' . $e->getMessage());
        //     }
        // }
        if ($request->hasFile('thumbnail')) {
            try {
                $thumb = $request->file('thumbnail');
                $img = \Intervention\Image\Facades\Image::make($thumb->getRealPath())
                    ->resize(400, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });

                // Compress until under 300KB
                $quality = 90;
                do {
                    $encoded = $img->encode('jpg', $quality);
                    $quality -= 5;
                } while (strlen($encoded) > 300 * 1024 && $quality > 10);

                $thumbName = 'thumb_' . time() . '_' . Str::random(6) . '.jpg';
                $thumbPath = 'wallpapers/thumbnails/' . $thumbName;

                Storage::disk('b2')->put($thumbPath, (string)$encoded);
                $thumbnailUrl = Storage::disk('b2')->url($thumbPath);
            } catch (\Throwable $e) {
                Log::warning('Thumbnail file upload failed: ' . $e->getMessage());
            }
        }
        // (2) Else if frontend sent Base64 thumbnail
        // elseif ($request->filled('video_thumbnail_base64')) {
        //     try {
        //         $data = preg_replace('#^data:image/\w+;base64,#i', '', $request->input('video_thumbnail_base64'));
        //         $imageData = base64_decode($data);
        //         if ($imageData !== false) {
        //             $thumbName = 'thumb_' . time() . '_' . Str::random(6) . '.jpg';
        //             $thumbPath = 'wallpapers/thumbnails/' . $thumbName;
        //             Storage::disk('b2')->put($thumbPath, $imageData);
        //             $thumbnailUrl = Storage::disk('b2')->url($thumbPath);
        //         }
        //     } catch (\Throwable $e) {
        //         Log::warning('Base64 thumbnail upload failed: ' . $e->getMessage());
        //     }
        // }
        elseif ($request->filled('video_thumbnail_base64')) {
            try {
                $data = preg_replace('#^data:image/\w+;base64,#i', '', $request->video_thumbnail_base64);
                $imageData = base64_decode($data);

                if ($imageData === false) {
                    throw new \Exception("Invalid base64");
                }

                $img = \Intervention\Image\Facades\Image::make($imageData)
                    ->resize(400, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });

                // Compress until under 300KB
                $quality = 90;
                do {
                    $encoded = $img->encode('jpg', $quality);
                    $quality -= 5;
                } while (strlen($encoded) > 300 * 1024 && $quality > 10);

                $thumbName = 'thumb_' . time() . '_' . Str::random(6) . '.jpg';
                $thumbPath = 'wallpapers/thumbnails/' . $thumbName;

                Storage::disk('b2')->put($thumbPath, (string)$encoded);
                $thumbnailUrl = Storage::disk('b2')->url($thumbPath);
            } catch (\Throwable $e) {
                Log::warning('Base64 thumbnail upload failed: ' . $e->getMessage());
            }
        }

        // (3) If it's an image wallpaper and no thumbnail given → auto-generate
        // elseif ($mediaType === 'image') {
        //     try {
        //         // Make thumbnail using Intervention Image
        //         $thumbName = 'thumb_' . time() . '_' . Str::random(6) . '.jpg';
        //         $thumbPath = 'wallpapers/thumbnails/' . $thumbName;

        //         $image = \Intervention\Image\Facades\Image::make($tmpFile)
        //             ->resize(400, null, function ($constraint) {
        //                 $constraint->aspectRatio();
        //                 $constraint->upsize();
        //             })
        //             ->encode('jpg', 80);

        //         Storage::disk('b2')->put($thumbPath, (string) $image);
        //         $thumbnailUrl = Storage::disk('b2')->url($thumbPath);
        //     } catch (\Throwable $e) {
        //         Log::warning('Auto thumbnail generation failed: ' . $e->getMessage());
        //     }
        // }

        elseif ($mediaType === 'image') {
            try {
                $thumbName = 'thumb_' . time() . '_' . Str::random(6) . '.jpg';
                $thumbPath = 'wallpapers/thumbnails/' . $thumbName;

                // Base resize (keeps aspect ratio)
                $img = Image::make($tmpFile)
                    ->resize(400, null, function ($c) {
                        $c->aspectRatio();
                        $c->upsize();
                    });

                $quality = 80;
                do {
                    $encoded = $img->encode('jpg', $quality);
                    $sizeKB = strlen($encoded) / 1024;
                    $quality -= 5;
                } while ($sizeKB > 300 && $quality > 10);

                Storage::disk('b2')->put($thumbPath, (string) $encoded);
                $thumbnailUrl = Storage::disk('b2')->url($thumbPath);
            } catch (\Throwable $e) {
                Log::warning('Auto thumbnail generation failed: ' . $e->getMessage());
            }
        }


        // dd($tmpFile);
        @unlink($tmpFile);

        Wallpaper::create([
            'title'         => $request->title,
            'category_id'   => $categoryId,
            'file_path'     => $b2Path,
            'media_type'    => $mediaType,
            'mime_type'     => $mimeType,
            'file_size'     => $fileSize,
            'file_url'      => $fileUrl,
            'thumbnail_url' => $thumbnailUrl,
            'thumbnail_path' => $thumbPath,
            'user_id' => $id,
            'is_admin'      => 1,
        ]);

        return redirect()->route('admin.users.categories.wallpapers', [$id, $categoryId])
            ->with('success', 'Wallpaper uploaded successfully.');
    }

    /**
     * Delete user category and all related wallpapers with file cleanup
     */
    public function destroyCategory($userId, $categoryId)
    {
        $category = WallpaperCategory::where('user_id', $userId)->findOrFail($categoryId);

        // Get all wallpapers in this category
        $wallpapers = Wallpaper::where('category_id', $categoryId)
            ->where('user_id', $userId)
            ->get();

        // Delete wallpaper files from storage
        foreach ($wallpapers as $wallpaper) {
            try {
                // Delete main file
                if ($wallpaper->file_path && Storage::disk('b2')->exists($wallpaper->file_path)) {
                    Storage::disk('b2')->delete($wallpaper->file_path);
                }

                // Delete thumbnail if exists
                if ($wallpaper->thumbnail_url) {
                    $thumbnailPath = str_replace(Storage::disk('b2')->url(''), '', $wallpaper->thumbnail_url);
                    if (Storage::disk('b2')->exists($thumbnailPath)) {
                        Storage::disk('b2')->delete($thumbnailPath);
                    }
                }
            } catch (\Exception $e) {
                // Log error but continue with deletion
                \Log::error('Failed to delete wallpaper files for ID ' . $wallpaper->id . ': ' . $e->getMessage());
            }
        }

        // Delete wallpaper records from database
        Wallpaper::where('category_id', $categoryId)
            ->where('user_id', $userId)
            ->delete();

        // Delete the category
        $category->delete();

        return redirect()->route('admin.users.categories.index', $userId)->with('success', 'Category and all related wallpapers deleted successfully.');
    }

    public function getUserApiKeys($userId)
    {
        $user = User::findOrFail($userId);
        $apiKeys = $user->apiKeys()->latest()->get();

        // Build HTML string manually
        if ($apiKeys->isEmpty()) {
            return '<p class="text-muted">No API keys found for this user.</p>';
        }

        $html = '<table class="table table-bordered align-middle">
        <thead>
            <tr>
                <th>Name</th>
                <th>App Name</th>
                <th>Created</th>
                <th>Last Used</th>
                <th>Expires</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>';

        foreach ($apiKeys as $key) {
            $html .= '<tr>
            <td>' . e($key->name) . '<br><small class="text-muted">ID: ' . e($key->id) . '</small></td>
            <td>' . e($key->app_name) . '<br><small class="text-muted">ID: ' . e($key->id) . '</small></td>
            <td>' . $key->created_at->format('M d, Y h:i A') . '</td>
            <td>' . ($key->last_used_at ? $key->last_used_at->format('M d, Y h:i A') : 'Never') . '</td>
            <td>' . ($key->expires_at ? $key->expires_at->format('M d, Y h:i A') : 'Never') . '</td>
            <td><span class="badge ' . ($key->is_active ? 'bg-success' : 'bg-danger') . '">' . ($key->is_active ? 'Active' : 'Inactive') . '</span></td>
            <td><button class="btn btn-sm btn-danger" onclick="deleteApiKey(' . $key->id . ')">Delete</button></td>
        </tr>';
        }

        $html .= '</tbody></table>';

        return $html;
    }


    /**
     * Display API keys management page
     */
    // public function apiKeys(Request $request)
    // {
    //     // return $user = User::where('email', 'admin@admin.com')->first();
    //     $users = User::where('email', 'admin@admin.com')->withCount('apiKeys');

    //     // Search functionality
    //     if ($request->has('search')) {
    //         $users->where('name', 'like', '%' . $request->search . '%')
    //             ->orWhere('email', 'like', '%' . $request->search . '%');
    //     }

    //     $users = $users->paginate(10);

    //     return view('admin.api-keys.index', compact('users'));
    // }

    public function apiKeys(Request $request, $id)
    {
        $apiKeyApp = ApiKeyApp::with('apiKeys')->where('id', $id)->where('is_active', 1)->first();
        return view('admin.api-keys.index', compact('apiKeyApp'));
    }

    /**
     * Generate API key for a user
     */
    // public function generateApiKey(Request $request)
    // {
    //     // return $request->all();
    //     $request->validate([
    //         'user_id' => 'required|exists:users,id',
    //         'name' => 'required|string|max:255',
    //         'app_name' => 'required|string|max:255',
    //         'expires_in_days' => 'nullable|integer|min:1|max:365'
    //     ]);

    //     $user = User::findOrFail($request->user_id);

    //     $expiresAt = null;
    //     if ($request->filled('expires_in_days')) {
    //         $expiresAt = now()->addDays($request->input('expires_in_days'));
    //     }

    //     $apiKey = $user->createApiKey($request->name, $expiresAt);

    //     return redirect()->route('admin.api-keys.index')
    //         ->with('success', 'API key generated successfully for ' . $user->name)
    //         ->with('api_key', $apiKey->key); // Flash the key for one-time display
    // }

    public function generateApiKey(Request $request)
    {
        $request->all();
        $request->validate([
            'name' => 'required|string|max:255',
            // 'app_name' => 'required|string|max:255',
            'expires_in_days' => 'nullable|integer|min:1|max:365',
            'app_id' => 'nullable|exists:api_key_app,id'
        ]);

        $expiresAt = null;
        if ($request->filled('expires_in_days')) {
            $expiresAt = now()->addDays($request->input('expires_in_days'));
        }

        $apiKey = ApiKey::create([
            'app_id' => $request->app_id,
            'name' => $request->input('name'),
            'key' => Str::random(64),
            'expires_at' => $expiresAt,
            'is_active' => 1,
        ]);

        return redirect()->back()
            ->with('success', 'API key generated successfully')
            ->with('api_key', $apiKey->key);
    }

    public function userApiKeys($userId)
    {
        $user = User::with('apiKeys')->findOrFail($userId);

        return view('admin.api-keys.user-keys', compact('user'))->render();
    }

    /**
     * Delete an API key
     */
    public function deleteApiKey($id)
    {
        $apiKey = ApiKey::findOrFail($id);
        $apiKey->delete();

        return true;
        // return redirect()->back()
        //     ->with('success', 'API key deleted successfully');
    }
}
