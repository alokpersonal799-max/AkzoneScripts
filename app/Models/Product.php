<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'tagline',
        'description',
        'price',
        'sale_price',
        'thumbnail',
        'gallery',
        'demo_url',
        'version',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'external_url',
        'download_limit',
        'link_expiry_minutes',
        'download_message',
        'tags',
        'downloads',
        'sales',
        'views',
        'rating',
        'reviews_count',
        'status',
        'is_featured',
        'is_purchasable',
        'use_global_contact',
        'contact_whatsapp',
        'contact_telegram',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'rating' => 'decimal:2',
            'gallery' => 'array',
            'tags' => 'array',
            'is_featured' => 'boolean',
            'is_purchasable' => 'boolean',
            'use_global_contact' => 'boolean',
            'downloads' => 'integer',
            'sales' => 'integer',
            'views' => 'integer',
            'reviews_count' => 'integer',
            'file_size' => 'integer',
            'download_limit' => 'integer',
            'link_expiry_minutes' => 'integer',
        ];
    }

    /**
     * Use the slug for route model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * @return BelongsTo<Category, Product>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany<Review>
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * @return HasMany<Review>
     */
    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    /**
     * @return HasMany<OrderItem>
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @return HasMany<ProductChangelog>
     */
    public function changelogs(): HasMany
    {
        return $this->hasMany(ProductChangelog::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * @param  Builder<Product>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('status', 'published');
    }

    /**
     * @param  Builder<Product>  $query
     */
    public function scopeFeatured(Builder $query): void
    {
        $query->where('is_featured', true);
    }

    /**
     * Filter products by a free text search term.
     *
     * @param  Builder<Product>  $query
     */
    public function scopeSearch(Builder $query, ?string $term): void
    {
        if (! $term) {
            return;
        }

        $query->where(function (Builder $q) use ($term): void {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('tagline', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * The effective price taking any active sale into account.
     */
    public function getCurrentPriceAttribute(): float
    {
        return (float) ($this->sale_price ?? $this->price);
    }

    /**
     * Whether the product is currently discounted.
     */
    public function getIsOnSaleAttribute(): bool
    {
        return ! is_null($this->sale_price) && (float) $this->sale_price < (float) $this->price;
    }

    /**
     * Whether the product is free.
     */
    public function getIsFreeAttribute(): bool
    {
        return $this->current_price <= 0;
    }

    /**
     * Public URL for the thumbnail, falling back to a generated placeholder.
     */
    public function getThumbnailUrlAttribute(): string
    {
        // Allow full external URLs (e.g. seeded demo placeholders) to pass through directly.
        if ($this->thumbnail && Str::startsWith($this->thumbnail, ['http://', 'https://'])) {
            return $this->thumbnail;
        }

        if ($this->thumbnail && Storage::disk('public')->exists($this->thumbnail)) {
            return Storage::disk('public')->url($this->thumbnail);
        }

        // Deterministic gradient placeholder based on the product title.
        $seed = urlencode($this->title ?? 'Akzone');

        return "https://placehold.co/600x400/0f172a/22d3ee?text={$seed}";
    }

    /**
     * All image URLs for the product gallery (thumbnail first, then gallery).
     *
     * @return array<int, string>
     */
    public function getGalleryUrlsAttribute(): array
    {
        $urls = [$this->thumbnail_url];

        foreach ((array) $this->gallery as $path) {
            if (! $path) {
                continue;
            }

            // Full external URLs (e.g. seeded demo placeholders) pass through directly.
            if (Str::startsWith($path, ['http://', 'https://'])) {
                $urls[] = $path;
            } elseif (Storage::disk('public')->exists($path)) {
                $urls[] = Storage::disk('public')->url($path);
            }
        }

        return array_values(array_unique($urls));
    }

    /**
     * Human friendly file size, e.g. "2.4 MB".
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = (int) $this->file_size;

        if ($bytes <= 0) {
            return '—';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);

        return round($bytes / (1024 ** $power), 2).' '.$units[$power];
    }

    /**
     * A stable-but-lively "people viewing now" number (changes hourly per product).
     */
    public function getViewersNowAttribute(): int
    {
        return ((($this->id * 7) + ((int) now()->format('H') * 3)) % 25) + 5;
    }

    /**
     * Whether the product is delivered via an external hosted link.
     */
    public function getIsExternalFileAttribute(): bool
    {
        return $this->file_type === 'external' && ! empty($this->external_url);
    }

    /**
     * Whether there is something for a buyer to download (uploaded file or external link).
     */
    public function getHasDownloadableAttribute(): bool
    {
        return $this->is_external_file || ! empty($this->file_path);
    }

    /**
     * Whether buyers can download an unlimited number of times.
     */
    public function getIsUnlimitedDownloadAttribute(): bool
    {
        return empty($this->download_limit) || (int) $this->download_limit <= 0;
    }

    /**
     * The WhatsApp number to use for this product (per-product or global).
     */
    public function getEffectiveWhatsappAttribute(): ?string
    {
        $value = $this->use_global_contact ? setting('contact_whatsapp') : $this->contact_whatsapp;

        return $value ?: null;
    }

    /**
     * The Telegram username/link to use for this product (per-product or global).
     */
    public function getEffectiveTelegramAttribute(): ?string
    {
        $value = $this->use_global_contact ? setting('contact_telegram') : $this->contact_telegram;

        return $value ?: null;
    }
}
