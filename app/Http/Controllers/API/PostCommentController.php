<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PostComment;
use App\Models\ProfilePost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostCommentController extends Controller
{
    // Public: list comments on a profile post
    public function index($postId, Request $request)
    {
        $perPage = min((int)($request->query('limit', 20)), 100);
        $comments = PostComment::with('user')
            ->where('profile_post_id', $postId)
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);

        return response()->json([
            'data' => $comments->items(),
            'next_cursor' => $comments->nextPageUrl() ? (string)$comments->currentPage() + 1 : null
        ]);
    }

    // Auth: add a comment
    public function store($postId, Request $request)
    {
        $request->validate(['text' => 'required|string|max:1000']);
        $post = ProfilePost::findOrFail($postId);
        $userId = $request->user()->id;

        $comment = null;
        DB::transaction(function () use ($post, $userId, $request, &$comment) {
            $comment = PostComment::create([
                'profile_post_id' => $post->id,
                'user_id' => $userId,
                'text' => $request->input('text'),
            ]);
            $post->increment('comments_count');
        });

        return response()->json($comment, 201);
    }

    // Auth: edit own comment
    public function update($commentId, Request $request)
    {
        $request->validate(['text' => 'required|string|max:1000']);
        $comment = PostComment::findOrFail($commentId);
        abort_unless($comment->user_id === $request->user()->id, 403);
        $comment->text = $request->input('text');
        $comment->save();
        return response()->json($comment);
    }

    // Auth: delete own comment
    public function destroy($commentId, Request $request)
    {
        $comment = PostComment::findOrFail($commentId);
        abort_unless($comment->user_id === $request->user()->id, 403);

        DB::transaction(function () use ($comment) {
            $post = ProfilePost::find($comment->profile_post_id);
            $comment->delete();
            if ($post && $post->comments_count > 0) {
                $post->decrement('comments_count');
            }
        });

        return response()->json([], 204);
    }
}
