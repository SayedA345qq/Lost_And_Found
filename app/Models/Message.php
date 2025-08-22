<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Message extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'post_id',
        'message',
        'is_read',
        'sender_deleted',
        'receiver_deleted',
        'is_flagged',
        'flag_count'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'sender_deleted' => 'boolean',
        'receiver_deleted' => 'boolean',
        'is_flagged' => 'boolean'
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('sender_id', $userId)->where('sender_deleted', false)
              ->orWhere('receiver_id', $userId)->where('receiver_deleted', false);
        });
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeActive($query)
    {
        return $query->where('is_flagged', false);
    }
}
