<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $guarded = [];

    public function flavor()
    {
        return $this->belongsTo(FgpItem::class, 'flavor_id', 'fgp_item_id');
    }

    public function calculateAmount()
    {
        return $this->price * $this->quantity;
    }
}
