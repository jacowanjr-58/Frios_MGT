<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FgpOrder extends Model
{
    use HasFactory;

    protected $table = 'fgp_orders';

    protected $primaryKey = 'fgp_ordersID';  // tell Eloquent the primary key

    public $timestamps = true; // if you have timestamps (created_at, updated_at)


    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_ID', 'user_id');
    }

    public function item()
    {
        return $this->belongsTo(FgpItem::class, 'fgp_item_id', 'name');
    }

    public function orderDetails()
    {
        return $this->hasMany(FgpOrderDetail::class, 'fgp_order_id', 'fgp_ordersID');
    }
}
