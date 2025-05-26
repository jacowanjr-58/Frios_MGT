<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPaidMail extends Mailable
{
    use Queueable, SerializesModels;

    public $franchiseeadmin;
    public $order;
    public $pdfPath;
    public $paymentUrl;

    public function __construct($franchiseeadmin, $order, $pdfPath = null, $paymentUrl = null)
    {
        $this->franchiseeadmin = $franchiseeadmin;
        $this->order = $order;
        $this->pdfPath = $pdfPath;
        $this->paymentUrl = $paymentUrl;
    }

    public function build()
    {
        $email = $this->markdown('emails.order.paid')
            ->subject('Order Payment Confirmation')
            ->with([
                'franchiseeadmin' => $this->franchiseeadmin,
                'order' => $this->order,
                'paymentUrl' => $this->paymentUrl,
            ]);

        if ($this->pdfPath && file_exists($this->pdfPath)) {
            $email->attach($this->pdfPath);
        }

        return $email;
    }
}
