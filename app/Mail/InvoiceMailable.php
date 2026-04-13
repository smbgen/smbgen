<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice)
    {
        //
    }

    public function build()
    {
        $invoice = $this->invoice->load('items', 'user');

        return $this->subject('Invoice #'.$invoice->id.' from '.config('app.name'))
            ->view('emails.invoice')
            ->with([
                'invoice' => $invoice,
            ]);
    }
}
