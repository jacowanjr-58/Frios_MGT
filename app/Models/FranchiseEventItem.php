<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FranchiseEventItem extends Model
{
    protected $guarded = ['id'];

    public function event() {
        return $this->belongsTo(FranchiseEvent::class);
    }

    public function item() {
        return $this->belongsTo(FpgItem::class, 'item_id');
    }

    public function fpgItem()
    {
        return $this->belongsTo(FpgItem::class, 'in_stock');
    }

    public function orderableItem()
    {
        return $this->belongsTo(FpgItem::class, 'orderable');
    }


}
