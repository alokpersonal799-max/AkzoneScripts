<?php

namespace App\Mail;

use App\Models\Product;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PriceDropMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Product $product, public User $user) {}

    public function build(): self
    {
        return $this->subject('Price drop on '.$this->product->title)
            ->view('emails.price-drop');
    }
}
