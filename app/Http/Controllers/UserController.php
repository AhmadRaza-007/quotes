<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
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

    // In UserController.php edit method
    // public function edit($id)
    // {
    //     $user = User::with([
    //         'categories.children',
    //         'wallpapers.category'
    //     ])->findOrFail($id);

    //     // Get all category IDs (including children)
    //     $allCategoryIds = $user->categories->pluck('id')->toArray();
    //     foreach ($user->categories as $category) {
    //         $allCategoryIds = array_merge($allCategoryIds, $category->children->pluck('id')->toArray());
    //     }
    //     $allCategoryIds = array_unique($allCategoryIds);

    //     // Get counts for all categories
    //     $wallpaperCounts = Wallpaper::whereIn('category_id', $allCategoryIds)
    //         ->where('owner_user_id', $id)
    //         ->groupBy('category_id')
    //         ->selectRaw('category_id, COUNT(*) as count')
    //         ->pluck('count', 'category_id');

    //     // Assign counts to all categories
    //     foreach ($user->categories as $category) {
    //         $category->direct_wallpapers_count = $wallpaperCounts[$category->id] ?? 0;
    //         $category->children_count = $category->children->count();

    //         // Also assign counts to children
    //         foreach ($category->children as $child) {
    //             $child->direct_wallpapers_count = $wallpaperCounts[$child->id] ?? 0;
    //         }
    //     }

    //     return view('admin.users.edit', compact('user'));
    // }
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
     * Store wallpaper for user (using existing WallpaperController logic)
     */
    public function storeWallpaper(Request $request, $id, $categoryId)
    {
        // Validate the request
        $request->validate([
            'title' => 'nullable|max:255',
            'file' => 'required|file|mimes:jpg,jpeg,png,gif,mp4,webm,webp,mov,avi,m4v|max:102400',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'category_id' => 'required',
            'media_type' => 'nullable|in:image,video,live',
        ]);

        // Get the user
        $user = User::findOrFail($id);

        // Use the existing WallpaperController logic but set owner_user_id
        $uploaded = $request->file('file');
        $extension = $uploaded->getClientOriginalExtension();
        $fileName = time() . '_' . Str::random(6) . '.' . $extension;
        $mimeType = $uploaded->getClientMimeType();
        $fileSize = $uploaded->getSize();

        // Determine media type
        $mediaType = $request->input('media_type');
        if (!$mediaType) {
            if (str_starts_with($mimeType, 'video/')) {
                $mediaType = 'video';
            } elseif ($mimeType === 'image/gif') {
                $mediaType = 'live';
            } else {
                $mediaType = 'image';
            }
        }

        $tmpDir = sys_get_temp_dir();
        $tmpFile = $tmpDir . DIRECTORY_SEPARATOR . $fileName;

        try {
            $uploaded->move($tmpDir, $fileName);
        } catch (\Throwable $e) {
            Log::error('Failed to move uploaded file to tmp: ' . $e->getMessage());
            return back()->withErrors('Upload failed (move).');
        }

        // Upload file to B2
        $b2Path = 'wallpapers/' . $fileName;
        try {
            $stream = fopen($tmpFile, 'r');
            if ($stream === false) {
                throw new \RuntimeException('Failed to open tmp file for reading: ' . $tmpFile);
            }
            $ok = Storage::disk('b2')->put($b2Path, $stream);
            if (is_resource($stream)) fclose($stream);
            if (!$ok) throw new \RuntimeException('Storage::put returned falsy for ' . $b2Path);
        } catch (\Throwable $e) {
            Log::error('Failed to upload file to B2: ' . $e->getMessage());
            @unlink($tmpFile);
            return back()->withErrors('Upload to storage failed.');
        }

        $fileUrl = null;
        try {
            $fileUrl = Storage::disk('b2')->url($b2Path);
        } catch (\Throwable $e) {
            Log::warning('Could not generate public URL for uploaded file: ' . $e->getMessage());
        }

        // Thumbnail handling (same as existing logic)
        $thumbnailPath = null;
        $thumbnailUrl = null;

        // 1) explicit thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $thumb = $request->file('thumbnail');
            $thumbName = 'thumb_' . time() . '_' . Str::random(6) . '.' . $thumb->getClientOriginalExtension();

            try {
                $putResult = Storage::disk('b2')->putFileAs('wallpapers/thumbnails', $thumb, $thumbName);
                if ($putResult) {
                    $thumbnailPath = 'wallpapers/thumbnails/' . $thumbName;
                    $thumbnailUrl = Storage::disk('b2')->url($thumbnailPath);
                }
            } catch (\Throwable $e) {
                Log::error('Failed to upload explicit thumbnail to B2: ' . $e->getMessage());
            }
        }

        // 2) auto-generate thumbnail if video
        if ($mediaType === 'video' && !$thumbnailPath) {
            // $ffmpegBin = '/opt/homebrew/bin/ffmpeg';
            $ffmpegBin = '/usr/bin/ffmpeg';
            if (!$ffmpegBin) {
                Log::error('FFmpeg not found on server!');
            } else {
                $autoName = 'thumb_' . time() . '_' . Str::random(6) . '.jpg';
                $localThumbDir = public_path('uploads/wallpapers/thumbnails');

                if (!file_exists($localThumbDir)) {
                    mkdir($localThumbDir, 0777, true);
                }

                $tmpThumb = $localThumbDir . DIRECTORY_SEPARATOR . $autoName;
                $cmd = $ffmpegBin . ' -y -i ' . escapeshellarg($tmpFile) . ' -ss 00:00:00 -vframes 1 -q:v 2 ' . escapeshellarg($tmpThumb) . ' 2>&1';
                $output = shell_exec($cmd);

                if (file_exists($tmpThumb)) {
                    $thumbB2Path = 'wallpapers/thumbnails/' . $autoName;
                    try {
                        $stream = fopen($tmpThumb, 'r');
                        if ($stream === false) throw new \RuntimeException('Failed to open thumbnail for upload');
                        $ok = Storage::disk('b2')->put($thumbB2Path, $stream);
                        if (is_resource($stream)) fclose($stream);
                        if ($ok) {
                            $thumbnailPath = $thumbB2Path;
                            $thumbnailUrl = Storage::disk('b2')->url($thumbB2Path);
                        }
                    } catch (\Throwable $e) {
                        Log::error("Error uploading thumbnail to B2: " . $e->getMessage());
                    }
                    @unlink($tmpThumb);
                }
            }
        }

        @unlink($tmpFile);

        // Create the wallpaper record with user assignment
        Wallpaper::create([
            'title'             => $request->title,
            'category_id'       => $categoryId,
            'file_path'         => $b2Path,
            'media_type'        => $mediaType,
            'mime_type'         => $mimeType,
            'file_size'         => $fileSize,
            'file_url'          => $fileUrl,
            'thumbnail_url'     => $thumbnailUrl,
            'owner_user_id'     => $id, // Assign to the specific user
            'is_admin'          => 1, // Marked as admin-uploaded
        ]);

        return redirect()->route('admin.users.edit', $id)->with('success', 'Wallpaper uploaded successfully.');
    }


    /**
     * Delete user's wallpaper
     */
    public function deleteWallpaper($userId, $wallpaperId)
    {
        $wallpaper = Wallpaper::where('owner_user_id', $userId)->findOrFail($wallpaperId);
        $wallpaper->delete();

        return redirect()->back()->with('success', 'Wallpaper deleted successfully.');
    }

    // Add these methods to UserController

    /**
     * Store category for user
     */
    public function storeCategory(Request $request, $id)
    {
        $request->validate([
            'category_name' => 'required|max:255',
            'parent_id' => 'required|exists:wallpaper_categories,id'
        ]);

        // Verify parent is one of the default categories
        $parentCategory = WallpaperCategory::whereIn('category_name', ['Wallpapers', 'Live Wallpapers'])
            ->whereNull('owner_user_id')
            ->findOrFail($request->parent_id);

        WallpaperCategory::create([
            'category_name' => $request->category_name,
            'parent_id' => $request->parent_id,
            'owner_user_id' => $id, // User category
            'is_active' => 1,
            'depth' => 1,
            'order' => 0,
        ]);

        return redirect()->route('admin.users.categories.index', $id)->with('success', 'Category created successfully.');
    }

    // In app/Http/Controllers/UserController.php
    /**
     * Show wallpapers in a specific category
     */
    public function categoryWallpapers($userId, $categoryId)
    {
        $user = User::findOrFail($userId);
        $category = WallpaperCategory::where('owner_user_id', $userId)
            ->with('parent')
            ->findOrFail($categoryId);

        // Base query
        $wallpapersQuery = Wallpaper::where('category_id', $categoryId)
            ->where('owner_user_id', $userId);

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

        $categories = WallpaperCategory::where('owner_user_id', $id)
            ->withCount('wallpapers')
            ->with('parent')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.users.categories.index', compact('user', 'categories'));
    }

    /**
     * Show form to create subcategory for user
     */
    public function createCategory($id)
    {
        $user = User::findOrFail($id);
        $defaultCategories = WallpaperCategory::whereIn('category_name', ['Wallpapers', 'Live Wallpapers'])
            ->whereNull('owner_user_id')
            ->get();

        return view('admin.users.categories.create', compact('user', 'defaultCategories'));
    }

    /**
     * Show form to upload wallpaper to specific category
     */
    public function createCategoryWallpaper($userId, $categoryId)
    {
        $user = User::findOrFail($userId);
        $category = WallpaperCategory::where('owner_user_id', $userId)->findOrFail($categoryId);

        return view('admin.users.categories.wallpapers.create', compact('user', 'category'));
    }

    /**
     * Store wallpaper in specific category
     */
    /**
     * Enhanced storeCategoryWallpaper method that handles both single and multiple files
     */
    public function storeCategoryWallpaper(Request $request, $userId, $categoryId)
    {
        $user = User::findOrFail($userId);
        $category = WallpaperCategory::where('owner_user_id', $userId)->findOrFail($categoryId);
        $parentCategory = $category->parent;

        // Check if multiple files are being uploaded
        if ($request->hasFile('files')) {
            // return $parentCategory->category_name;
            // Multiple file upload
            if ($parentCategory->category_name == 'Wallpapers') {
                $request->validate([
                    'files' => 'required|array|max:20',
                    'files.*' => 'file|mimes:jpg,jpeg,png,webp|max:102400',
                ]);
            } else {
                $request->validate([
                    'files' => 'required|array|max:10',
                    'files.*' => 'file|mimes:mp4,webm,mov,avi,gif|max:102400',
                    'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
                ]);
            }
            $uploadedFiles = $request->file('files');
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($uploadedFiles as $file) {
                try {
                    // Create individual request for each file
                    $fileRequest = new Request();
                    $fileRequest->files->set('file', $file);
                    $fileRequest->merge([
                        'title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                        'category_id' => $categoryId,
                        'media_type' => $parentCategory->category_name == 'Wallpapers' ? 'image' : 'video',
                    ]);

                    if ($request->hasFile('thumbnail')) {
                        $fileRequest->files->set('thumbnail', $request->file('thumbnail'));
                    }

                    // Call storeWallpaper for each file
                    $this->storeWallpaper($fileRequest, $userId, $categoryId);
                    $successCount++;
                } catch (\Exception $e) {
                    Log::error('Error uploading file ' . $file->getClientOriginalName() . ': ' . $e->getMessage());
                    $errorCount++;
                    $errors[] = "Error with file " . $file->getClientOriginalName() . ": " . $e->getMessage();
                }
            }

            $message = "Upload completed: {$successCount} files uploaded successfully.";
            if ($errorCount > 0) {
                $message .= " {$errorCount} files failed to upload.";
                if (!empty($errors)) {
                    session()->flash('upload_errors', $errors);
                }
            }

            return redirect()->route('admin.users.categories.wallpapers', [$userId, $categoryId])
                ->with('success', $message);
        } else {
            // Single file upload - your existing logic
            $request->validate([
                'title' => 'nullable|max:255',
                'file' => 'required|file|mimes:jpg,jpeg,png,gif,mp4,webm,webp,mov,avi,m4v|max:102400',
                'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
                'media_type' => 'nullable|in:image,video,live',
            ]);

            $this->storeWallpaper($request, $userId, $categoryId);

            return redirect()->route('admin.users.categories.wallpapers', [$userId, $categoryId])->with('success', 'Wallpaper uploaded successfully.');
        }
    }


    /**
     * Store multiple wallpapers in specific category
     */
    // public function storeCategoryWallpaper(Request $request, $userId, $categoryId)
    // {
    //     $user = User::findOrFail($userId);
    //     $category = WallpaperCategory::where('owner_user_id', $userId)->findOrFail($categoryId);
    //     $parentCategory = $category->parent;

    //     // Validation for multiple files
    //     if ($parentCategory->category_name == 'Wallpapers') {
    //         $request->validate([
    //             'files' => 'required|array|max:20',
    //             'files.*' => 'file|mimes:jpg,jpeg,png,webp|max:102400',
    //         ]);
    //     } else { // Live Wallpapers
    //         $request->validate([
    //             'files' => 'required|array|max:10',
    //             'files.*' => 'file|mimes:mp4,webm,mov,avi,gif|max:102400',
    //             'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
    //         ]);
    //     }

    //     $uploadedFiles = $request->file('files');
    //     $successCount = 0;
    //     $errorCount = 0;
    //     $errors = [];

    //     foreach ($uploadedFiles as $file) {
    //         try {
    //             // Determine media type
    //             $mimeType = $file->getClientMimeType();
    //             $mediaType = 'image';

    //             if ($parentCategory->category_name == 'Live Wallpapers') {
    //                 $mediaType = ($mimeType === 'image/gif') ? 'live' : 'video';
    //             }

    //             // Upload logic for each file
    //             $extension = $file->getClientOriginalExtension();
    //             $fileName = time() . '_' . Str::random(6) . '_' . $file->getClientOriginalName();
    //             $fileSize = $file->getSize();

    //             $tmpDir = sys_get_temp_dir();
    //             $tmpFile = $tmpDir . DIRECTORY_SEPARATOR . $fileName;

    //             try {
    //                 $file->move($tmpDir, $fileName);
    //             } catch (\Throwable $e) {
    //                 Log::error('Failed to move uploaded file to tmp: ' . $e->getMessage());
    //                 $errorCount++;
    //                 $errors[] = "Failed to process file: " . $file->getClientOriginalName();
    //                 continue;
    //             }

    //             // Upload to B2
    //             $b2Path = 'wallpapers/' . $fileName;
    //             try {
    //                 $stream = fopen($tmpFile, 'r');
    //                 if ($stream === false) {
    //                     throw new \RuntimeException('Failed to open tmp file for reading: ' . $tmpFile);
    //                 }
    //                 $ok = Storage::disk('b2')->put($b2Path, $stream);
    //                 if (is_resource($stream)) fclose($stream);
    //                 if (!$ok) throw new \RuntimeException('Storage::put returned falsy for ' . $b2Path);
    //             } catch (\Throwable $e) {
    //                 Log::error('Failed to upload file to B2: ' . $e->getMessage());
    //                 @unlink($tmpFile);
    //                 $errorCount++;
    //                 $errors[] = "Failed to upload file: " . $file->getClientOriginalName();
    //                 continue;
    //             }

    //             $fileUrl = null;
    //             try {
    //                 $fileUrl = Storage::disk('b2')->url($b2Path);
    //             } catch (\Throwable $e) {
    //                 Log::warning('Could not generate public URL for uploaded file: ' . $e->getMessage());
    //             }

    //             // Thumbnail handling for Live Wallpapers
    //             $thumbnailPath = null;
    //             $thumbnailUrl = null;

    //             if ($parentCategory->category_name == 'Live Wallpapers' && $request->hasFile('thumbnail')) {
    //                 $thumb = $request->file('thumbnail');
    //                 $thumbName = 'thumb_' . time() . '_' . Str::random(6) . '.' . $thumb->getClientOriginalExtension();

    //                 try {
    //                     $putResult = Storage::disk('b2')->putFileAs('wallpapers/thumbnails', $thumb, $thumbName);
    //                     if ($putResult) {
    //                         $thumbnailPath = 'wallpapers/thumbnails/' . $thumbName;
    //                         $thumbnailUrl = Storage::disk('b2')->url($thumbnailPath);
    //                     }
    //                 } catch (\Throwable $e) {
    //                     Log::error('Failed to upload thumbnail to B2: ' . $e->getMessage());
    //                 }
    //             }

    //             @unlink($tmpFile);

    //             // Create wallpaper record
    //             Wallpaper::create([
    //                 'title'             => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
    //                 'category_id'       => $categoryId,
    //                 'file_path'         => $b2Path,
    //                 'media_type'        => $mediaType,
    //                 'mime_type'         => $mimeType,
    //                 'file_size'         => $fileSize,
    //                 'file_url'          => $fileUrl,
    //                 'thumbnail_url'     => $thumbnailUrl,
    //                 'owner_user_id'     => $userId,
    //                 'is_admin'          => 1,
    //             ]);

    //             $successCount++;
    //         } catch (\Exception $e) {
    //             Log::error('Error uploading file ' . $file->getClientOriginalName() . ': ' . $e->getMessage());
    //             $errorCount++;
    //             $errors[] = "Error with file " . $file->getClientOriginalName() . ": " . $e->getMessage();
    //         }
    //     }

    //     // Prepare response message
    //     $message = "Upload completed: {$successCount} files uploaded successfully.";
    //     if ($errorCount > 0) {
    //         $message .= " {$errorCount} files failed to upload.";
    //         if (!empty($errors)) {
    //             session()->flash('upload_errors', $errors);
    //         }
    //     }

    //     return redirect()->route('admin.users.categories.wallpapers', [$userId, $categoryId])
    //         ->with('success', $message);
    // }


    /**
     * Delete user category
     */
    // public function destroyCategory($userId, $categoryId)
    // {
    //     $category = WallpaperCategory::where('owner_user_id', $userId)->findOrFail($categoryId);
    //     $category->delete();

    //     return redirect()->route('admin.users.edit', $userId)->with('success', 'Category deleted successfully.');
    // }

    /**
     * Delete user category and all related wallpapers with file cleanup
     */
    public function destroyCategory($userId, $categoryId)
    {
        $category = WallpaperCategory::where('owner_user_id', $userId)->findOrFail($categoryId);

        // Get all wallpapers in this category
        $wallpapers = Wallpaper::where('category_id', $categoryId)
            ->where('owner_user_id', $userId)
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
            ->where('owner_user_id', $userId)
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
    public function apiKeys(Request $request)
    {
        $users = User::withCount('apiKeys');

        // Search functionality
        if ($request->has('search')) {
            $users->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $users = $users->paginate(10);

        return view('admin.api-keys.index', compact('users'));
    }

    /**
     * Generate API key for a user
     */
    public function generateApiKey(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'expires_in_days' => 'nullable|integer|min:1|max:365'
        ]);

        $user = User::findOrFail($request->user_id);

        $expiresAt = null;
        if ($request->filled('expires_in_days')) {
            $expiresAt = now()->addDays($request->input('expires_in_days'));
        }

        $apiKey = $user->createApiKey($request->name, $expiresAt);

        return redirect()->route('admin.api-keys.index')
            ->with('success', 'API key generated successfully for ' . $user->name)
            ->with('api_key', $apiKey->key); // Flash the key for one-time display
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

        return redirect()->route('admin.api-keys.index')
            ->with('success', 'API key deleted successfully');
    }
}
