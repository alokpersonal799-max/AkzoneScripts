<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class AdminNotification extends Model
{
    protected $fillable = ['type', 'title', 'body', 'url', 'read_at'];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    /**
     * Create an admin notification (no-op if the table doesn't exist yet).
     *
     * Note: named notifyAdmins() rather than push() because Eloquent's
     * base Model already defines a non-static push() method.
     */
    public static function notifyAdmins(string $type, string $title, ?string $body = null, ?string $url = null): void
    {
        if (Schema::hasTable('admin_notifications')) {
            static::create(compact('type', 'title', 'body', 'url'));
        }

        // Mirror actionable alerts to Telegram (bots subscribed to "admin_alert").
        try {
            app(\App\Services\TelegramService::class)->notify(
                'admin_alert',
                \App\Support\TelegramMessages::adminAlert($type, $title, $body, $url)
            );
        } catch (\Throwable $e) {
            // Telegram must never block the core notification.
        }
    }

    public function isRead(): bool
    {
        return ! is_null($this->read_at);
    }
}
