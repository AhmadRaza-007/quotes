<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FollowController extends Controller
{
    // POST /api/users/{userId}/follow
    public function follow($userId, Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $authId = (int) $user->id;
        $targetId = (int) $userId;

        if ($authId === $targetId) {
            return response()->json(['message' => 'Cannot follow yourself'], 400);
        }

        try {
            Follow::firstOrCreate([
                'follower_id' => $authId,
                'followee_id' => $targetId,
            ]);

            $followersCount = Follow::where('followee_id', $targetId)->count();

            return response()->json([
                'following' => true,
                'followers_count' => (int) $followersCount,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('FollowController::follow error: ' . $e->getMessage(), ['follower_id' => $authId, 'followee_id' => $targetId]);
            return response()->json(['message' => 'Server error'], 500);
        }
    }

    // POST /api/users/{userId}/unfollow
    public function unfollow($userId, Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $authId = (int) $user->id;
        $targetId = (int) $userId;

        try {
            // Use the model so events / model behaviour is preserved
            Follow::where('follower_id', $authId)
                ->where('followee_id', $targetId)
                ->delete();

            $followersCount = Follow::where('followee_id', $targetId)->count();

            return response()->json([
                'following' => false,
                'followers_count' => (int) $followersCount,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('FollowController::unfollow error: ' . $e->getMessage(), ['follower_id' => $authId, 'followee_id' => $targetId]);
            return response()->json(['message' => 'Server error'], 500);
        }
    }

    // GET /api/users/{userId}/followers
    public function followers($userId, Request $request)
    {
        $perPage = min((int) $request->query('limit', 20), 100);

        $page = (int) max(1, $request->query('page', 1));

        $query = User::select('users.id', 'users.name', 'users.avatar')
            ->join('follows', 'users.id', '=', 'follows.follower_id')
            ->where('follows.followee_id', (int)$userId)
            ->orderBy('follows.created_at', 'desc');

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $paginator->items(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total_pages' => $paginator->lastPage(),
            'next_page' => $paginator->currentPage() < $paginator->lastPage() ? $paginator->currentPage() + 1 : null,
        ], 200);
    }

    // GET /api/users/{userId}/following
    public function following($userId, Request $request)
    {
        $perPage = min((int) $request->query('limit', 20), 100);

        $page = (int) max(1, $request->query('page', 1));

        $query = User::select('users.id', 'users.name', 'users.avatar')
            ->join('follows', 'users.id', '=', 'follows.followee_id')
            ->where('follows.follower_id', (int)$userId)
            ->orderBy('follows.created_at', 'desc');

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $paginator->items(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total_pages' => $paginator->lastPage(),
            'next_page' => $paginator->currentPage() < $paginator->lastPage() ? $paginator->currentPage() + 1 : null,
        ], 200);
    }
}
