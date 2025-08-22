<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Claim;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // User's statistics
        $userStats = [
            'posts' => $user->posts()->count(),
            'claims_made' => $user->claims()->count(),
            'claims_received' => Claim::whereHas('post', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count(),
            'resolved_posts' => $user->posts()->where('status', 'resolved')->count()
        ];

        // Public statistics
        $totalPosts = Post::active()->count();
        $resolvedPosts = Post::where('status', 'resolved')->count();
        $successRate = $totalPosts > 0 ? round(($resolvedPosts / $totalPosts) * 100, 1) : 0;
        
        $publicStats = [
            'total_posts' => $totalPosts,
            'resolved_posts' => $resolvedPosts,
            'success_rate' => $successRate,
            'active_users' => User::whereHas('posts', function($q) {
                $q->where('created_at', '>=', now()->subDays(30));
            })->count(),
            'lost_items' => Post::active()->where('type', 'lost')->count(),
            'found_items' => Post::active()->where('type', 'found')->count(),
            'success_stories' => Post::where('status', 'resolved')->whereHas('claims', function($q) {
                $q->where('status', 'accepted');
            })->count()
        ];

        return view('dashboard', compact('userStats', 'publicStats'));
    }
}