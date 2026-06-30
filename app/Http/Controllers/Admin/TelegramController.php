<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\TelegramBot;
use App\Models\User;
use App\Services\TelegramService;
use App\Support\TelegramMessages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TelegramController extends Controller
{
    /**
     * Show the Telegram connection manager (bots + previews).
     */
    public function index(): View
    {
        return view('admin.telegram.index', [
            'bots' => TelegramBot::latest()->get(),
            'events' => TelegramBot::EVENTS,
            'previews' => $this->buildPreviews(),
            'autoEnabled' => setting('autotgpromo_enabled', '0') === '1',
            'autoInterval' => (int) setting('autotgpromo_interval', 6),
            'autoUnit' => setting('autotgpromo_unit', 'hours'),
        ]);
    }

    /**
     * Connect a new bot.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateBot($request);

        TelegramBot::create($this->normalize($data, $request));

        return back()->with('success', 'Telegram bot connected.');
    }

    /**
     * Update an existing bot (token optional on edit).
     */
    public function update(Request $request, TelegramBot $bot): RedirectResponse
    {
        $data = $this->validateBot($request, $bot);
        $payload = $this->normalize($data, $request);

        // Keep the existing token if the field was left blank on edit.
        if (empty($data['token'])) {
            unset($payload['token']);
        }

        $bot->update($payload);

        return back()->with('success', 'Bot updated.');
    }

    /**
     * Remove a bot.
     */
    public function destroy(TelegramBot $bot): RedirectResponse
    {
        $bot->delete();

        return back()->with('success', 'Bot removed.');
    }

    /**
     * Send a test message to a single bot.
     */
    public function test(TelegramBot $bot, TelegramService $telegram): RedirectResponse
    {
        return $telegram->sendTest($bot)
            ? back()->with('success', 'Test message sent to '.$bot->name.'. Check your Telegram channel.')
            : back()->with('error', 'Could not reach Telegram. Check the token and chat ID (and that the bot is an admin of the channel).');
    }

    /**
     * Broadcast a custom message to all bots subscribed to "custom".
     */
    public function broadcast(Request $request, TelegramService $telegram): RedirectResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $telegram->notify('custom', TelegramMessages::custom($data['message']));

        return back()->with('success', 'Custom message broadcast to your connected bots.');
    }

    /**
     * Save the time-based auto product promotion settings.
     */
    public function autoPromo(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'autotgpromo_interval' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'autotgpromo_unit' => ['nullable', 'in:minutes,hours,days'],
        ]);

        \App\Models\Setting::put('autotgpromo_enabled', $request->boolean('autotgpromo_enabled') ? '1' : '0', 'telegram');
        \App\Models\Setting::put('autotgpromo_interval', (string) ($data['autotgpromo_interval'] ?? 6), 'telegram');
        \App\Models\Setting::put('autotgpromo_unit', $data['autotgpromo_unit'] ?? 'hours', 'telegram');

        return back()->with('success', 'Auto promotion settings saved.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateBot(Request $request, ?TelegramBot $bot = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'token' => [$bot ? 'nullable' : 'required', 'string', 'max:255'],
            'chat_id' => ['required', 'string', 'max:120'],
            'events' => ['nullable', 'array'],
            'events.*' => ['string'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function normalize(array $data, Request $request): array
    {
        $allowed = array_merge(['all'], array_keys(TelegramBot::EVENTS));
        $events = array_values(array_intersect($data['events'] ?? [], $allowed));

        return [
            'name' => $data['name'],
            'token' => $data['token'] ?? null,
            'chat_id' => $data['chat_id'],
            'events' => $events ?: ['all'],
            'is_active' => $request->boolean('is_active'),
        ];
    }

    /**
     * Build sample message payloads for the on-page preview.
     *
     * @return array<string, array{text: string, photo: ?string, buttons: array}>
     */
    protected function buildPreviews(): array
    {
        $user = new User(['name' => 'Jane Cooper', 'email' => 'jane@example.com']);

        $product = Product::with('category')->first() ?: new Product([
            'title' => 'Nebula — SaaS Landing Template',
            'slug' => 'nebula-saas-landing-template',
            'tagline' => 'Modern, animated SaaS landing page in Tailwind CSS.',
            'price' => 24,
            'demo_url' => 'https://example.com/demo',
        ]);

        $category = Category::first() ?: new Category([
            'name' => 'UI Kits & Templates',
            'slug' => 'ui-kits-templates',
            'icon' => '🎨',
            'description' => 'Beautifully crafted UI kits, themes and HTML templates.',
        ]);

        return [
            'registration' => TelegramMessages::registration($user),
            'product_added' => TelegramMessages::productAdded($product),
            'category_added' => TelegramMessages::categoryAdded($category),
            'promotion' => TelegramMessages::promotionFromSettings(),
            'purchase' => TelegramMessages::purchase($user, $product),
            'review' => TelegramMessages::review($user, $product, 5),
            'free_download' => TelegramMessages::freeDownload($user, $product),
            'auto_promo' => TelegramMessages::autoPromo($product),
            'custom' => TelegramMessages::custom('✨ <b>Special weekend sale!</b> Use code <b>SAVE20</b> for 20% off everything this weekend only.'),
        ];
    }
}
