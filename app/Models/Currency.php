<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Currency extends Model
{
    protected $fillable = [
        'code', 'name', 'symbol', 'exchange_rate', 'is_default', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'exchange_rate' => 'decimal:6',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        $flush = fn () => Cache::forget('active_currencies');
        static::saved($flush);
        static::deleted($flush);
    }

    /**
     * Cached collection of active currencies.
     *
     * @return \Illuminate\Support\Collection<int, Currency>
     */
    public static function active()
    {
        if (! Schema::hasTable('currencies')) {
            return collect();
        }

        return Cache::rememberForever('active_currencies', function () {
            return static::where('is_active', true)->orderByDesc('is_default')->orderBy('code')->get();
        });
    }

    /**
     * The default (base) currency.
     */
    public static function default(): ?self
    {
        return static::active()->firstWhere('is_default', true) ?? static::active()->first();
    }
}
