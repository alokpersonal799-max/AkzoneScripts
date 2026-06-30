<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class VerifyEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $verifyUrl;

    public function __construct(public User $user)
    {
        $this->verifyUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHours(48),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );
    }

    public function build(): self
    {
        return $this->subject('Verify your email address')->view('emails.verify-email');
    }
}
