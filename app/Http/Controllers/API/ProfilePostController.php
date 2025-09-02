<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProfilePost;
use App\Models\Wallpaper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfilePostController extends Controller
{
    // Public: list a user's profile posts
    public function listByUser($userId, Request $request)
    {
        $perPage = min((int)($request->query('limit', 20)), 100);
        $posts = ProfilePost::with(['owner', 'wallpaper'])
            ->where('owner_user_id', $userId)
            ->latest()
            ->paginate($perPage);

        return response()->json(['data' => $posts->items(), 'next_cursor' => $posts->nextPageUrl() ? (string)$posts->currentPage() + 1 : null]);
    }

    // Public: show a single profile post
    public function show($postId)
    {
        $post = ProfilePost::with(['owner', 'wallpaper'])->findOrFail($postId);
        return response()->json($post);
    }

    // Auth: create a profile post by favoriting/reposting a wallpaper
    public function store(Request $request)
    {
        $validated = $request->validate([
            'wallpaper_id' => 'required|exists:wallpapers,id',
            'caption' => 'nullable|string|max:255',
        ]);

        $userId = $request->user()->id;

        // Ensure this wallpaper is admin-uploaded (optional if your schema distinguishes admins)
        // Assuming wallpapers are always admin uploaded per requirement.

        // Create unique reference (owner_user_id + wallpaper_id)
        $post = ProfilePost::firstOrCreate(
            [
                'owner_user_id' => $userId,
                'wallpaper_id' => $validated['wallpaper_id'],
            ],
            [
                'caption' => $validated['caption'] ?? null,
            ]
        );

        return response()->json($post, $post->wasRecentlyCreated ? 201 : 200);
    }

    // Auth: update caption
    public function update($postId, Request $request)
    {
        $request->validate(['caption' => 'nullable|string|max:255']);
        $post = ProfilePost::findOrFail($postId);
        abort_unless($post->owner_user_id === $request->user()->id, 403);
        $post->caption = $request->input('caption');
        $post->save();
        return response()->json($post);
    }

    // Auth: delete profile post (soft delete)
    public function destroy($postId, Request $request)
    {
        $post = ProfilePost::findOrFail($postId);
        abort_unless($post->owner_user_id === $request->user()->id, 403);
        $post->delete();
        return response()->json([], 204);
    }

    // Auth: following feed (posts by people I follow)
    public function followingFeed(Request $request)
    {
        $userId = $request->user()->id;
        $perPage = min((int)($request->query('limit', 20)), 100);

        $posts = ProfilePost::with(['owner', 'wallpaper'])
            ->whereIn('owner_user_id', function ($q) use ($userId) {
                $q->select('followee_id')
                    ->from('follows')
                    ->where('follower_id', $userId);
            })
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'data' => $posts->items(),
            'next_cursor' => $posts->nextPageUrl() ? (string)$posts->currentPage() + 1 : null
        ]);
    }
}
