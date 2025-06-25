<?php

namespace App\Enums;

enum EventStatus: string
{
    case SCHEDULED = 'scheduled';
    case TENTATIVE = 'tentative';
    case STAFFED = 'staffed';
}