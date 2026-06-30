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
        'is_approved',
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
