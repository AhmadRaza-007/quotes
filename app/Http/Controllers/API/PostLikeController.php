<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PostLike;
use App\Models\ProfilePost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostLikeController extends Controller
{
    // Public: list users who liked a post
    public function index($postId, Request $request)
    {
        $perPage = min((int)($request->query('limit', 20)), 100);
        $likes = PostLike::with('user')
            ->where('profile_post_id', $postId)
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'data' => $likes->getCollection()->pluck('user')->values(),
            'next_cursor' => $likes->nextPageUrl() ? (string)$likes->currentPage() + 1 : null
        ]);
    }

    // Auth: like (idempotent)
    public function like($postId, Request $request)
    {
        $userId = $request->user()->id;
        $post = ProfilePost::findOrFail($postId);

        $created = false;

        DB::transaction(function () use ($userId, $post, &$created) {
            $like = PostLike::firstOrCreate([
                'user_id' => $userId,
                'profile_post_id' => $post->id,
            ]);
            $created = $like->wasRecentlyCreated;
            if ($created) {
                $post->increment('likes_count');
            }
        });

        $post->refresh();
        return response()->json(['liked' => true, 'likes_count' => $post->likes_count]);
    }

    // Auth: unlike (idempotent)
    public function unlike($postId, Request $request)
    {
        $userId = $request->user()->id;
        $post = ProfilePost::findOrFail($postId);

        DB::transaction(function () use ($userId, $post) {
            $deleted = PostLike::where('user_id', $userId)
                ->where('profile_post_id', $post->id)
                ->delete();
            if ($deleted) {
                $post->decrement('likes_count');
            }
        });

        $post->refresh();
        return response()->json(['liked' => false, 'likes_count' => $post->likes_count]);
    }
}
