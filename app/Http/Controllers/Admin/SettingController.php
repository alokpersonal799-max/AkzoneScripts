<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Available settings sections (key => label + icon).
     *
     * @var array<string, array{label: string, icon: string}>
     */
    public const SECTIONS = [
        'general' => ['label' => 'General', 'icon' => 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z'],
        'homepage' => ['label' => 'Homepage', 'icon' => 'm2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75'],
        'hero' => ['label' => 'Hero Section', 'icon' => 'M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z'],
        'footer' => ['label' => 'Footer', 'icon' => 'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21'],
        'pages' => ['label' => 'Custom Pages', 'icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z'],
        'seo' => ['label' => 'SEO', 'icon' => 'm21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z'],
        'payments' => ['label' => 'Payment Gateways', 'icon' => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z'],
        'manual' => ['label' => 'Manual Payment', 'icon' => 'M3.75 4.5h16.5M3.75 9h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5'],
        'mail' => ['label' => 'Email / SMTP', 'icon' => 'M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75'],
        'auth' => ['label' => 'Login &amp; Captcha', 'icon' => 'M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z'],
        'integrations' => ['label' => 'Integrations', 'icon' => 'M14.25 9.75 16.5 12l-2.25 2.25m-4.5 0L7.5 12l2.25-2.25M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z'],
        'maintenance' => ['label' => 'Maintenance', 'icon' => 'M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z'],
        'currencies' => ['label' => 'Currencies', 'icon' => 'M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
    ];

    public function index(): View
    {
        return $this->show('general');
    }

    /**
     * Display a settings section.
     */
    public function show(string $section): View
    {
        abort_unless(array_key_exists($section, self::SECTIONS), 404);

        return view('admin.settings.index', [
            'section' => $section,
            'sections' => self::SECTIONS,
            'currencies' => $section === 'currencies'
                ? Currency::orderByDesc('is_default')->orderBy('code')->get()
                : collect(),
            'pages' => $section === 'pages' ? \App\Models\Page::orderBy('title')->get() : collect(),
        ]);
    }

    public function updateHomepage(Request $request): RedirectResponse
    {
        foreach (['home_show_categories', 'home_show_latest', 'home_show_featured', 'home_show_bestselling', 'home_show_free', 'home_show_testimonials'] as $key) {
            Setting::put($key, $request->boolean($key) ? '1' : '0', 'home');
        }

        return back()->with('success', 'Homepage settings saved.');
    }

    public function updateMail(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'mail_host' => ['nullable', 'string', 'max:255'],
            'mail_port' => ['nullable', 'string', 'max:10'],
            'mail_username' => ['nullable', 'string', 'max:255'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'mail_encryption' => ['nullable', 'in:tls,ssl,none'],
            'mail_from_address' => ['nullable', 'email', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:255'],
        ]);

        Setting::put('mail_enabled', $request->boolean('mail_enabled') ? '1' : '0', 'mail');
        foreach (['mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name'] as $key) {
            Setting::put($key, $data[$key] ?? '', 'mail');
        }

        return back()->with('success', 'Email settings saved.');
    }

    public function updateAuth(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'captcha_site_key' => ['nullable', 'string', 'max:255'],
            'captcha_secret' => ['nullable', 'string', 'max:255'],
        ]);

        Setting::put('require_email_verification', $request->boolean('require_email_verification') ? '1' : '0', 'auth');
        Setting::put('captcha_enabled', $request->boolean('captcha_enabled') ? '1' : '0', 'auth');
        Setting::put('captcha_site_key', $data['captcha_site_key'] ?? '', 'auth');
        Setting::put('captcha_secret', $data['captcha_secret'] ?? '', 'auth');

        return back()->with('success', 'Login & captcha settings saved.');
    }

    public function updateIntegrations(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tawk_embed' => ['nullable', 'string', 'max:5000'],
        ]);

        Setting::put('tawk_enabled', $request->boolean('tawk_enabled') ? '1' : '0', 'integrations');
        Setting::put('tawk_embed', $data['tawk_embed'] ?? '', 'integrations');

        return back()->with('success', 'Integration settings saved.');
    }

    public function updateGeneral(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'support_email' => ['required', 'email', 'max:255'],
            'social_twitter' => ['nullable', 'string', 'max:255'],
            'social_github' => ['nullable', 'string', 'max:255'],
            'social_discord' => ['nullable', 'string', 'max:255'],
            'social_facebook' => ['nullable', 'string', 'max:255'],
            'contact_whatsapp' => ['nullable', 'string', 'max:50'],
            'contact_telegram' => ['nullable', 'string', 'max:100'],
            'timezone' => ['nullable', 'string', 'max:64', 'timezone'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $existing = Setting::get('site_logo');
            if ($existing) {
                Storage::disk('public')->delete($existing);
            }
            Setting::put('site_logo', $request->file('logo')->store('branding', 'public'), 'general');
        }

        foreach (['site_name', 'support_email', 'social_twitter', 'social_github', 'social_discord', 'social_facebook', 'contact_whatsapp', 'contact_telegram'] as $key) {
            Setting::put($key, $data[$key] ?? '', 'general');
        }
        if (! empty($data['timezone'])) {
            Setting::put('timezone', $data['timezone'], 'general');
        }

        return back()->with('success', 'General settings saved.');
    }

    public function updateHero(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'hero_badge' => ['nullable', 'string', 'max:255'],
            'hero_title' => ['required', 'string', 'max:255'],
            'hero_highlight' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($data as $key => $value) {
            Setting::put($key, $value ?? '', 'hero');
        }

        return back()->with('success', 'Hero section updated.');
    }

    public function updateFooter(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'footer_about' => ['nullable', 'string', 'max:1000'],
            'footer_copyright' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($data as $key => $value) {
            Setting::put($key, $value ?? '', 'footer');
        }

        return back()->with('success', 'Footer updated.');
    }

    public function updateSeo(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'seo_keywords' => ['nullable', 'string', 'max:500'],
            'analytics_id' => ['nullable', 'string', 'max:100'],
            'seo_og_image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('seo_og_image')) {
            $existing = Setting::get('seo_og_image');
            if ($existing) {
                Storage::disk('public')->delete($existing);
            }
            Setting::put('seo_og_image', $request->file('seo_og_image')->store('branding', 'public'), 'seo');
        }

        foreach (['seo_title', 'seo_description', 'seo_keywords', 'analytics_id'] as $key) {
            Setting::put($key, $data[$key] ?? '', 'seo');
        }

        return back()->with('success', 'SEO settings saved.');
    }

    public function updateMaintenance(Request $request): RedirectResponse
    {
        $request->validate([
            'maintenance_message' => ['nullable', 'string', 'max:500'],
        ]);

        Setting::put('maintenance_enabled', $request->boolean('maintenance_enabled') ? '1' : '0', 'maintenance');
        Setting::put('maintenance_message', $request->input('maintenance_message', ''), 'maintenance');

        return back()->with('success', 'Maintenance settings saved.');
    }

    public function updatePayments(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'stripe_key' => ['nullable', 'string', 'max:255'],
            'stripe_secret' => ['nullable', 'string', 'max:255'],
            'paypal_client_id' => ['nullable', 'string', 'max:255'],
            'paypal_secret' => ['nullable', 'string', 'max:255'],
            'paypal_mode' => ['nullable', 'in:sandbox,live'],
            'razorpay_key' => ['nullable', 'string', 'max:255'],
            'razorpay_secret' => ['nullable', 'string', 'max:255'],
        ]);

        foreach (['pay_manual_enabled', 'pay_stripe_enabled', 'pay_paypal_enabled', 'pay_razorpay_enabled'] as $flag) {
            Setting::put($flag, $request->boolean($flag) ? '1' : '0', 'payments');
        }

        foreach (['stripe_key', 'stripe_secret', 'paypal_client_id', 'paypal_secret', 'razorpay_key', 'razorpay_secret'] as $key) {
            Setting::put($key, $data[$key] ?? '', 'payments');
        }
        Setting::put('paypal_mode', $data['paypal_mode'] ?? 'sandbox', 'payments');

        return back()->with('success', 'Payment gateway settings saved.');
    }

    public function updateManual(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'manual_instructions' => ['nullable', 'string', 'max:2000'],
            'manual_upi_id' => ['nullable', 'string', 'max:255'],
            'manual_bank_details' => ['nullable', 'string', 'max:2000'],
            'manual_qr' => ['nullable', 'image', 'max:2048'],
            'manual_crypto_qr' => ['nullable', 'image', 'max:2048'],
            'crypto_label' => ['nullable', 'array'],
            'crypto_label.*' => ['nullable', 'string', 'max:100'],
            'crypto_address' => ['nullable', 'array'],
            'crypto_address.*' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('manual_qr')) {
            $existing = Setting::get('manual_qr');
            if ($existing) {
                Storage::disk('public')->delete($existing);
            }
            Setting::put('manual_qr', $request->file('manual_qr')->store('branding', 'public'), 'manual');
        }

        if ($request->hasFile('manual_crypto_qr')) {
            $existing = Setting::get('manual_crypto_qr');
            if ($existing) {
                Storage::disk('public')->delete($existing);
            }
            Setting::put('manual_crypto_qr', $request->file('manual_crypto_qr')->store('branding', 'public'), 'manual');
        }

        // Per-method visibility toggles.
        Setting::put('manual_upi_enabled', $request->boolean('manual_upi_enabled') ? '1' : '0', 'manual');
        Setting::put('manual_bank_enabled', $request->boolean('manual_bank_enabled') ? '1' : '0', 'manual');
        Setting::put('manual_crypto_enabled', $request->boolean('manual_crypto_enabled') ? '1' : '0', 'manual');

        // Build the crypto wallet list from paired label/address inputs.
        $crypto = [];
        foreach ((array) ($data['crypto_label'] ?? []) as $i => $label) {
            $address = $data['crypto_address'][$i] ?? '';
            if (trim((string) $label) !== '' && trim((string) $address) !== '') {
                $crypto[] = ['label' => $label, 'address' => $address];
            }
        }

        Setting::put('manual_instructions', $data['manual_instructions'] ?? '', 'manual');
        Setting::put('manual_upi_id', $data['manual_upi_id'] ?? '', 'manual');
        Setting::put('manual_bank_details', $data['manual_bank_details'] ?? '', 'manual');
        Setting::put('manual_crypto', json_encode($crypto), 'manual');

        return back()->with('success', 'Manual payment settings saved.');
    }
}
