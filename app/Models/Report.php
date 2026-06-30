<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = [
        'user_id', 'product_id', 'reason', 'details', 'status', 'admin_note',
    ];

    /**
     * @return BelongsTo<User, Report>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Product, Report>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
