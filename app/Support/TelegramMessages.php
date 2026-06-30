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
 */
class TelegramMessages
{
    protected static function site(): string
    {
        return e(setting('site_name', config('app.name')));
    }

    protected static function storeUrl(): string
    {
        return route('products.index');
    }

    /**
     * @return array{text: string, photo: ?string, buttons: array<int, array{0: string, 1: string}>}
     */
    public static function registration(User $user): array
    {
        $text = "🎉 <b>New member joined ".self::site()."!</b>\n\n"
            ."👤 <b>".e($user->name)."</b> just created an account.\n\n"
            ."A warm welcome aboard — happy building! 🚀";

        return [
            'text' => $text,
            'photo' => null,
            'buttons' => [['🛍 Visit store', self::storeUrl()]],
        ];
    }

    public static function productAdded(Product $product): array
    {
        $price = $product->price > 0 ? money($product->current_price) : 'Free';

        $text = "🆕 <b>New product just dropped!</b>\n\n"
            ."<b>".e($product->title)."</b>\n"
            .($product->tagline ? '<i>'.e($product->tagline)."</i>\n" : '')
            ."\n💰 <b>".$price."</b>"
            .($product->category ? "\n📂 ".e($product->category->name) : '');

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
        $text = "📂 <b>New category added!</b>\n\n"
            .($category->icon ? $category->icon.' ' : '')."<b>".e($category->name)."</b>\n"
            .($category->description ? '<i>'.e(Str::limit($category->description, 160))."</i>" : '');

        return [
            'text' => $text,
            'photo' => null,
            'buttons' => [['🗂 Browse category', route('categories.show', $category)], ['🛍 Visit store', self::storeUrl()]],
        ];
    }

    public static function purchase(User $user, Product $product): array
    {
        $text = "🛒 <b>New purchase!</b>\n\n"
            ."🙏 Thank you <b>".e($user->name)."</b> for purchasing\n"
            ."<b>".e($product->title)."</b>!\n\n"
            ."Enjoy your download and lifetime updates. 💜";

        return [
            'text' => $text,
            'photo' => null,
            'buttons' => [['🔎 View product', route('products.show', $product)]],
        ];
    }

    public static function review(User $user, Product $product, int $rating): array
    {
        $stars = str_repeat('⭐', max(1, min(5, $rating)));

        $text = "📝 <b>New review!</b>\n\n"
            ."<b>".e($user->name)."</b> rated <b>".e($product->title)."</b>\n"
            .$stars." (".$rating."/5)\n\n"
            ."Check it out and share your thoughts too!";

        return [
            'text' => $text,
            'photo' => null,
            'buttons' => [['🔎 Visit product', route('products.show', $product)]],
        ];
    }

    public static function freeDownload(User $user, Product $product): array
    {
        $text = "⬇️ <b>Free download!</b>\n\n"
            ."<b>".e($user->name)."</b> just grabbed <b>".e($product->title)."</b> for free. 🎁\n\n"
            ."Loved it? Don't forget to leave a review! ✍️";

        return [
            'text' => $text,
            'photo' => null,
            'buttons' => [['⭐ Review & visit', route('products.show', $product)]],
        ];
    }

    /**
     * Builds the promotion update message from the current promotion settings.
     */
    public static function promotionFromSettings(): array
    {
        $mode = setting('promo_mode', 'off');

        if ($mode === 'message') {
            $text = "📣 <b>Announcement from ".self::site()."</b>\n\n".e(setting('promo_message', ''));
        } elseif ($mode === 'countdown') {
            $text = "⏰ <b>Limited-time offer is live!</b>\n\n"
                .e(setting('promo_countdown_label', 'Limited time offer'))." — hurry before it ends! 🔥";
        } elseif ($mode === 'products') {
            $text = "✨ <b>".e(setting('promo_heading', 'Featured picks'))."</b>\n\n"
                ."Freshly featured products are now live on ".self::site().". Take a look! 👀";
        } else {
            $text = "✨ <b>Something new on ".self::site()."</b>\n\nVisit the store for the latest updates!";
        }

        return [
            'text' => $text,
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
}
