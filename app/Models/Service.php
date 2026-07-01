<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Service extends Model
{
    protected $fillable = [
        'name', 'subtitle', 'description', 'provider_type', 'provider_name', 'provider_avatar',
        'use_global_contact', 'allow_inquiry',
        'whatsapp', 'telegram', 'instagram', 'twitter', 'github', 'discord', 'facebook',
        'custom_label', 'custom_url', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'use_global_contact' => 'boolean',
            'allow_inquiry' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Display name of the provider (custom name, or the site name for admin).
     */
    public function getProviderLabelAttribute(): string
    {
        if ($this->provider_type === 'custom' && $this->provider_name) {
            return $this->provider_name;
        }

        return setting('site_name', config('app.name'));
    }

    /**
     * Provider avatar URL — uploaded image, or an auto-generated initials SVG.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->provider_type === 'custom' && $this->provider_avatar) {
            return Storage::disk('public')->url($this->provider_avatar);
        }

        if ($this->provider_type !== 'custom' && setting('site_logo')) {
            return Storage::disk('public')->url(setting('site_logo'));
        }

        $initial = strtoupper(mb_substr($this->provider_label ?: 'S', 0, 1));
        $palette = ['#2563eb', '#7c3aed', '#db2777', '#059669', '#ea580c', '#0891b2'];
        $bg = $palette[abs(crc32($this->provider_label.$this->id)) % count($palette)];
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" viewBox="0 0 128 128">'
            .'<rect width="128" height="128" rx="28" fill="'.$bg.'"/>'
            .'<text x="64" y="64" dy=".12em" font-family="Arial,sans-serif" font-size="58" font-weight="700" fill="#fff" text-anchor="middle" dominant-baseline="middle">'.htmlspecialchars($initial, ENT_QUOTES).'</text></svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    /**
     * Resolved contact links (custom links, or global site settings for admin).
     *
     * @return array<int, array{type: string, label: string, url: string, color: string}>
     */
    public function getContactLinksAttribute(): array
    {
        $useGlobal = $this->provider_type !== 'custom' && $this->use_global_contact;

        $wa = $useGlobal ? setting('contact_whatsapp') : $this->whatsapp;
        $tg = $useGlobal ? setting('contact_telegram') : $this->telegram;
        $insta = $useGlobal ? null : $this->instagram;
        $tw = $useGlobal ? setting('social_twitter') : $this->twitter;
        $gh = $useGlobal ? setting('social_github') : $this->github;
        $dc = $useGlobal ? setting('social_discord') : $this->discord;
        $fb = $useGlobal ? setting('social_facebook') : $this->facebook;

        $links = [];
        if ($wa) {
            $links[] = ['type' => 'whatsapp', 'label' => 'WhatsApp', 'url' => 'https://wa.me/'.ltrim($wa, '+'), 'color' => 'bg-emerald-500 hover:bg-emerald-600'];
        }
        if ($tg) {
            $links[] = ['type' => 'telegram', 'label' => 'Telegram', 'url' => 'https://t.me/'.ltrim($tg, '@'), 'color' => 'bg-sky-500 hover:bg-sky-600'];
        }
        if ($insta) {
            $links[] = ['type' => 'instagram', 'label' => 'Instagram', 'url' => $insta, 'color' => 'bg-gradient-to-br from-fuchsia-500 to-orange-500 hover:opacity-90'];
        }
        foreach (['twitter' => $tw, 'github' => $gh, 'discord' => $dc, 'facebook' => $fb] as $type => $url) {
            if ($url && $url !== '#') {
                $links[] = ['type' => $type, 'label' => ucfirst($type), 'url' => $url, 'color' => 'bg-slate-700 hover:bg-slate-800'];
            }
        }

        return $links;
    }
}
