<?php

namespace App\Enums;

enum FgpOrders: string
{
    case PENDING = 'Pending';
    case PAID = 'Paid';
    case SHIPPED = 'Shipped';
    case DELIVERED = 'Delivered';
    case CANCELLED = 'Cancelled';
    case PENDING_FULFILLMENT = 'Pending Fulfillment';
    case ON_HOLD = 'On Hold';
    case AWAITING_SHIPMENT = 'Awaiting Shipment';
    case AWAITING_PAYMENT = 'Awaiting Payment';
    case SHIPPED = 'Shipped';
    case DELIVERED = 'Delivered';
    case CANCELLED = 'Cancelled';
}