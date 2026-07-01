<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Advertisement extends Model
{
    protected $fillable = [
        'title', 'image_path', 'image_url', 'link_url', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Resolve the best available image for display.
     *
     * Prefers an uploaded file, falls back to an external URL, and finally
     * a generic placeholder so the banner never renders a broken image.
     */
    public function getDisplayImageAttribute(): string
    {
        if ($this->image_path) {
            return Storage::disk('public')->url($this->image_path);
        }

        if ($this->image_url) {
            return $this->image_url;
        }

        return 'https://placehold.co/600x300/eef2ff/2563eb?text=Ad';
    }
}
