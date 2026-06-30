<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewProductMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Product $product) {}

    public function build(): self
    {
        return $this->subject('New product: '.$this->product->title)
            ->view('emails.new-product');
    }
}
