<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display all notifications for the authenticated user
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all'); // all, unread, read
        $sort = $request->get('sort', 'latest'); // latest, oldest
        $type = $request->get('type', 'all'); // all, found, claim, missing_person

        $query = auth()->user()->notifications();

        // Apply read/unread filter
        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }

        // Apply type filter
        if ($type !== 'all') {
            $typeMap = [
                'found' => 'App\\Notifications\\NewFoundNotification',
                'claim' => 'App\\Notifications\\NewClaimNotification',
                'missing_person' => 'App\\Notifications\\MissingPersonNotification'
            ];
            
            if (isset($typeMap[$type])) {
                $query->where('type', $typeMap[$type]);
            }
        }

        // Apply sorting
        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $notifications = $query->paginate(20);

        return view('notifications.index', compact('notifications', 'filter', 'sort', 'type'));
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($id)
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            
            // Redirect to the action URL if it exists
            if (isset($notification->data['action_url'])) {
                return redirect($notification->data['action_url']);
            }
        }

        return back();
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Clear all notifications for the current user
     */
    public function clearAll()
    {
        $deletedCount = auth()->user()->notifications()->delete();

        $message = $deletedCount > 0 
            ? "Cleared {$deletedCount} notification(s)."
            : "No notifications to clear.";

        return back()->with('success', $message);
    }

    /**
     * Get unread notifications count (for AJAX)
     */
    public function getUnreadCount()
    {
        $count = auth()->user()->unreadNotifications->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Delete a specific notification
     */
    public function destroy($id)
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->delete();
            return back()->with('success', 'Notification deleted.');
        }

        return back()->with('error', 'Notification not found.');
    }
}