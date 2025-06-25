<?php

namespace App\Enums;

enum EventPaymentPlan: string
{
    case CASH = 'cash';
    case CHECK = 'check';
    case INVOICE = 'invoice';
    case CREDIT_CARD = 'credit-card';
}