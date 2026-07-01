<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductChangelog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'version',
        'notes',
        'released_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'released_at' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Product, ProductChangelog>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
