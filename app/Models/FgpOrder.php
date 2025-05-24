<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FgpOrder extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'date_transaction' => 'datetime',
        'label_created_at' => 'datetime',
        'delivered_at' => 'datetime',
        'is_delivered' => 'boolean',
        'is_paid' => 'boolean',
    ];

    // 🔗 Link to the user who placed the order
    public function user()
    {
        return $this->belongsTo(User::class, 'user_ID', 'user_id');
    }

    // 🔗 Link to the order items (1-to-many)
    public function items()
    {
        return $this->hasMany(FgpOrderDetail::class, 'fgp_order_id', 'fgp_ordersID');
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