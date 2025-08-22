<?php

namespace App\Http\Controllers;

use App\Models\FoundNotification;
use App\Models\Post;
use App\Notifications\NewFoundNotification;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FoundNotificationController extends Controller
{
    use AuthorizesRequests;

    /**
     * Store a new found notification
     */
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'found_location' => 'required|string|max:255',
            'contact_info' => 'required|string|max:255'
        ]);

        // Check if user already has a pending found notification for this post
        $existingNotification = FoundNotification::where('post_id', $post->id)
            ->where('finder_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if ($existingNotification) {
            return back()->with('error', 'You already have a pending found notification for this item.');
        }

        $validated['post_id'] = $post->id;
        $validated['finder_id'] = auth()->id();

        $foundNotification = FoundNotification::create($validated);

        // Send in-app notification to post owner
        $post->user->notify(new NewFoundNotification($foundNotification));

        return back()->with('success', 'Your found notification has been sent to the owner!');
    }

    /**
     * Accept a found notification
     */
    public function accept(FoundNotification $foundNotification)
    {
        $this->authorize('update', $foundNotification->post);

        $foundNotification->update(['status' => 'accepted']);
        $foundNotification->post->update(['status' => 'resolved']);

        // Reject all other pending found notifications for this post
        FoundNotification::where('post_id', $foundNotification->post_id)
            ->where('id', '!=', $foundNotification->id)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);

        return back()->with('success', 'Found notification accepted successfully!');
    }

    /**
     * Reject a found notification
     */
    public function reject(FoundNotification $foundNotification)
    {
        $this->authorize('update', $foundNotification->post);

        $foundNotification->update(['status' => 'rejected']);

        return back()->with('success', 'Found notification rejected.');
    }

    /**
     * Show user's found notifications
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all'); // all, pending, accepted, rejected
        $sort = $request->get('sort', 'latest'); // latest, oldest
        $search = $request->get('search');

        $query = FoundNotification::with(['post', 'finder'])
            ->where('finder_id', auth()->id());

        // Apply status filter
        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        // Apply search filter
        if ($search) {
            $query->whereHas('post', function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Apply sorting
        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $foundNotifications = $query->paginate(10);

        return view('found-notifications.index', compact('foundNotifications', 'filter', 'sort', 'search'));
    }

    /**
     * Show found notifications for user's posts
     */
    public function received(Request $request)
    {
        $filter = $request->get('filter', 'all'); // all, pending, accepted, rejected
        $sort = $request->get('sort', 'latest'); // latest, oldest
        $search = $request->get('search');

        $query = FoundNotification::with(['post', 'finder'])
            ->whereHas('post', function ($query) {
                $query->where('user_id', auth()->id());
            });

        // Apply status filter
        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('message', 'like', '%' . $search . '%')
                  ->orWhere('found_location', 'like', '%' . $search . '%')
                  ->orWhereHas('finder', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('post', function ($postQuery) use ($search) {
                      $postQuery->where('title', 'like', '%' . $search . '%');
                  });
            });
        }

        // Apply sorting
        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $foundNotifications = $query->paginate(10);

        return view('found-notifications.received', compact('foundNotifications', 'filter', 'sort', 'search'));
    }
}