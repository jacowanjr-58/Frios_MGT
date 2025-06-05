<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FgpOrder extends Model
{
    use HasFactory;

    protected $table = 'fgp_orders';

    protected $primaryKey = 'fgp_ordersID';  // tell Eloquent the primary key

    public $timestamps = true; // if you have timestamps (created_at, updated_at)


    protected $guarded = [];
    protected $casts = [
        'date_transaction' => 'datetime',
        'label_created_at' => 'datetime',
        'delivered_at' => 'datetime',
        'is_delivered' => 'boolean',
        'is_paid' => 'boolean',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_ID', 'user_id');
    }

    public function item()
    {
        return $this->belongsTo(FgpItem::class, 'fgp_item_id', 'name');
    }


    //Note the Plural for adding to OrderDetails
    public function items()
    {
        return $this->hasMany(FgpOrderDetail::class, 'fgp_order_id', 'fgp_ordersID');
    }

    public function orderDetails()
    {
        return $this->hasMany(FgpOrderDetail::class, 'fgp_order_id', 'fgp_ordersID');
    }

     public function orderDiscrepancies()
    {
        // If your OrderDiscrepancy table’s FK is “order_id”
        // and your local PK is “fgp_ordersID”, do:
        return $this->hasMany(
            OrderDiscrepancy::class,
            'order_id',        // FK column on order_discrepancies
            'fgp_ordersID'     // PK column on fgp_orders
        );
    }

    // 🔗 Optional: link to customer (if used)
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function flavorSummary()
    {
        return $this->items->map(function ($item) {
            return "({$item->unit_number}) {$item->flavor->name}";
        })->implode('; ');
    }

 /**
     * NEW: Summarize what *actually arrived*, by grouping `quantity_received` per flavor.
     */
    public function arrivedFlavorSummary(): string
    {
        // If no detail has a positive `quantity_received`, show a dash
        if ($this->orderDetails->sum('quantity_received') === 0) {
            return '—';
        }

        return $this->orderDetails
            ->groupBy(fn($detail) => $detail->flavor->name)
            ->map(fn($grouped, $flavorName) =>
                $grouped->sum('quantity_received') . " {$flavorName}"
            )
            ->implode(', ');
    }


    public function flavorDetails()
    {
        return $this->items->map(function ($item) {
            $subtotal = number_format($item->unit_number * $item->unit_cost, 2);
            return "Flavor: {$item->flavor->name}, Qty: {$item->unit_number}, Subtotal: \${$subtotal}";
        })->implode("\n");
    }
    // 🚚 Optional: derived full shipping address (for display)
    public function fullShippingAddress()
    {
        return trim("{$this->ship_to_address1} {$this->ship_to_address2}, {$this->ship_to_city}, {$this->ship_to_state} {$this->ship_to_zip}");
    }
}

