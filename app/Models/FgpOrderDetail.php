<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FgpOrderDetail extends Model
{
    protected $guarded = [];

    public function flavor()
    {
        return $this->belongsTo(FgpItem::class, 'fgp_item_id', 'fgp_item_id');
    }
}
