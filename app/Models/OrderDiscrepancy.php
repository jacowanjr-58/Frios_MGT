<?php
// app/Models/OrderDiscrepancy.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDiscrepancy extends Model
{
    protected $fillable = [
        'order_id',
        'order_detail_id',
        'quantity_ordered',
        'quantity_received',
        'notes',
    ];

    public function order()
    {
        return $this->belongsTo(FgpOrder::class, 'order_id');
    }

    public function detail()
    {
        return $this->belongsTo(FgpOrderDetail::class, 'order_detail_id');
    }
}

