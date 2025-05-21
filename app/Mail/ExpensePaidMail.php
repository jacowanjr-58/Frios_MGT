<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExpensePaidMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pdfPath;
    public $corporateAdmin;
    public $expense;

    public function __construct($corporateAdmin, $expense, $pdfPath = null)
    {
        $this->corporateAdmin = $corporateAdmin;
        $this->expense = $expense;
        $this->pdfPath = $pdfPath;
    }


    public function build()
    {
        $email = $this->markdown('emails.expense.paid')
                    ->subject('Expense Payment Confirmation')
                    ->with([
                        'corporateAdmin' => $this->corporateAdmin,
                        'expense' => $this->expense,
                    ]);

        // Check if PDF path exists and attach
        if ($this->pdfPath && file_exists($this->pdfPath)) {
            $email->attach($this->pdfPath);
        }

        return $email;
    }

}
