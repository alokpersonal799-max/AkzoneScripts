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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Throwable;

class DemoDataController extends Controller
{
    /** Maximum number of times demo data may be imported. */
    public const MAX_IMPORTS = 5;

    private const IMPORTS_KEY = 'demo_import_uses';

    private const HIDDEN_KEY = 'demo_tool_hidden';

    /** Emails created by the DemoSeeder (safe to remove when clearing demo data). */
    private const DEMO_EMAILS = [
        'admin@akzone.com', 'user@akzone.com',
        'rahul.verma@example.com', 'sophie.t@example.com', 'daniel.osei@example.com',
        'mei.lin@example.com', 'carlos.m@example.com', 'aisha.khan@example.com',
        'tom.becker@example.com', 'priya.nair@example.com',
    ];

    /**
     * Import the demonstration dataset (sample products, reviews, etc.).
     * Limited to MAX_IMPORTS uses.
     */
    public function import(): RedirectResponse
    {
        if ($this->isHidden()) {
            return back()->with('error', 'The demo data tool has been permanently hidden.');
        }

        if ($this->importsUsed() >= self::MAX_IMPORTS) {
            return back()->with('error', 'Demo data can only be imported '.self::MAX_IMPORTS.' times, and that limit has been reached.');
        }

        try {
            Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);
        } catch (Throwable $e) {
            return back()->with('error', 'Could not import demo data: '.$e->getMessage());
        }

        Setting::put(self::IMPORTS_KEY, (string) ($this->importsUsed() + 1), 'general');
        Setting::put('demo_mode', '1', 'general');

        return back()->with('success', 'Demo data imported successfully. '.$this->importsLeft().' import(s) remaining.');
    }

    /**
     * Remove the demonstration content from the store. May be used any number of times.
     * Because demo logins are public, the admin must set their own real email &
     * password here so the store is secure for business use.
     */
    public function clear(Request $request): RedirectResponse
    {
        if ($this->isHidden()) {
            return back()->with('error', 'The demo data tool has been permanently hidden.');
        }

        $data = $request->validate([
            'admin_email' => ['required', 'email', 'max:255'],
            'admin_password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [], [
            'admin_email' => 'admin email',
            'admin_password' => 'admin password',
        ]);

        $current = Auth::user();

        try {
            // 1) Convert the current account into the real admin (own email + password).
            //    This happens first so it is excluded from the demo-account cleanup below.
            if ($current) {
                $current->forceFill([
                    'email' => $data['admin_email'],
                    'password' => Hash::make($data['admin_password']),
                    'role' => 'admin',
                ])->save();
            }

            // 2) Delete demo content.
            $this->safeDelete(fn () => ProductChangelog::query()->delete(), 'product_changelogs');
            $this->safeDelete(fn () => Review::query()->delete(), 'reviews');
            $this->safeDelete(fn () => Product::query()->delete(), 'products');
            $this->safeDelete(fn () => Category::query()->delete(), 'categories');
            $this->safeDelete(fn () => Service::query()->delete(), 'services');
            $this->safeDelete(fn () => Advertisement::query()->delete(), 'advertisements');
            $this->safeDelete(fn () => ContactMessage::query()->delete(), 'contact_messages');
            $this->safeDelete(fn () => Page::query()->delete(), 'pages');
            $this->safeDelete(fn () => TelegramBot::query()->delete(), 'telegram_bots');

            // 3) Remove seeded demo accounts, but never the admin performing this action.
            $this->safeDelete(function () {
                User::whereIn('email', self::DEMO_EMAILS)
                    ->where('id', '!=', Auth::id())
                    ->delete();
            }, 'users');

            // 4) Turn off promo/popup that referenced now-deleted demo products, and
            //    leave demonstration mode so the credit/demo badges disappear.
            foreach (['hero_promo_enabled', 'popup_enabled', 'announcement_enabled'] as $flag) {
                Setting::put($flag, '0', 'promotion');
            }
            Setting::put('demo_mode', '0', 'general');

            // 5) Keep the current session authenticated after the password change.
            if ($current) {
                Auth::login($current->fresh());
                $request->session()->regenerate();
            }
        } catch (Throwable $e) {
            return back()->with('error', 'Could not clear demo data: '.$e->getMessage());
        }

        return back()->with('success', 'Demo data cleared and your admin login updated. Your store is now clean and ready for business. Remember your new email & password — keep them safe!');
    }

    /**
     * Permanently hide the demo data tool. Cannot be undone without reinstalling.
     */
    public function hide(): RedirectResponse
    {
        Setting::put(self::HIDDEN_KEY, '1', 'general');

        return back()->with('success', 'The demo data tool has been permanently hidden.');
    }

    private function safeDelete(callable $fn, string $table): void
    {
        if (Schema::hasTable($table)) {
            $fn();
        }
    }

    private function importsUsed(): int
    {
        return (int) setting(self::IMPORTS_KEY, 0);
    }

    private function importsLeft(): int
    {
        return max(0, self::MAX_IMPORTS - $this->importsUsed());
    }

    private function isHidden(): bool
    {
        return setting(self::HIDDEN_KEY, '0') === '1';
    }
}
