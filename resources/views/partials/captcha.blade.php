@if (\App\Support\Captcha::enabled())
    <div class="g-recaptcha" data-sitekey="{{ setting('captcha_site_key') }}"></div>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif
