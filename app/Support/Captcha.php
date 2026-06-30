<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;

class Captcha
{
    /**
     * Whether captcha is enabled and configured.
     */
    public static function enabled(): bool
    {
        return setting('captcha_enabled') === '1' && setting('captcha_site_key') && setting('captcha_secret');
    }

    /**
     * Verify a Google reCAPTCHA response token.
     */
    public static function verify(?string $token): bool
    {
        if (! self::enabled()) {
            return true; // Captcha not in use → always pass.
        }

        if (! $token) {
            return false;
        }

        try {
            $response = Http::asForm()->timeout(8)->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => setting('captcha_secret'),
                'response' => $token,
            ]);

            return (bool) ($response->json('success') ?? false);
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }
}
