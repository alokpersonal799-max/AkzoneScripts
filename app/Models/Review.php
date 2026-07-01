<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'comment',
        'admin_reply',
        'replied_at',
        'is_approved',
        'is_testimonial',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_approved' => 'boolean',
            'is_testimonial' => 'boolean',
            'replied_at' => 'datetime',
        ];
    }

    /**
     * Recalculate the parent product's cached rating and review count.
     */
    protected static function booted(): void
    {
        $recalculate = function (Review $review): void {
            $product = $review->product;

            if (! $product) {
                return;
            }

            $approved = $product->reviews()->where('is_approved', true);

            $product->update([
                'rating' => round((float) $approved->avg('rating'), 2),
                'reviews_count' => $approved->count(),
            ]);
        };

        static::saved($recalculate);
        static::deleted($recalculate);
    }

    /**
     * Approved reviews the admin has marked as testimonials.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<Review>  $query
     */
    public function scopeTestimonials($query): void
    {
        $query->where('is_approved', true)->where('is_testimonial', true);
    }

    /**
     * @return BelongsTo<Product, Review>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo<User, Review>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
