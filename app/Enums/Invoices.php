<?php

namespace App\Enums;

enum Invoices: string
{
    case RECEIVABLE = 'receivable';
    case PAYABLE = 'payable';
    case UNPAID = 'unpaid';
    case PARTIAL = 'partial';
    case PAID = 'paid';
}