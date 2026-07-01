<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'user_name', 'action', 'subject_type', 'subject_id', 'subject_label', 'ip',
    ];

    /**
     * A tint class for the action badge.
     */
    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'created' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'updated' => 'bg-amber-50 text-amber-700 ring-amber-200',
            'deleted' => 'bg-rose-50 text-rose-700 ring-rose-200',
            default => 'bg-slate-100 text-slate-600 ring-slate-200',
        };
    }
}
