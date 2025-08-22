<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\Comment;
use App\Models\Message;
use App\Models\Report;
use Illuminate\Console\Command;

class ProcessFlaggedContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content:process-flags {--threshold=20 : Number of reports needed to flag content}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process reported content and automatically flag items with enough reports';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $threshold = $this->option('threshold');
        
        $this->info("Processing flagged content with threshold: {$threshold}");

        // Process Posts
        $flaggedPosts = $this->processPosts($threshold);
        
        // Process Comments
        $flaggedComments = $this->processComments($threshold);
        
        // Process Messages
        $flaggedMessages = $this->processMessages($threshold);

        $this->info("Processing complete!");
        $this->info("Posts flagged: {$flaggedPosts}");
        $this->info("Comments flagged: {$flaggedComments}");
        $this->info("Messages flagged: {$flaggedMessages}");
    }

    private function processPosts($threshold)
    {
        $posts = Post::where('is_flagged', false)->get();
        $flagged = 0;

        foreach ($posts as $post) {
            $reportCount = Report::where('reportable_type', 'App\Models\Post')
                ->where('reportable_id', $post->id)
                ->count();

            if ($reportCount >= $threshold) {
                $post->update([
                    'is_flagged' => true,
                    'flag_count' => $reportCount
                ]);
                $flagged++;
                $this->line("Flagged post: {$post->title} ({$reportCount} reports)");
            }
        }

        return $flagged;
    }

    private function processComments($threshold)
    {
        $comments = Comment::where('is_flagged', false)->get();
        $flagged = 0;

        foreach ($comments as $comment) {
            $reportCount = Report::where('reportable_type', 'App\Models\Comment')
                ->where('reportable_id', $comment->id)
                ->count();

            if ($reportCount >= $threshold) {
                $comment->update([
                    'is_flagged' => true,
                    'flag_count' => $reportCount
                ]);
                $flagged++;
                $this->line("Flagged comment: {$comment->id} ({$reportCount} reports)");
            }
        }

        return $flagged;
    }

    private function processMessages($threshold)
    {
        $messages = Message::where('is_flagged', false)->get();
        $flagged = 0;

        foreach ($messages as $message) {
            $reportCount = Report::where('reportable_type', 'App\Models\Message')
                ->where('reportable_id', $message->id)
                ->count();

            if ($reportCount >= $threshold) {
                $message->update([
                    'is_flagged' => true,
                    'flag_count' => $reportCount
                ]);
                $flagged++;
                $this->line("Flagged message: {$message->id} ({$reportCount} reports)");
            }
        }

        return $flagged;
    }
}
