<?php

namespace App\Enums;

enum InventoryRemovalQueue: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
}   