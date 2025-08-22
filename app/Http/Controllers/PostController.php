<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Notifications\MissingPersonNotification;
use App\Services\NotificationCleanupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Post::with(['user', 'claims', 'comments'])
            ->active()
            ->latest();

        // Apply filters
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('location')) {
            $query->byLocation($request->location);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('date_from')) {
            $query->where('date_lost_found', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date_lost_found', '<=', $request->date_to);
        }

        // Sort
        if ($request->filled('sort') && $request->sort === 'oldest') {
            $query->oldest();
        }

        $posts = $query->paginate(12);

        $categories = ['pet', 'person', 'vehicle', 'electronics', 'documents', 'jewelry', 'clothing', 'keys', 'other'];

        return view('posts.index', compact('posts', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ['pet', 'person', 'vehicle', 'electronics', 'documents', 'jewelry', 'clothing', 'keys', 'other'];
        return view('posts.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|in:pet,person,vehicle,electronics,documents,jewelry,clothing,keys,other',
            'location' => 'required|string|max:255',
            'date_lost_found' => 'required|date|before_or_equal:today',
            'type' => 'required|in:lost,found',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('posts', 'public');
                $images[] = $path;
            }
        }

        $validated['user_id'] = auth()->id();
        $validated['images'] = $images;

        $post = Post::create($validated);

        // Send missing person notifications to all users if this is a person-related post
        if ($validated['category'] === 'person') {
            $notificationType = $validated['type'] === 'lost' ? 'lost' : 'found';
            
            // Get all users except the post creator
            $users = User::where('id', '!=', auth()->id())->get();
            
            foreach ($users as $user) {
                $user->notify(new MissingPersonNotification($post, $notificationType));
            }
        }

        return redirect()->route('posts.index')->with('success', 'Post created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load(['user', 'claims.user', 'foundNotifications.finder', 'comments.user', 'messages']);
        
        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        
        $categories = ['pet', 'person', 'vehicle', 'electronics', 'documents', 'jewelry', 'clothing', 'keys', 'other'];
        return view('posts.edit', compact('post', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|in:pet,person,vehicle,electronics,documents,jewelry,clothing,keys,other',
            'location' => 'required|string|max:255',
            'date_lost_found' => 'required|date|before_or_equal:today',
            'type' => 'required|in:lost,found',
            'status' => 'required|in:active,resolved',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $images = $post->images ?? [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('posts', 'public');
                $images[] = $path;
            }
        }

        $validated['images'] = $images;

        $post->update($validated);

        return redirect()->route('posts.show', $post)->with('success', 'Post updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $postTitle = $post->title;

        // Get count of affected notifications before deletion
        $affectedNotifications = NotificationCleanupService::getAffectedNotifications($post);
        $notificationCount = $affectedNotifications->count();

        // Delete associated images
        if ($post->images) {
            foreach ($post->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        // Delete the post (this will trigger the model's boot method to clean up related data)
        $post->delete();

        $message = "Post '{$postTitle}' deleted successfully!";
        if ($notificationCount > 0) {
            $message .= " Also cleaned up {$notificationCount} related notification(s).";
        }

        return redirect()->route('posts.index')->with('success', $message);
    }

    /**
     * Update post status
     */
    public function updateStatus(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'status' => 'required|in:active,resolved'
        ]);

        $post->update($validated);

        return back()->with('success', 'Status updated successfully!');
    }

    /**
     * Display user's posts for management
     */
    public function myPosts(Request $request)
    {
        $query = auth()->user()->posts()->with(['claims', 'foundNotifications']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%');
            });
        }

        // Sort
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'oldest':
                $query->oldest();
                break;
            case 'title':
                $query->orderBy('title');
                break;
            case 'status':
                $query->orderBy('status');
                break;
            default:
                $query->latest();
                break;
        }

        $posts = $query->paginate(15);
        $categories = ['pet', 'person', 'vehicle', 'electronics', 'documents', 'jewelry', 'clothing', 'keys', 'other'];

        return view('posts.my-posts', compact('posts', 'categories'));
    }

    /**
     * Bulk delete posts
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'post_ids' => 'required|array',
            'post_ids.*' => 'exists:posts,id'
        ]);

        $posts = Post::whereIn('id', $validated['post_ids'])
                    ->where('user_id', auth()->id())
                    ->get();

        $deletedCount = 0;
        $totalNotifications = 0;

        foreach ($posts as $post) {
            // Count notifications before deletion
            $affectedNotifications = NotificationCleanupService::getAffectedNotifications($post);
            $totalNotifications += $affectedNotifications->count();

            // Delete associated images
            if ($post->images) {
                foreach ($post->images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
            $post->delete();
            $deletedCount++;
        }

        $message = "Successfully deleted {$deletedCount} post(s).";
        if ($totalNotifications > 0) {
            $message .= " Also cleaned up {$totalNotifications} related notification(s).";
        }

        return redirect()->route('posts.my-posts')->with('success', $message);
    }
}