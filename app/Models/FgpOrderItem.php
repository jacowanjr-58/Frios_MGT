<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FgpOrderItem extends Model
{
    protected $table = 'fgp_order_items';

    protected $fillable = [
        'fgp_order_id',
        'fgp_item_id',
        'quantity',
        'unit_price',
        'price',
    ];

    public function fgpItem()
    {
        return $this->belongsTo(FgpItem::class, 'fgp_item_id');
    }

    public function item()
    {
        return $this->belongsTo(FgpItem::class, 'fgp_item_id');
    }

    public function order()
    {
        return $this->belongsTo(FgpOrder::class, 'fgp_order_id');
    }

    public function flavor()
    {
        return $this->belongsTo(FgpItem::class, 'fgp_item_id');
    }
} 