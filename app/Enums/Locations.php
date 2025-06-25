<?php

namespace App\Enums;

enum Locations: string
{
    case ON_SITE = 'On-Site';
    case OFF_SITE = 'Off-Site';
    case OTHER = 'Other';
}

