<?php

namespace App\Mail;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReviewApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Review $review) {}

    public function build(): self
    {
        return $this->subject('Your review has been published')
            ->view('emails.review-approved');
    }
}
