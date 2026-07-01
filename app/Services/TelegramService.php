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
     * elapsed, recommend a random published product to the subscribed bots.
     * Safe to call on every request / every scheduler tick (cheap when not due).
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

        $this->dispatchRandomPromo();
    }

    /**
     * Immediately recommend a random published product (manual "Send now").
     */
    public function sendAutoPromoNow(): bool
    {
        return $this->dispatchRandomPromo();
    }

    /**
     * Pick a random published product and post the recommendation, stamping
     * the last-sent time so the interval restarts.
     */
    protected function dispatchRandomPromo(): bool
    {
        $product = \App\Models\Product::published()->with('category')->inRandomOrder()->first();
        if (! $product) {
            return false;
        }

        // Stamp immediately to avoid duplicate sends from concurrent triggers.
        \App\Models\Setting::put('autotgpromo_last_sent', now()->toDateTimeString(), 'telegram');

        $this->notify('auto_promo', \App\Support\TelegramMessages::autoPromo($product));

        return true;
    }

    /**
     * Send the daily summary report once per day at (or after) the configured
     * time. Safe to call every scheduler tick.
     */
    public function runDailyReport(): void
    {
        try {
            if (! Schema::hasTable('settings') || setting('tg_daily_report_enabled', '0') !== '1') {
                return;
            }
        } catch (\Throwable $e) {
            return;
        }

        $today = now()->toDateString();
        if (setting('tg_daily_report_last') === $today) {
            return; // already sent today
        }

        [$h, $m] = array_pad(explode(':', (string) setting('tg_daily_report_time', '20:00')), 2, '00');
        $scheduled = now()->copy()->setTime((int) $h, (int) $m, 0);
        if (now()->lt($scheduled)) {
            return; // not time yet
        }

        \App\Models\Setting::put('tg_daily_report_last', $today, 'telegram');
        $this->notify('daily_report', \App\Support\TelegramMessages::dailyReport($this->dailyReportData()));
    }

    /**
     * Immediately send the daily report (manual "Send now").
     */
    public function sendDailyReportNow(): bool
    {
        \App\Models\Setting::put('tg_daily_report_last', now()->toDateString(), 'telegram');
        $this->notify('daily_report', \App\Support\TelegramMessages::dailyReport($this->dailyReportData()));

        return true;
    }

    /**
     * Gather today's metrics for the daily report.
     *
     * @return array<string, mixed>
     */
    public function dailyReportData(): array
    {
        $today = \Illuminate\Support\Carbon::today();
        $stat = \App\Models\DailyStat::todayRow();

        $top = \App\Models\OrderItem::query()
            ->whereHas('order', fn ($q) => $q->where('status', 'completed')->whereDate('paid_at', $today))
            ->select('product_title', \Illuminate\Support\Facades\DB::raw('COUNT(*) as c'))
            ->groupBy('product_title')->orderByDesc('c')->first();

        return [
            'views' => (int) $stat->views,
            'registrations' => \App\Models\User::where('role', 'user')->whereDate('created_at', $today)->count(),
            'logins' => (int) $stat->logins,
            'password_resets' => (int) $stat->password_resets,
            'sales' => (float) \App\Models\Order::where('status', 'completed')->whereDate('paid_at', $today)->sum('total'),
            'top_product' => $top?->product_title,
            'orders_total' => \App\Models\Order::whereDate('created_at', $today)->count(),
            'orders_completed' => \App\Models\Order::where('status', 'completed')->whereDate('paid_at', $today)->count(),
            'orders_pending' => \App\Models\Order::where('status', 'pending')->count(),
        ];
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
