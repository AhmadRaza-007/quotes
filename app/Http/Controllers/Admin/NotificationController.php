<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display the notification management page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // return storage_path('firebase/live-wallpapers-d9a58-firebase-adminsdk-fbsvc-e057aef797.json');
        $totalUsers = User::count();
        $totalDevices = UserDevice::where('is_active', true)->count();
        $recentDevices = UserDevice::with('user')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.notifications.index', compact(
            'totalUsers',
            'totalDevices',
            'recentDevices'
        ));
    }

    /**
     * Send notification to all users
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendToAll(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:500',
            'type' => 'nullable|string|max:50',
        ]);

        try {
            $result = $this->notificationService->sendToAllUsers(
                $request->title,
                $request->body,
                [
                    'type' => $request->type ?? 'admin_announcement',
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Notification sent successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notification to specific user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendToUser(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:500',
            'type' => 'nullable|string|max:50',
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            $result = $this->notificationService->sendToUser(
                $user,
                $request->title,
                $request->body,
                [
                    'type' => $request->type ?? 'admin_message',
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Notification sent successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get users list for dropdown
     *
     * @return JsonResponse
     */
    public function getUsers(): JsonResponse
    {
        $users = User::select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }

    /**
     * Get notification statistics
     *
     * @return JsonResponse
     */
    public function getStats(): JsonResponse
    {
        $totalUsers = User::count();
        $totalDevices = UserDevice::where('is_active', true)->count();
        $devicesByPlatform = UserDevice::where('is_active', true)
            ->selectRaw('platform, COUNT(*) as count')
            ->groupBy('platform')
            ->get();

        return response()->json([
            'success' => true,
            'stats' => [
                'total_users' => $totalUsers,
                'total_devices' => $totalDevices,
                'devices_by_platform' => $devicesByPlatform
            ]
        ]);
    }

    /**
     * Send wallpaper notification to all users
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendWallpaperNotification(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:500',
            'wallpaper_id' => 'required|exists:wallpapers,id',
            'category_id' => 'required|exists:wallpaper_categories,id',
            'parent_id' => 'required|exists:wallpaper_categories,id',
            'thumbnail_url' => 'required|url',
            'media_type' => 'required|string|max:50',
        ]);

        try {
            $result = $this->notificationService->sendToAllUsers(
                $request->title,
                $request->message,
                [
                    'type' => 'new_wallpaper',
                    'wallpaper_id' => $request->wallpaper_id,
                    'category_id' => $request->category_id,
                    'image' => $request->thumbnail_url,
                    'parent_id' => $request->parent_id,
                    'media_type' => $request->media_type,
                    'click_action' => 'NOTIFICATION_CLICK'
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Wallpaper notification sent successfully',
                'sent_count' => $result['sent_count'] ?? 0,
                'failure_count' => $result['failure_count'] ?? 0,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send wallpaper notification: ' . $e->getMessage()
            ], 500);
        }
    }
}
