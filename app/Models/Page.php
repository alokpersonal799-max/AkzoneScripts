<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    protected $fillable = [
        'title', 'slug', 'content', 'content_type', 'layout', 'is_published', 'show_in_footer',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'show_in_footer' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Auto-generate a unique slug from the title when none is provided.
     */
    protected static function booted(): void
    {
        static::saving(function (Page $page): void {
            if (empty($page->slug)) {
                $base = Str::slug($page->title) ?: 'page';
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->when($page->exists, fn ($q) => $q->whereKeyNot($page->getKey()))->exists()) {
                    $slug = $base.'-'.$i++;
                }
                $page->slug = $slug;
            }
        });
    }

    /**
     * Published pages flagged to appear in the footer.
     *
     * @return \Illuminate\Support\Collection<int, Page>
     */
    public static function footerLinks()
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('pages')) {
            return collect();
        }

        return static::where('is_published', true)->where('show_in_footer', true)->orderBy('title')->get();
    }
}
