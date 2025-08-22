<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationCleanupService
{
    /**
     * Clean up all notifications related to a post
     */
    public static function cleanupPostNotifications(Post $post)
    {
        try {
            $deletedCount = 0;

            // Method 1: Delete notifications where post_id is in the data JSON
            $count1 = DB::table('notifications')
                ->where('data->post_id', $post->id)
                ->delete();
            $deletedCount += $count1;

            // Method 2: Delete notifications using JSON contains (for different JSON structures)
            $count2 = DB::table('notifications')
                ->whereJsonContains('data', ['post_id' => $post->id])
                ->delete();
            $deletedCount += $count2;

            // Method 3: Delete notifications using LIKE for JSON data (fallback)
            $count3 = DB::table('notifications')
                ->where('data', 'LIKE', '%"post_id":' . $post->id . '%')
                ->delete();
            $deletedCount += $count3;

            // Method 4: Delete notifications by type and post reference
            $notificationTypes = [
                'missing_person_alert',
                'missing_person_found',
                'new_claim',
                'new_found_notification',
                'claim_accepted',
                'claim_rejected',
                'found_notification_accepted',
                'found_notification_rejected'
            ];

            foreach ($notificationTypes as $type) {
                $count = DB::table('notifications')
                    ->where('type', $type)
                    ->where('data', 'LIKE', '%"post_id":' . $post->id . '%')
                    ->delete();
                $deletedCount += $count;
            }

            Log::info("Cleaned up {$deletedCount} notifications for post ID: {$post->id}");

            return $deletedCount;

        } catch (\Exception $e) {
            Log::error("Error cleaning up notifications for post ID: {$post->id}. Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Clean up orphaned notifications (notifications pointing to non-existent posts)
     */
    public static function cleanupOrphanedNotifications()
    {
        try {
            $deletedCount = 0;

            // Get all notifications that have post_id in their data
            $notifications = DB::table('notifications')
                ->whereNotNull('data')
                ->get();

            foreach ($notifications as $notification) {
                $data = json_decode($notification->data, true);
                
                if (isset($data['post_id'])) {
                    $postExists = Post::where('id', $data['post_id'])->exists();
                    
                    if (!$postExists) {
                        DB::table('notifications')
                            ->where('id', $notification->id)
                            ->delete();
                        $deletedCount++;
                    }
                }
            }

            Log::info("Cleaned up {$deletedCount} orphaned notifications");

            return $deletedCount;

        } catch (\Exception $e) {
            Log::error("Error cleaning up orphaned notifications. Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get notifications that would be affected by deleting a post
     */
    public static function getAffectedNotifications(Post $post)
    {
        try {
            $notifications = collect();

            // Get notifications from database
            $dbNotifications = DB::table('notifications')
                ->where(function($query) use ($post) {
                    $query->where('data->post_id', $post->id)
                          ->orWhere('data', 'LIKE', '%"post_id":' . $post->id . '%');
                })
                ->get();

            foreach ($dbNotifications as $notification) {
                $data = json_decode($notification->data, true);
                $notifications->push([
                    'id' => $notification->id,
                    'type' => $data['type'] ?? 'unknown',
                    'title' => $data['title'] ?? 'No title',
                    'notifiable_id' => $notification->notifiable_id,
                    'created_at' => $notification->created_at
                ]);
            }

            return $notifications;

        } catch (\Exception $e) {
            Log::error("Error getting affected notifications for post ID: {$post->id}. Error: " . $e->getMessage());
            return collect();
        }
    }
}