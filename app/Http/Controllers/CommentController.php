<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CommentController extends Controller
{
    use AuthorizesRequests;
    /**
     * Store a new comment
     */
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $validated['post_id'] = $post->id;
        $validated['user_id'] = auth()->id();

        Comment::create($validated);

        return back()->with('success', 'Comment added successfully!');
    }

    /**
     * Delete a comment
     */
    public function destroy(Comment $comment)
    {
        // Only post owner or comment author can delete
        if (auth()->id() !== $comment->user_id && auth()->id() !== $comment->post->user_id) {
            abort(403);
        }

        $comment->delete();

        return back()->with('success', 'Comment deleted successfully!');
    }
}
