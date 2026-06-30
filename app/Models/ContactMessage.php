<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = ['name', 'email', 'subject', 'message', 'ip', 'read_at'];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    public function isRead(): bool
    {
        return ! is_null($this->read_at);
    }

    /**
     * Delete messages older than the admin-configured retention period.
     * A retention of 0 (or blank) disables auto-deletion.
     */
    public static function pruneExpired(): void
    {
        $days = (int) setting('contact_autodelete_days', 0);

        if ($days > 0) {
            static::where('created_at', '<', now()->subDays($days))->delete();
        }
    }
}
