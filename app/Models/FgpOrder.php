<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FgpOrder extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    protected static function booted()
    {
        static::creating(function ($fgpOrder) {
            if (Auth::check()) {
                $fgpOrder->created_by = Auth::id();
                $fgpOrder->updated_by = Auth::id();
                $fgpOrder->franchise_id = session('franchise_id') ?? null;
            }
        });

        static::updating(function ($fgpOrder) {
            if (Auth::check()) {
                $fgpOrder->updated_by = Auth::id();
                $fgpOrder->franchise_id = session('franchise_id') ?? null;
            }
        });
    }
    public function getDateJoinedAttribute($value)
    {
        return $value ? date('m-d-Y', strtotime($value)) : null;
    }

    /**
     * Get the created_at formatted as mm-dd-yyyy
     */
    public function getCreatedAtAttribute($value)
    {
        return $value ? date('m-d-Y', strtotime($value)) : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function franchise()
    {
        return $this->belongsTo(Franchise::class, 'franchise_id');
    }

    public function item()
    {
        return $this->belongsTo(FgpItem::class, 'fgp_item_id', 'name');
    }

    public function getOrderNum() : string{
            return $this->order_num ?? "FGP-" . $this->id;
    }

    //Note the Plural for adding to OrderDetails
    public function items()
    {
        return $this->hasMany(FgpOrderDetail::class, 'fgp_order_id');
    }

    public function orderItems()
    {
        return $this->hasMany(FgpOrderItem::class, 'fgp_order_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(FgpOrderDetail::class, 'fgp_order_id');
    }

     public function orderDiscrepancies()
    {
        // If your OrderDiscrepancy table's FK is "order_id"
        // and your local PK is "id", do:
        return $this->hasMany(
            OrderDiscrepancy::class,
            'order_id'        // FK column on order_discrepancies
        );
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

