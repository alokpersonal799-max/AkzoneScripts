<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class DailyStat extends Model
{
    protected $fillable = ['stat_date', 'views', 'logins', 'password_resets'];

    protected function casts(): array
    {
        return ['stat_date' => 'date'];
    }

    /**
     * Increment a counter for today (safe no-op before the table exists).
     */
    public static function bump(string $field, int $amount = 1): void
    {
        if (! in_array($field, ['views', 'logins', 'password_resets'], true)) {
            return;
        }

        try {
            if (! Schema::hasTable('daily_stats')) {
                return;
            }
            static::firstOrCreate(['stat_date' => now()->toDateString()])->increment($field, $amount);
        } catch (\Throwable $e) {
            // Never let stat tracking break the request.
        }
    }

    /**
     * Today's stat row (not persisted if it doesn't exist yet).
     */
    public static function todayRow(): self
    {
        return static::firstOrNew(['stat_date' => now()->toDateString()]);
    }
}
