<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\View\View;

class CronController extends Controller
{
    /**
     * Show the cron job setup page + the list of scheduled features.
     */
    public function index(): View
    {
        $base = base_path();
        $php = PHP_BINARY && ! str_contains(strtolower(PHP_BINARY), 'fpm') ? PHP_BINARY : 'php';

        return view('admin.cron.index', [
            // Standard "poor-man's cron" entry that drives the Laravel scheduler.
            'command' => '* * * * * cd '.$base.' && '.$php.' artisan schedule:run >> /dev/null 2>&1',
            'simpleCommand' => '* * * * * cd '.$base.' && php artisan schedule:run >> /dev/null 2>&1',
            'basePath' => $base,
            'phpBinary' => $php,
            'tasks' => [
                [
                    'name' => 'Telegram auto product promotion',
                    'schedule' => 'Checked every minute · posts on your chosen interval',
                    'purpose' => 'Automatically recommends a random published product to your Telegram channel to drive sales.',
                    'where' => 'Admin → TG Connection → Auto product promotion',
                    'enabled' => Setting::get('autotgpromo_enabled', '0') === '1',
                    'last' => Setting::get('autotgpromo_last_sent'),
                ],
                [
                    'name' => 'Telegram daily report',
                    'schedule' => 'Checked every minute · sends once per day at your set time',
                    'purpose' => 'Sends a daily summary (views, new users, logins, password resets, sales, top seller, orders) to Telegram.',
                    'where' => 'Admin → TG Connection → Daily report',
                    'enabled' => Setting::get('tg_daily_report_enabled', '0') === '1',
                    'last' => Setting::get('tg_daily_report_last'),
                ],
            ],
            // Features that work without cron (triggered by site traffic / admin visits).
            'noCronFeatures' => [
                ['name' => 'Auto promotion (fallback)', 'note' => 'Also fires on storefront visits if cron is not set.'],
                ['name' => 'Scheduled themes', 'note' => 'Festival/sale themes auto-activate between dates on each page load.'],
                ['name' => 'Contact message auto-delete', 'note' => 'Old messages are pruned when you open Admin → Messages.'],
                ['name' => 'Announcement popup frequency', 'note' => 'Handled per-visitor in the browser.'],
            ],
        ]);
    }
}
