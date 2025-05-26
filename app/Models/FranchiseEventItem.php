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
        return $this->belongsTo(FgpItem::class, 'item_id');
    }

    public function fgpItem()
    {
        return $this->belongsTo(FgpItem::class, 'in_stock');
    }

    public function orderableItem()
    {
        return $this->belongsTo(FgpItem::class, 'orderable');
    }

    public function events()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }


}
