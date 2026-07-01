<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CountryView extends Model
{
    protected $fillable = ['code', 'views'];

    /**
     * Record one page view for the given ISO country code.
     */
    public static function record(?string $code): void
    {
        $code = strtoupper((string) $code);

        if (strlen($code) !== 2 || ! ctype_alpha($code) || ! Schema::hasTable('country_views')) {
            return;
        }

        try {
            $row = static::firstOrCreate(['code' => $code], ['views' => 0]);
            $row->increment('views');
        } catch (\Throwable $e) {
            // Never let analytics break a request.
        }
    }
}
