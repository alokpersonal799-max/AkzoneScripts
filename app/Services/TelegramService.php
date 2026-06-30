<?php

namespace App\Services;

use App\Models\TelegramBot;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class TelegramService
{
    /**
     * Send a prepared message to every active bot subscribed to the event.
     *
     * @param  array{text: string, photo?: ?string, buttons?: array<int, array{0: string, 1: string}>}  $message
     */
    public function notify(string $event, array $message): void
    {
        try {
            if (! Schema::hasTable('telegram_bots')) {
                return;
            }
        } catch (\Throwable $e) {
            return;
        }

        TelegramBot::where('is_active', true)->get()
            ->filter(fn (TelegramBot $bot) => $bot->sendsEvent($event))
            ->each(fn (TelegramBot $bot) => $this->send($bot, $message));
    }

    /**
     * Send a single message to one bot. Network failures are swallowed
     * (and logged) so they never interrupt the user-facing request.
     *
     * @param  array{text: string, photo?: ?string, buttons?: array<int, array{0: string, 1: string}>}  $message
     */
    public function send(TelegramBot $bot, array $message): bool
    {
        $text = $message['text'] ?? '';
        $photo = $message['photo'] ?? null;
        $buttons = $message['buttons'] ?? [];

        // Telegram can only fetch publicly reachable image URLs; skip local ones.
        if ($photo && $this->isLocalUrl($photo)) {
            $photo = null;
        }

        $payload = [
            'chat_id' => $bot->chat_id,
            'parse_mode' => 'HTML',
        ];

        if (! empty($buttons)) {
            // Lay buttons out two per row (1×2). Drop any with non-public URLs.
            $rows = [];
            $current = [];
            foreach ($buttons as $button) {
                [$label, $url] = $button;
                if (! $url || $this->isLocalUrl($url)) {
                    continue;
                }
                $current[] = ['text' => $label, 'url' => $url];
                if (count($current) === 2) {
                    $rows[] = $current;
                    $current = [];
                }
            }
            if (! empty($current)) {
                $rows[] = $current;
            }
            if (! empty($rows)) {
                $payload['reply_markup'] = json_encode(['inline_keyboard' => $rows]);
            }
        }

        $base = 'https://api.telegram.org/bot'.$bot->token.'/';

        try {
            if ($photo) {
                $payload['photo'] = $photo;
                $payload['caption'] = $this->captionLimit($text);
                $response = Http::timeout(10)->asForm()->post($base.'sendPhoto', $payload);
            } else {
                $payload['text'] = $text;
                $payload['disable_web_page_preview'] = false;
                $response = Http::timeout(10)->asForm()->post($base.'sendMessage', $payload);
            }

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('Telegram send failed for bot '.$bot->id.': '.$e->getMessage());

            return false;
        }
    }

    /**
     * Convenience helper for sending a test message to a single bot.
     */
    public function sendTest(TelegramBot $bot): bool
    {
        return $this->send($bot, [
            'text' => "✅ <b>Connection successful!</b>\n\nBot <b>".e($bot->name)."</b> is now linked to <b>".e(setting('site_name', config('app.name')))."</b> and ready to post updates. 🚀",
        ]);
    }

    /**
     * Time-based auto promotion: if enabled and the configured interval has
     * elapsed, pick a random published product and recommend it to the
     * subscribed bots. Designed to be called on every web request (cheap when
     * not due) so it works on hosting without a real cron job.
     */
    public function runAutoPromo(): void
    {
        try {
            if (! Schema::hasTable('settings') || setting('autotgpromo_enabled', '0') !== '1') {
                return;
            }
        } catch (\Throwable $e) {
            return;
        }

        $interval = max(1, (int) setting('autotgpromo_interval', 6));
        $unit = setting('autotgpromo_unit', 'hours');
        $minutes = $interval * ($unit === 'days' ? 1440 : ($unit === 'minutes' ? 1 : 60));

        $last = setting('autotgpromo_last_sent');
        if ($last && \Illuminate\Support\Carbon::parse($last)->gt(now()->subMinutes($minutes))) {
            return; // not due yet
        }

        $product = \App\Models\Product::published()->with('category')->inRandomOrder()->first();
        if (! $product) {
            return;
        }

        // Stamp immediately to avoid duplicate sends from concurrent requests.
        \App\Models\Setting::put('autotgpromo_last_sent', now()->toDateTimeString(), 'telegram');

        $this->notify('auto_promo', \App\Support\TelegramMessages::autoPromo($product));
    }

    protected function isLocalUrl(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST) ?: '';

        return $host === '' || in_array($host, ['localhost', '127.0.0.1', '::1'], true) || str_ends_with($host, '.local') || str_ends_with($host, '.test');
    }

    /**
     * Telegram photo captions are limited to 1024 characters.
     */
    protected function captionLimit(string $text): string
    {
        return mb_strlen($text) > 1024 ? mb_substr($text, 0, 1021).'...' : $text;
    }
}
