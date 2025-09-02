<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FollowController extends Controller
{
    public function follow($userId, Request $request)
    {
        $authId = $request->user()->id;
        abort_if((int)$authId === (int)$userId, 400, 'Cannot follow yourself');

        $follow = Follow::firstOrCreate([
            'follower_id' => $authId,
            'followee_id' => $userId,
        ]);

        $followersCount = Follow::where('followee_id', $userId)->count();

        return response()->json([
            'following' => true,
            'followers_count' => $followersCount,
        ]);
    }

    public function unfollow($userId, Request $request)
    {
        $authId = $request->user()->id;
        DB::table('follows')
            ->where('follower_id', $authId)
            ->where('followee_id', $userId)
            ->delete();

        $followersCount = Follow::where('followee_id', $userId)->count();

        return response()->json([
            'following' => false,
            'followers_count' => $followersCount,
        ]);
    }

    public function followers($userId, Request $request)
    {
        $perPage = min((int)($request->query('limit', 20)), 100);
        $followers = DB::table('follows')
            ->join('users', 'users.id', '=', 'follows.follower_id')
            ->where('follows.followee_id', $userId)
            ->select('users.*')
            ->paginate($perPage);

        return response()->json([
            'data' => $followers->items(),
            'next_cursor' => $followers->nextPageUrl() ? (string)$followers->currentPage() + 1 : null
        ]);
    }

    public function following($userId, Request $request)
    {
        $perPage = min((int)($request->query('limit', 20)), 100);
        $following = DB::table('follows')
            ->join('users', 'users.id', '=', 'follows.followee_id')
            ->where('follows.follower_id', $userId)
            ->select('users.*')
            ->paginate($perPage);

        return response()->json([
            'data' => $following->items(),
            'next_cursor' => $following->nextPageUrl() ? (string)$following->currentPage() + 1 : null
        ]);
    }
}
