<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class AnnouncementReply extends Model
{
    protected $fillable = [
        'announcement_id', 'user_id', 'is_admin', 'type', 'rating', 'emoji', 'message', 'media_path',
    ];

    protected function casts(): array
    {
        return [
            'is_admin' => 'boolean',
            'rating' => 'integer',
        ];
    }

    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getMediaUrlAttribute(): ?string
    {
        if ($this->media_path && Storage::disk('public')->exists($this->media_path)) {
            return Storage::disk('public')->url($this->media_path);
        }

        return null;
    }
}
