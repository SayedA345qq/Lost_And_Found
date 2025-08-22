<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Message;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Store a new report
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reportable_type' => 'required|in:App\Models\Post,App\Models\Comment,App\Models\Message',
            'reportable_id' => 'required|integer',
            'reason' => 'required|string|in:spam,inappropriate,harassment,fake,other',
            'description' => 'nullable|string|max:500'
        ]);

        // Check if user already reported this item
        $existingReport = Report::where('user_id', auth()->id())
            ->where('reportable_type', $validated['reportable_type'])
            ->where('reportable_id', $validated['reportable_id'])
            ->first();

        if ($existingReport) {
            return back()->with('error', 'You have already reported this item.');
        }

        $validated['user_id'] = auth()->id();
        Report::create($validated);

        // Update flag count on the reported item
        $this->updateFlagCount($validated['reportable_type'], $validated['reportable_id']);

        return back()->with('success', 'Report submitted successfully. Thank you for helping keep our community safe.');
    }

    /**
     * Update flag count and potentially hide content
     */
    private function updateFlagCount($type, $id)
    {
        $model = $type::find($id);
        if (!$model) return;

        $reportCount = Report::where('reportable_type', $type)
            ->where('reportable_id', $id)
            ->count();

        $model->update([
            'flag_count' => $reportCount,
            'is_flagged' => $reportCount >= 10 // Hide after 10 reports
        ]);
    }

    /**
     * Show report form
     */
    public function create(Request $request)
    {
        $type = $request->get('type');
        $id = $request->get('id');

        // Validate the reportable item exists
        switch ($type) {
            case 'post':
                $item = Post::findOrFail($id);
                $reportableType = 'App\Models\Post';
                break;
            case 'comment':
                $item = Comment::findOrFail($id);
                $reportableType = 'App\Models\Comment';
                break;
            case 'message':
                $item = Message::findOrFail($id);
                $reportableType = 'App\Models\Message';
                break;
            default:
                abort(404);
        }

        $reasons = [
            'spam' => 'Spam',
            'inappropriate' => 'Inappropriate Content',
            'harassment' => 'Harassment',
            'fake' => 'Fake/Misleading',
            'other' => 'Other'
        ];

        return view('reports.create', compact('item', 'type', 'reportableType', 'reasons'));
    }
}
