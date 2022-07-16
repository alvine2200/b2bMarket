<?php

namespace App\Mail;

use App\Models\ProductsServices\Invoice as ProductsServicesInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Invoice extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ProductsServicesInvoice $invoice)
    {
        //
        $this->invoice = $invoice;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $invoice = $this->invoice;
        $order = $invoice->order;
        $recipient = $order->user;
        $address = $order->deliveryAddress;
        return $this->view('invoice')->with(compact('invoice', 'order', 'recipient', 'address'));
        // return $this->view('view.name');
    }
}
