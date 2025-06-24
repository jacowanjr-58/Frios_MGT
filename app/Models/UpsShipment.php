<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpsShipment extends Model
{
    protected $fillable = [
        'fgp_order_id',
        'shipment_id',
        'tracking_number',
        'label_format',
        'label_file_path',
        'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'array',
    ];
}

