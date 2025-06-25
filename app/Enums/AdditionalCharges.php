<?php

namespace App\Enums;

enum AdditionalCharges: string
{
    case OPTIONAL = 'optional';
    case REQUIRED = 'required';
    case FIXED = 'fixed';
    case PERCENTAGE = 'percentage';
}
