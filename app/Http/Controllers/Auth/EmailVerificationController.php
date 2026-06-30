<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmailMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailVerificationController extends Controller
{
    /**
     * Verify the user's email from a signed link (works without login).
     */
    public function verify(Request $request, int $id, string $hash): RedirectResponse
    {
        abort_unless($request->hasValidSignature(), 403, 'Invalid or expired verification link.');

        $user = User::findOrFail($id);

        if (! hash_equals($hash, sha1($user->email))) {
            abort(403, 'Invalid verification link.');
        }

        if (! $user->hasVerifiedEmail()) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        return redirect()->route('login')->with('success', 'Your email is verified. You can now sign in.');
    }

    /**
     * Resend a verification link for the given email.
     */
    public function resend(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $request->email)->first();

        if ($user && ! $user->hasVerifiedEmail()) {
            try {
                Mail::to($user->email)->send(new VerifyEmailMail($user));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('success', 'If that account needs verification, we have sent a new link.');
    }
}
