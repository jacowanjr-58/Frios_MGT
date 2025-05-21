<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventPaidMail extends Mailable
{
    use Queueable, SerializesModels;

    public $franchisee;
    public $eventTransaction;
    public $eventItems;
    public $pdfPath;

    public function __construct($franchisee, $eventTransaction, $eventItems, $pdfPath = null)
    {
        $this->franchisee = $franchisee;
        $this->eventTransaction = $eventTransaction;
        $this->eventItems = $eventItems;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        $email = $this->markdown('emails.event.paid')
                      ->subject('Event Payment Confirmation')
                      ->with([
                          'franchisee' => $this->franchisee,
                          'eventTransaction' => $this->eventTransaction,
                          'eventItems' => $this->eventItems,
                      ]);

        if ($this->pdfPath && file_exists($this->pdfPath)) {
            $email->attach($this->pdfPath);
        }

        return $email;
    }
}
