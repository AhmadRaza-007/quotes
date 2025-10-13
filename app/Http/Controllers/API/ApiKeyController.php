<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\ApiKey;
use Illuminate\Support\Carbon;

class ApiKeyController extends Controller
{
    /**
     * Get all API keys for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $apiKeys = $user->apiKeys()->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $apiKeys
        ]);
    }

    /**
     * Generate a new API key
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'expires_in_days' => 'nullable|integer|min:1|max:365'
        ]);

        $user = $request->user();

        $expiresAt = null;
        if ($request->has('expires_in_days')) {
            $expiresAt = Carbon::now()->addDays($request->expires_in_days);
        }

        $apiKey = $user->createApiKey($request->name, $expiresAt);

        return response()->json([
            'success' => true,
            'message' => 'API key generated successfully',
            'data' => [
                'id' => $apiKey->id,
                'name' => $apiKey->name,
                'key' => $apiKey->key,
                'expires_at' => $apiKey->expires_at,
                'created_at' => $apiKey->created_at,
            ]
        ], 201);
    }

    /**
     * Show API key details
     */
    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        $apiKey = $user->apiKeys()->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $apiKey
        ]);
    }

    /**
     * Update API key (name or status)
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'is_active' => 'sometimes|boolean'
        ]);

        $user = $request->user();

        $apiKey = $user->apiKeys()->findOrFail($id);

        $apiKey->update($request->only(['name', 'is_active']));

        return response()->json([
            'success' => true,
            'message' => 'API key updated successfully',
            'data' => $apiKey
        ]);
    }

    /**
     * Delete an API key
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        $apiKey = $user->apiKeys()->findOrFail($id);

        $apiKey->delete();

        return response()->json([
            'success' => true,
            'message' => 'API key deleted successfully'
        ]);
    }

    /**
     * Regenerate an API key
     */
    public function regenerate(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        $apiKey = $user->apiKeys()->findOrFail($id);

        $newKey = \Illuminate\Support\Str::random(64);

        $apiKey->update([
            'key' => $newKey,
            'last_used_at' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'API key regenerated successfully',
            'data' => [
                'id' => $apiKey->id,
                'name' => $apiKey->name,
                'key' => $newKey,
                'expires_at' => $apiKey->expires_at,
            ]
        ]);
    }
}
