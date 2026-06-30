<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login form.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! \App\Support\Captcha::verify($request->input('g-recaptcha-response'))) {
            throw ValidationException::withMessages([
                'email' => 'Captcha verification failed. Please try again.',
            ]);
        }

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        // Block banned accounts.
        if (Auth::user()->is_banned) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => __('This account has been suspended. Please contact support.'),
            ]);
        }

        // Require a verified email when the setting is enabled.
        if (setting('require_email_verification') === '1' && ! Auth::user()->hasVerifiedEmail()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Please verify your email address before signing in. Check your inbox or use the resend option below.',
            ]);
        }

        $request->session()->regenerate();

        // Admins go straight to the admin dashboard, everyone else to theirs.
        $redirect = Auth::user()->isAdmin()
            ? route('admin.dashboard')
            : route('dashboard');

        return redirect()->intended($redirect);
    }

    /**
     * Destroy an authenticated session (logout).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been logged out.');
    }
}
