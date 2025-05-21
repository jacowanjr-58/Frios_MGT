<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPaidMail extends Mailable
{
    use Queueable, SerializesModels;

    public $corporateAdmin;
    public $order;
    public $pdfPath;

    public function __construct($corporateAdmin, $order, $pdfPath = null)
    {
        $this->corporateAdmin = $corporateAdmin;
        $this->order = $order;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        $email = $this->markdown('emails.order.paid')
                      ->subject('Order Payment Confirmation')
                      ->with([
                          'corporateAdmin' => $this->corporateAdmin,
                          'order' => $this->order,
                      ]);

        if ($this->pdfPath && file_exists($this->pdfPath)) {
            $email->attach($this->pdfPath);
        }

        return $email;
    }
}
