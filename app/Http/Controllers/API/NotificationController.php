<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Register device for push notifications
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function registerDevice(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required|string',
            'device_type' => 'nullable|string|in:ios,android,web',
            'platform' => 'nullable|string|in:ios,android,web',
            'app_version' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            $device = $this->notificationService->registerDevice(
                $user,
                $request->device_token,
                $request->device_type,
                $request->platform,
                $request->app_version
            );

            return response()->json([
                'success' => true,
                'message' => 'Device registered successfully',
                'device' => $device
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to register device: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unregister device for push notifications
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function unregisterDevice(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $this->notificationService->unregisterDevice($request->device_token);

            return response()->json([
                'success' => true,
                'message' => 'Device unregistered successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unregister device: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send test notification to current user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendTestNotification(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $result = $this->notificationService->sendToUser(
                $user,
                'Test Notification',
                'This is a test notification from Beast Wallpapers',
                [
                    'type' => 'test',
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                ]
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's registered devices
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserDevices(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $devices = $user->devices()->where('is_active', true)->get();

            return response()->json([
                'success' => true,
                'devices' => $devices
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch devices: ' . $e->getMessage()
            ], 500);
        }
    }
}
