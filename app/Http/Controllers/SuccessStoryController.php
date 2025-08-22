<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Claim;
use App\Models\FoundNotification;
use Illuminate\Http\Request;

class SuccessStoryController extends Controller
{
    /**
     * Display success stories
     */
    public function index()
    {
        // Get all resolved posts (both lost and found items that were successfully resolved)
        $successStories = Post::with([
            'user', 
            'claims' => function($query) {
                $query->where('status', 'accepted')->with('user');
            },
            'foundNotifications' => function($query) {
                $query->where('status', 'accepted')->with('finder');
            }
        ])
        ->where('status', 'resolved')
        ->latest('updated_at')
        ->paginate(12);

        // Calculate success statistics
        $totalPosts = Post::count();
        $resolvedPosts = Post::where('status', 'resolved')->count();
        $successRate = $totalPosts > 0 ? round(($resolvedPosts / $totalPosts) * 100, 1) : 0;
        
        $totalClaims = Claim::count();
        $acceptedClaims = Claim::where('status', 'accepted')->count();
        $claimSuccessRate = $totalClaims > 0 ? round(($acceptedClaims / $totalClaims) * 100, 1) : 0;

        $totalFoundNotifications = FoundNotification::count();
        $acceptedFoundNotifications = FoundNotification::where('status', 'accepted')->count();
        $foundNotificationSuccessRate = $totalFoundNotifications > 0 ? round(($acceptedFoundNotifications / $totalFoundNotifications) * 100, 1) : 0;

        $stats = [
            'total_posts' => $totalPosts,
            'resolved_posts' => $resolvedPosts,
            'success_rate' => $successRate,
            'total_claims' => $totalClaims,
            'accepted_claims' => $acceptedClaims,
            'claim_success_rate' => $claimSuccessRate,
            'total_found_notifications' => $totalFoundNotifications,
            'accepted_found_notifications' => $acceptedFoundNotifications,
            'found_notification_success_rate' => $foundNotificationSuccessRate,
            'lost_items_resolved' => Post::where('type', 'lost')->where('status', 'resolved')->count(),
            'found_items_resolved' => Post::where('type', 'found')->where('status', 'resolved')->count(),
            'active_users' => \App\Models\User::whereHas('posts', function($q) {
                $q->where('created_at', '>=', now()->subDays(30));
            })->count(),
        ];

        return view('success-stories.index', compact('successStories', 'stats'));
    }

    /**
     * Show a specific success story
     */
    public function show(Post $post)
    {
        // Ensure this is actually a success story
        if ($post->status !== 'resolved') {
            abort(404);
        }

        $post->load([
            'user', 
            'claims' => function($query) {
                $query->where('status', 'accepted')->with('user');
            },
            'foundNotifications' => function($query) {
                $query->where('status', 'accepted')->with('finder');
            },
            'comments.user'
        ]);

        // Get the resolution details
        $acceptedClaim = $post->claims->where('status', 'accepted')->first();
        $acceptedFoundNotification = $post->foundNotifications->where('status', 'accepted')->first();

        return view('success-stories.show', compact('post', 'acceptedClaim', 'acceptedFoundNotification'));
    }
}