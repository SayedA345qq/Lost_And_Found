<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Post;
use App\Notifications\NewClaimNotification;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ClaimController extends Controller
{
    use AuthorizesRequests;
    /**
     * Store a new claim
     */
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'contact_info' => 'required|string|max:255'
        ]);

        // Check if user already has a pending claim for this post
        $existingClaim = Claim::where('post_id', $post->id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if ($existingClaim) {
            return back()->with('error', 'You already have a pending claim for this item.');
        }

        $validated['post_id'] = $post->id;
        $validated['user_id'] = auth()->id();

        $claim = Claim::create($validated);

        // Load relationships for notification
        $claim->load(['user', 'post']);

        // Send in-app notification to post owner
        $post->user->notify(new NewClaimNotification($claim));

        return back()->with('success', 'Your claim has been submitted successfully!');
    }

    /**
     * Accept a claim
     */
    public function accept(Claim $claim)
    {
        $this->authorize('update', $claim->post);

        $claim->update(['status' => 'accepted']);
        $claim->post->update(['status' => 'resolved']);

        // Load relationships for notification
        $claim->load(['user', 'post.user']);

        // Send acceptance notification to the claimer
        $claim->user->notify(new NewClaimNotification($claim, 'accepted'));

        // Get all other pending claims for this post to reject them
        $otherClaims = Claim::with(['user', 'post.user'])
            ->where('post_id', $claim->post_id)
            ->where('id', '!=', $claim->id)
            ->where('status', 'pending')
            ->get();

        // Reject all other pending claims and notify their users
        foreach ($otherClaims as $otherClaim) {
            $otherClaim->update(['status' => 'rejected']);
            $otherClaim->user->notify(new NewClaimNotification($otherClaim, 'rejected'));
        }

        return back()->with('success', 'Claim accepted successfully!');
    }

    /**
     * Reject a claim
     */
    public function reject(Claim $claim)
    {
        $this->authorize('update', $claim->post);

        $claim->update(['status' => 'rejected']);

        // Load relationships for notification
        $claim->load(['user', 'post.user']);

        // Send rejection notification to the claimer
        $claim->user->notify(new NewClaimNotification($claim, 'rejected'));

        return back()->with('success', 'Claim rejected.');
    }

    /**
     * Show user's claims
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all'); // all, pending, accepted, rejected
        $sort = $request->get('sort', 'latest'); // latest, oldest
        $search = $request->get('search');

        $query = Claim::with(['post', 'user'])
            ->where('user_id', auth()->id());

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

        $claims = $query->paginate(10);

        return view('claims.index', compact('claims', 'filter', 'sort', 'search'));
    }

    /**
     * Show claims for user's posts
     */
    public function received(Request $request)
    {
        $filter = $request->get('filter', 'all'); // all, pending, accepted, rejected
        $sort = $request->get('sort', 'latest'); // latest, oldest
        $search = $request->get('search');

        $query = Claim::with(['post', 'user'])
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
                  ->orWhereHas('user', function ($userQuery) use ($search) {
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

        $claims = $query->paginate(10);

        return view('claims.received', compact('claims', 'filter', 'sort', 'search'));
    }
}