<?php

use App\Services\TelegramService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Telegram auto product promotion — checks each minute and posts when the
// admin-configured interval has elapsed. Requires the system cron entry:
//   * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
Schedule::call(function () {
    app(TelegramService::class)->runAutoPromo();
})->everyMinute()->name('telegram-auto-promo')->withoutOverlapping();

// Telegram daily report — sends once per day at the admin-configured time.
Schedule::call(function () {
    app(TelegramService::class)->runDailyReport();
})->everyMinute()->name('telegram-daily-report')->withoutOverlapping();
