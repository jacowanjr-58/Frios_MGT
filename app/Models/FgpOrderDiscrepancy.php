<?php
// app/Models/OrderDiscrepancy.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FgpOrderDiscrepancy extends Model
{

    protected $table = 'fgp_order_discrepancies';
    
    protected $fillable = [
        'fgp_order_id',
        'fgp_order_item_id',
        'user_id',
        'quantity_ordered',
        'quantity_received',
        'notes',
    ];

    public function order()
    {
        return $this->belongsTo(FgpOrder::class, 'fgp_order_id');
    }

    public function detail()
    {
        return $this->belongsTo(FgpOrderItem::class, 'fgp_order_item_id'); // Fix key name
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
