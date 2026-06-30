<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmailMail;
use App\Models\User;
use App\Support\Captcha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if (! Captcha::verify($request->input('g-recaptcha-response'))) {
            throw ValidationException::withMessages(['email' => 'Captcha verification failed. Please try again.']);
        }

        $requireVerify = setting('require_email_verification') === '1';

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'email_verified_at' => $requireVerify ? null : now(),
        ]);

        if ($requireVerify) {
            try {
                Mail::to($user->email)->send(new VerifyEmailMail($user));
            } catch (\Throwable $e) {
                report($e);
            }

            return redirect()->route('login')
                ->with('success', 'Account created! Please check your email to verify your account before signing in.');
        }

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Welcome to '.setting('site_name', config('marketplace.name')).'! Your account is ready.');
    }
}
