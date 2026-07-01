<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Builds the HTML message payloads (text + optional photo + inline buttons)
 * for every Telegram notification type. Used both when sending live updates
 * and when rendering the admin preview, so previews always match reality.
 *
 * Telegram supports a small HTML subset: <b> <i> <u> <s> <a> <code>
 * <blockquote>. We lean on those plus tasteful dividers and emoji for a
 * polished, consistent look.
 */
class TelegramMessages
{
    /** A slim decorative divider used across messages. */
    protected const RULE = "➖➖➖➖➖➖➖➖➖➖";

    protected static function site(): string
    {
        return e(setting('site_name', config('app.name')));
    }

    protected static function storeUrl(): string
    {
        return route('products.index');
    }

    /** A consistent, understated signature line. */
    protected static function footer(): string
    {
        return "\n\n<i>✦ ".self::site()." ✦</i>";
    }

    /**
     * @return array{text: string, photo: ?string, buttons: array<int, array{0: string, 1: string}>}
     */
    public static function registration(User $user): array
    {
        $text = "🎉 <b>NEW MEMBER JOINED</b> 🎉\n"
            .self::RULE."\n\n"
            ."👤 <b>".e($user->name)."</b> just joined the community!\n\n"
            ."<blockquote>Welcome aboard — we're thrilled to have you. Explore premium scripts, templates &amp; assets, all with instant delivery. 🚀</blockquote>"
            .self::footer();

        return [
            'text' => $text,
            'photo' => null,
            'buttons' => [['🛍 Visit store', self::storeUrl()]],
        ];
    }

    public static function productAdded(Product $product): array
    {
        $price = $product->price > 0 ? money($product->current_price) : 'FREE';

        $text = "✨ <b>NEW ARRIVAL</b> ✨\n"
            .self::RULE."\n\n"
            ."🧩 <b>".e($product->title)."</b>\n"
            .($product->tagline ? '<i>'.e($product->tagline)."</i>\n" : '')
            ."\n💰 Price: <b>".$price."</b>"
            .($product->category ? "\n📂 Category: <b>".e($product->category->name)."</b>" : '')
            ."\n\n👇 <b>Grab it now</b>"
            .self::footer();

        $buttons = [['🔎 View product', route('products.show', $product)]];
        if ($product->demo_url) {
            $buttons[] = ['👀 Live preview', $product->demo_url];
        }
        $buttons[] = ['🛍 Visit store', self::storeUrl()];

        return [
            'text' => $text,
            'photo' => $product->thumbnail_url,
            'buttons' => $buttons,
        ];
    }

    public static function categoryAdded(Category $category): array
    {
        $text = "📂 <b>NEW CATEGORY</b>\n"
            .self::RULE."\n\n"
            .($category->icon ? $category->icon.' ' : '')."<b>".e($category->name)."</b>\n"
            .($category->description ? "\n<blockquote>".e(Str::limit($category->description, 180))."</blockquote>" : '')
            .self::footer();

        return [
            'text' => $text,
            'photo' => null,
            'buttons' => [['🗂 Browse category', route('categories.show', $category)], ['🛍 Visit store', self::storeUrl()]],
        ];
    }

    public static function purchase(User $user, Product $product): array
    {
        $text = "🛒 <b>NEW PURCHASE</b> 🎊\n"
            .self::RULE."\n\n"
            ."🙏 Thank you <b>".e($user->name)."</b> for purchasing\n"
            ."🧩 <b>".e($product->title)."</b>!\n\n"
            ."<blockquote>Enjoy your download, lifetime access &amp; updates. 💜</blockquote>"
            .self::footer();

        return [
            'text' => $text,
            'photo' => null,
            'buttons' => [['🔎 View product', route('products.show', $product)]],
        ];
    }

    public static function review(User $user, Product $product, int $rating): array
    {
        $rating = max(1, min(5, $rating));
        $stars = str_repeat('⭐', $rating).str_repeat('☆', 5 - $rating);

        $text = "📝 <b>NEW REVIEW</b>\n"
            .self::RULE."\n\n"
            ."<b>".e($user->name)."</b> reviewed\n"
            ."🧩 <b>".e($product->title)."</b>\n\n"
            .$stars."  <b>".$rating.".0</b> / 5"
            .self::footer();

        return [
            'text' => $text,
            'photo' => null,
            'buttons' => [['🔎 Visit product', route('products.show', $product)]],
        ];
    }

    public static function freeDownload(User $user, Product $product): array
    {
        $text = "⬇️ <b>FREE DOWNLOAD</b> 🎁\n"
            .self::RULE."\n\n"
            ."<b>".e($user->name)."</b> just grabbed\n"
            ."🧩 <b>".e($product->title)."</b> for free!\n\n"
            ."<blockquote>Loved it? Leave a quick review &amp; help others discover it. ✍️</blockquote>"
            .self::footer();

        return [
            'text' => $text,
            'photo' => null,
            'buttons' => [['⭐ Review & visit', route('products.show', $product)]],
        ];
    }

    /**
     * Time-based "recommended product" auto promotion (purchase-focused).
     */
    public static function autoPromo(Product $product): array
    {
        $price = $product->price > 0 ? money($product->current_price) : 'FREE';

        $text = "🔥 <b>TODAY'S RECOMMENDED PICK</b> 🔥\n"
            .self::RULE."\n\n"
            ."🧩 <b>".e($product->title)."</b>\n"
            .($product->tagline ? '<i>'.e($product->tagline)."</i>\n" : '')
            ."\n💰 Only <b>".$price."</b>"
            .($product->rating > 0 ? "  •  ⭐ <b>".number_format($product->rating, 1)."</b>" : '')
            .($product->sales > 0 ? "  •  🛒 <b>".number_format($product->sales)."</b> sold" : '')
            ."\n\n<blockquote>⚡ Don't miss out — grab it now with instant delivery &amp; lifetime updates!</blockquote>"
            .self::footer();

        $buttons = [['🛒 Buy / Visit now', route('products.show', $product)]];
        if ($product->demo_url) {
            $buttons[] = ['👀 Preview', $product->demo_url];
        }
        $buttons[] = ['🛍 Browse store', self::storeUrl()];

        return [
            'text' => $text,
            'photo' => $product->thumbnail_url,
            'buttons' => $buttons,
        ];
    }

    /**
     * Builds the promotion update message from the current promotion settings.
     */
    public static function promotionFromSettings(): array
    {
        $mode = setting('promo_mode', 'off');

        if ($mode === 'message') {
            $body = "📣 <b>ANNOUNCEMENT</b>\n".self::RULE."\n\n".e(setting('promo_message', ''));
        } elseif ($mode === 'countdown') {
            $body = "⏰ <b>LIMITED-TIME OFFER</b>\n".self::RULE."\n\n"
                ."<b>".e(setting('promo_countdown_label', 'Limited time offer'))."</b>\n"
                ."<blockquote>Hurry — the clock is ticking! 🔥</blockquote>";
        } elseif ($mode === 'products') {
            $body = "✨ <b>".e(setting('promo_heading', 'Featured picks'))."</b>\n".self::RULE."\n\n"
                ."Freshly featured products are now live. Take a look! 👀";
        } else {
            $body = "✨ <b>What's new</b>\n".self::RULE."\n\nVisit the store for the latest updates!";
        }

        return [
            'text' => $body.self::footer(),
            'photo' => null,
            'buttons' => [['🛍 Visit store', self::storeUrl()]],
        ];
    }

    public static function custom(string $message): array
    {
        return [
            'text' => $message,
            'photo' => null,
            'buttons' => [],
        ];
    }

    /**
     * Actionable admin alert (mirrors the admin notification bell).
     */
    public static function adminAlert(string $type, string $title, ?string $body = null, ?string $url = null): array
    {
        $icons = [
            'order' => '🛒', 'ticket' => '🎫', 'review' => '⭐', 'report' => '🚩',
            'user' => '👤', 'contact' => '✉️', 'product' => '🧩',
        ];
        $icon = $icons[$type] ?? '🔔';

        $text = "🔔 <b>ADMIN ACTION NEEDED</b>\n"
            .self::RULE."\n\n"
            .$icon.' <b>'.e($title)."</b>\n"
            .($body ? '<i>'.e($body)."</i>\n" : '')
            ."\n<blockquote>Tap below to review and take action.</blockquote>"
            .self::footer();

        return [
            'text' => $text,
            'photo' => null,
            'buttons' => $url ? [['⚡ Take action', $url]] : [],
        ];
    }

    /**
     * Daily summary report.
     *
     * @param  array<string, mixed>  $d
     */
    public static function dailyReport(array $d): array
    {
        $text = "📊 <b>DAILY REPORT</b>\n"
            ."<i>".self::site()." • ".now()->format('D, M j, Y • g:i A')."</i>\n"
            .self::RULE."\n\n"
            ."<b>Traffic &amp; users</b>\n"
            ."👁 Views: <b>".number_format((int) $d['views'])."</b>\n"
            ."🆕 New registrations: <b>".number_format((int) $d['registrations'])."</b>\n"
            ."🔐 Logins: <b>".number_format((int) $d['logins'])."</b>\n"
            ."🔑 Password resets: <b>".number_format((int) $d['password_resets'])."</b>\n\n"
            ."<b>Sales</b>\n"
            ."💰 Revenue today: <b>".money((float) $d['sales'])."</b>\n"
            ."🔥 Top seller: <b>".e($d['top_product'] ?: '—')."</b>\n\n"
            ."<b>Orders</b>\n"
            ."🧾 Total: <b>".number_format((int) $d['orders_total'])."</b>\n"
            ."✅ Completed: <b>".number_format((int) $d['orders_completed'])."</b>\n"
            ."⏳ Pending: <b>".number_format((int) $d['orders_pending'])."</b>"
            .self::footer();

        return [
            'text' => $text,
            'photo' => null,
            'buttons' => [['📈 Open dashboard', route('admin.dashboard')]],
        ];
    }
}
