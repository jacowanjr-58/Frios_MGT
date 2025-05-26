<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use App\Models\User;

class InvoiceMail extends Mailable
{
use Queueable, SerializesModels;

    public $invoice;
    public $invoiceItems;
    public $franchisee;
    public $stripePublicKey;
    public $paymentUrl;

    public function __construct($invoice, $invoiceItems, $franchisee)
    {
        $this->invoice = $invoice;
        $this->invoiceItems = $invoiceItems;
        $this->franchisee = $franchisee;

        // Fetch Stripe keys from the database
        $user = User::where('franchisee_id', $invoice->franchisee_id)->first();

        // Set the API key from the Stripe record associated with the franchisee
        Stripe::setApiKey(config('services.stripe.secret'));
        // Create the Stripe checkout session
        // Create the Stripe checkout session
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Invoice #' . $invoice->id,
                        ],
                        'unit_amount' => $invoice->total_price * 100,
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'payment_intent_data' => [
                'transfer_data' => [
                    'destination' => $user->stripe_account_id, // ✅ Route to connected account
                ],
            ],
            'success_url' => route('payment.success', ['invoice' => $invoice->id]),
            'cancel_url' => route('payment.cancel', ['invoice' => $invoice->id]),
        ]);

        $invoice->stripe_session_id = $session->id;
        $invoice->save();

        // Store the checkout session URL
        $this->paymentUrl = $session->url;
    }

    public function build()
    {
        return $this->markdown('emails.invoice')
            ->subject('Your Invoice INV-00' . $this->invoice->id)
            ->with([
                'paymentUrl' => $this->paymentUrl,
            ])
            ->attachData($this->generatePdf(), 'invoice.pdf', [
                'mime' => 'application/pdf',
            ]);
    }

    private function generatePdf()
    {
        $pdf = PDF::loadView('franchise_admin.payment.pdf.invoice', [
            'invoice' => $this->invoice,
            'invoiceItems' => $this->invoiceItems,
            'franchisee' => $this->franchisee,
        ]);
        return $pdf->output();
    }
}
