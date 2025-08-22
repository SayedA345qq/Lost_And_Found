<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;

class Post extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'location',
        'date_lost_found',
        'images',
        'type',
        'status',
        'is_flagged',
        'flag_count'
    ];

    protected $casts = [
        'images' => 'array',
        'date_lost_found' => 'date',
        'is_flagged' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        // When a post is being deleted, clean up related data
        static::deleting(function ($post) {
            // Delete related notifications from the notifications table
            // This handles Laravel's built-in notification system
            DB::table('notifications')
                ->where('data->post_id', $post->id)
                ->delete();

            // Also check for notifications that might reference the post in different ways
            DB::table('notifications')
                ->whereJsonContains('data', ['post_id' => $post->id])
                ->delete();

            // Delete related found notifications
            $post->foundNotifications()->delete();

            // Delete related claims
            $post->claims()->delete();

            // Delete related comments
            $post->comments()->delete();

            // Delete related messages
            $post->messages()->delete();

            // Delete related reports
            $post->reports()->delete();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    public function foundNotifications(): HasMany
    {
        return $this->hasMany(FoundNotification::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function scopeActive($query)
    {
        return $query->where('is_flagged', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('location', 'like', '%' . $location . '%');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%');
        });
    }
}