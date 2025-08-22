<?php

namespace App\Console\Commands;

use App\Services\NotificationCleanupService;
use Illuminate\Console\Command;

class CleanupOrphanedNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:cleanup-orphaned 
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up orphaned notifications that reference deleted posts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting orphaned notifications cleanup...');

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No notifications will be deleted');
            
            // Get notifications that would be deleted
            $notifications = \Illuminate\Support\Facades\DB::table('notifications')
                ->whereNotNull('data')
                ->get();

            $orphanedCount = 0;
            $orphanedNotifications = [];

            foreach ($notifications as $notification) {
                $data = json_decode($notification->data, true);
                
                if (isset($data['post_id'])) {
                    $postExists = \App\Models\Post::where('id', $data['post_id'])->exists();
                    
                    if (!$postExists) {
                        $orphanedCount++;
                        $orphanedNotifications[] = [
                            'id' => $notification->id,
                            'type' => $data['type'] ?? 'unknown',
                            'title' => $data['title'] ?? 'No title',
                            'post_id' => $data['post_id'],
                            'created_at' => $notification->created_at
                        ];
                    }
                }
            }

            if ($orphanedCount > 0) {
                $this->warn("Found {$orphanedCount} orphaned notifications:");
                
                $headers = ['ID', 'Type', 'Title', 'Post ID', 'Created At'];
                $this->table($headers, array_slice($orphanedNotifications, 0, 10)); // Show first 10
                
                if ($orphanedCount > 10) {
                    $this->info("... and " . ($orphanedCount - 10) . " more");
                }
                
                $this->info("Run without --dry-run to delete these notifications");
            } else {
                $this->info("No orphaned notifications found!");
            }

        } else {
            // Actually clean up orphaned notifications
            $deletedCount = NotificationCleanupService::cleanupOrphanedNotifications();
            
            if ($deletedCount > 0) {
                $this->info("Successfully cleaned up {$deletedCount} orphaned notifications!");
            } else {
                $this->info("No orphaned notifications found to clean up.");
            }
        }

        return 0;
    }
}