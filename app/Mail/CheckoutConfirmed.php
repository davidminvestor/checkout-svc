<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CheckoutConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;
    public string $total;
    public array $products;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $name, string $total, array $products)
    {
        $this->name     = $name;
        $this->total    = $total;
        $this->products = $products;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Checkout confirmed. ')
            ->markdown('emails.checkout-confirmed');
    }
}
