<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\Message;
use Illuminate\Http\Request;

class FlaggedContentController extends Controller
{
    /**
     * Show user's flagged content
     */
    public function index()
    {
        $user = auth()->user();
        
        $flaggedPosts = $user->posts()->where('is_flagged', true)->get();
        $flaggedComments = $user->comments()->where('is_flagged', true)->get();
        $flaggedMessages = $user->sentMessages()->where('is_flagged', true)->get();
        
        return view('flagged-content.index', compact('flaggedPosts', 'flaggedComments', 'flaggedMessages'));
    }

    /**
     * Restore flagged post
     */
    public function restorePost(Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            abort(403);
        }

        $post->update([
            'is_flagged' => false,
            'flag_count' => 0
        ]);

        // Clear all reports for this post
        $post->reports()->delete();

        return back()->with('success', 'Post has been restored successfully.');
    }

    /**
     * Restore flagged comment
     */
    public function restoreComment(Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            abort(403);
        }

        $comment->update([
            'is_flagged' => false,
            'flag_count' => 0
        ]);

        // Clear all reports for this comment
        $comment->reports()->delete();

        return back()->with('success', 'Comment has been restored successfully.');
    }

    /**
     * Restore flagged message
     */
    public function restoreMessage(Message $message)
    {
        if ($message->sender_id !== auth()->id()) {
            abort(403);
        }

        $message->update([
            'is_flagged' => false,
            'flag_count' => 0
        ]);

        // Clear all reports for this message
        $message->reports()->delete();

        return back()->with('success', 'Message has been restored successfully.');
    }
}