<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FgpOrder extends Model
{
    use HasFactory;

    // protected $table = 'fgp_order_details';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_ID', 'user_id');
    }

    public function item()
    {
        return $this->belongsTo(FgpItem::class, 'fgp_item_id', 'name');
    }
}
