<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FgpOrderDetail extends Model
{
    protected $guarded = [];

    protected $fillable = [
        'fgp_order_id',
        'fgp_item_id',
        'unit_number',
        'unit_cost',
        'date_transaction',
        'quantity_received',
        // …any others you want to mass‐assign
    ];

    public function fgp_item()
    {
        return $this->belongsTo(FgpItem::class, 'fgp_item_id', 'id');
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
        return $this->belongsTo(FgpItem::class, 'fgp_item_id', 'id');
    }

    public function discrepancy()
    {
        return $this->hasOne(
            FgpOrderDiscrepancy::class,
            'order_detail_id',  // FK on order_discrepancies
            'id'                // PK of this detail
        );
    }
}
