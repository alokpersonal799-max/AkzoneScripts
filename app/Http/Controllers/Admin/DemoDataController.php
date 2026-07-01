<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductChangelog;
use App\Models\Review;
use App\Models\Service;
use App\Models\Setting;
use App\Models\TelegramBot;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Throwable;

class DemoDataController extends Controller
{
    /** Maximum number of times the demo tool (import + clear combined) may be used. */
    public const MAX_USES = 2;

    private const USES_KEY = 'demo_tool_uses';

    /** Emails created by the DemoSeeder (safe to remove when clearing demo data). */
    private const DEMO_EMAILS = [
        'admin@akzone.com', 'user@akzone.com',
        'rahul.verma@example.com', 'sophie.t@example.com', 'daniel.osei@example.com',
        'mei.lin@example.com', 'carlos.m@example.com', 'aisha.khan@example.com',
        'tom.becker@example.com', 'priya.nair@example.com',
    ];

    /**
     * Import the demonstration dataset (sample products, reviews, etc.).
     */
    public function import(): RedirectResponse
    {
        if (! $this->hasUsesLeft()) {
            return back()->with('error', 'The demo data tool has already been used '.self::MAX_USES.' times and is no longer available.');
        }

        try {
            Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);
        } catch (Throwable $e) {
            return back()->with('error', 'Could not import demo data: '.$e->getMessage());
        }

        $this->recordUse();

        return back()->with('success', 'Demo data imported successfully. '.$this->remainingUses().' use(s) of the demo tool remaining.');
    }

    /**
     * Remove the demonstration content from the store.
     */
    public function clear(): RedirectResponse
    {
        if (! $this->hasUsesLeft()) {
            return back()->with('error', 'The demo data tool has already been used '.self::MAX_USES.' times and is no longer available.');
        }

        try {
            // Delete content in dependency-safe order. Each guarded so a missing
            // table never aborts the whole operation.
            $this->safeDelete(fn () => ProductChangelog::query()->delete(), 'product_changelogs');
            $this->safeDelete(fn () => Review::query()->delete(), 'reviews');
            $this->safeDelete(fn () => Product::query()->delete(), 'products');
            $this->safeDelete(fn () => Category::query()->delete(), 'categories');
            $this->safeDelete(fn () => Service::query()->delete(), 'services');
            $this->safeDelete(fn () => Advertisement::query()->delete(), 'advertisements');
            $this->safeDelete(fn () => ContactMessage::query()->delete(), 'contact_messages');
            $this->safeDelete(fn () => Page::query()->delete(), 'pages');
            $this->safeDelete(fn () => TelegramBot::query()->delete(), 'telegram_bots');

            // Remove seeded demo accounts, but never the admin performing this action.
            $this->safeDelete(function () {
                User::whereIn('email', self::DEMO_EMAILS)
                    ->where('id', '!=', Auth::id())
                    ->delete();
            }, 'users');

            // Disable promo/popup that referenced now-deleted demo products.
            foreach (['hero_promo_enabled', 'popup_enabled', 'announcement_enabled'] as $flag) {
                Setting::put($flag, '0', 'promotion');
            }
        } catch (Throwable $e) {
            return back()->with('error', 'Could not clear demo data: '.$e->getMessage());
        }

        $this->recordUse();

        return back()->with('success', 'Demo data cleared. '.$this->remainingUses().' use(s) of the demo tool remaining.');
    }

    private function safeDelete(callable $fn, string $table): void
    {
        if (Schema::hasTable($table)) {
            $fn();
        }
    }

    private function usesSoFar(): int
    {
        return (int) setting(self::USES_KEY, 0);
    }

    private function hasUsesLeft(): bool
    {
        return $this->usesSoFar() < self::MAX_USES;
    }

    private function remainingUses(): int
    {
        return max(0, self::MAX_USES - $this->usesSoFar());
    }

    private function recordUse(): void
    {
        Setting::put(self::USES_KEY, (string) ($this->usesSoFar() + 1), 'general');
    }
}
