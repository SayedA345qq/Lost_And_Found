<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Post;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * Show all conversations for the authenticated user
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        
        // Get filter and sort parameters
        $filter = $request->get('filter', 'all'); // all, unread, read
        $sort = $request->get('sort', 'latest'); // latest, oldest, unread_first
        $search = $request->get('search');
        
        // Base query for conversations
        $query = Message::select('post_id', 
            DB::raw('CASE WHEN sender_id = ' . $userId . ' THEN receiver_id ELSE sender_id END as other_user_id'),
            DB::raw('MAX(created_at) as last_message_at'),
            DB::raw('COUNT(CASE WHEN receiver_id = ' . $userId . ' AND is_read = 0 THEN 1 END) as unread_count')
        )
        ->forUser($userId)
        ->active();

        // Apply filters
        if ($filter === 'unread') {
            $query->having('unread_count', '>', 0);
        } elseif ($filter === 'read') {
            $query->having('unread_count', '=', 0);
        }

        $conversationsData = $query->groupBy('post_id', 'other_user_id');

        // Apply sorting
        switch ($sort) {
            case 'oldest':
                $conversationsData->orderBy('last_message_at', 'asc');
                break;
            case 'unread_first':
                $conversationsData->orderBy('unread_count', 'desc')
                                 ->orderBy('last_message_at', 'desc');
                break;
            default: // latest
                $conversationsData->orderBy('last_message_at', 'desc');
                break;
        }

        $conversationsData = $conversationsData->get();

        // Transform the data to include related models
        $conversations = $conversationsData->map(function ($conversation) {
            $conversation->post = Post::find($conversation->post_id);
            $conversation->other_user = User::find($conversation->other_user_id);
            return $conversation;
        });

        // Apply search filter if provided
        if ($search) {
            $conversations = $conversations->filter(function ($conversation) use ($search) {
                return stripos($conversation->other_user->name, $search) !== false ||
                       stripos($conversation->post->title, $search) !== false;
            });
        }

        return view('messages.index', compact('conversations', 'filter', 'sort', 'search'));
    }

    /**
     * Show conversation for a specific post and user
     */
    public function show(Post $post, User $user)
    {
        $userId = auth()->id();
        
        // Get messages between current user and specified user for this post
        // Only show messages that are not cleared for the current user
        $messages = Message::where('post_id', $post->id)
            ->where(function ($query) use ($userId, $user) {
                $query->where(function ($q) use ($userId, $user) {
                    // Messages sent by current user to other user (not cleared by current user)
                    $q->where('sender_id', $userId)
                      ->where('receiver_id', $user->id)
                      ->where('sender_deleted', false);
                })->orWhere(function ($q) use ($userId, $user) {
                    // Messages sent by other user to current user (not cleared by current user)
                    $q->where('sender_id', $user->id)
                      ->where('receiver_id', $userId)
                      ->where('receiver_deleted', false);
                });
            })
            ->active()
            ->with(['sender', 'receiver'])
            ->orderBy('created_at')
            ->get();

        // Mark messages as read
        Message::where('post_id', $post->id)
            ->where('sender_id', $user->id)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('messages.show', compact('messages', 'post', 'user'));
    }

    /**
     * Store a new message
     */
    public function store(Request $request, Post $post, User $user)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $validated['sender_id'] = auth()->id();
        $validated['receiver_id'] = $user->id;
        $validated['post_id'] = $post->id;

        $message = Message::create($validated);

        // Load relationships for notification
        $message->load(['sender', 'receiver', 'post']);


        return back();
    }

    /**
     * Clear entire conversation for current user
     */
    public function clearConversation(Post $post, User $user)
    {
        $userId = auth()->id();
        
        try {
            // Get all messages in this conversation involving the current user
            $messages = Message::where('post_id', $post->id)
                ->where(function ($query) use ($userId, $user) {
                    $query->where(function ($q) use ($userId, $user) {
                        $q->where('sender_id', $userId)
                          ->where('receiver_id', $user->id);
                    })->orWhere(function ($q) use ($userId, $user) {
                        $q->where('sender_id', $user->id)
                          ->where('receiver_id', $userId);
                    });
                })
                ->get();

            $clearedCount = 0;
            foreach ($messages as $message) {
                // Skip if already cleared by this user
                if (($message->sender_id === $userId && $message->sender_deleted) ||
                    ($message->receiver_id === $userId && $message->receiver_deleted)) {
                    continue;
                }

                // Mark as cleared for current user
                if ($message->sender_id === $userId) {
                    $message->update(['sender_deleted' => true]);
                } else {
                    $message->update(['receiver_deleted' => true]);
                }

                // If both users cleared the conversation, permanently delete the message
                $message->refresh();
                if ($message->sender_deleted && $message->receiver_deleted) {
                    $message->forceDelete();
                }
                
                $clearedCount++;
            }

            if ($clearedCount > 0) {
                return redirect()->route('messages.index')
                    ->with('success', "Conversation cleared successfully.");
            } else {
                return back()->with('info', 'Conversation has already been cleared.');
            }
            
        } catch (\Exception $e) {
            \Log::error('Conversation clearing failed: ' . $e->getMessage(), [
                'post_id' => $post->id,
                'other_user_id' => $user->id,
                'current_user_id' => $userId
            ]);
            
            return back()->with('error', 'Failed to clear conversation. Please try again.');
        }
    }

    /**
     * Start a new conversation
     */
    public function create(Post $post)
    {
        // Prevent messaging yourself
        if ($post->user_id === auth()->id()) {
            return back()->with('error', 'You cannot message yourself.');
        }

        return redirect()->route('messages.show', [$post, $post->user]);
    }
}