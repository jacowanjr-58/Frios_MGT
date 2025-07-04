<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FgpItem extends Model
{
    use HasFactory;

    protected $casts = [
        'dates_available' => 'array',
    ];
    
    protected $fillable = [
        'name',
        'description',
        'case_cost',
        'internal_inventory',
        'split_factor',
        'dates_available',
        'image1',
        'image2',
        'image3',
        'orderable',
        'created_by',
        'updated_by',
    ];

    protected static function booted()
    {
        static::creating(function ($fgpItem) {
            if (Auth::check()) {
                $fgpItem->created_by = Auth::id();
                $fgpItem->updated_by = Auth::id();
            }
        });

        static::updating(function ($fgpItem) {
            if (Auth::check()) {
                $fgpItem->updated_by = Auth::id();
            }
        });
    }

    /**
     * All inventory_master rows for this FGP item.
     */
    public function inventories()
    {
        return $this->hasMany(InventoryMaster::class, 'fgp_item_id');
    }


    // Many-to-many relationship with FgpCategory
    public function categories()
    {
        return $this->belongsToMany(FgpCategory::class, 'fgp_category_fgp_item', 'fgp_item_id', 'fgp_category_id');
    }

    public function orderItems()
    {
        return $this->hasMany(FgpOrderItem::class, 'fgp_item_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(FgpOrderDetail::class, 'fgp_item_id');
    }

    public function Orders()
    {
        return $this->hasMany(FgpOrder::class, 'fgp_item_id')->where('status', 'delivered');
    }

    public function InventoryAllocations()
    {
        return $this->hasMany(InventoryAllocation::class, 'fgp_item_id');
    }

    // public function availableQuantity() {
    //     $a = $this->Orders()->sum('unit_number');
    //     $b = $this->InventoryAllocations()->sum('quantity');
    //     Log::info('Orders qty: ' . $a);
    //     Log::info('Inventory qty: ' . $b);
    //     return $a - $b;
    // }




    public function availableQuantity()
    {
        return DB::table('fgp_order_details')
            ->join('fgp_orders', 'fgp_orders.id', '=', 'fgp_order_details.fgp_order_id')
            ->where('fgp_order_details.fgp_item_id', $this->id)
            ->where('fgp_orders.status', 'Delivered')
            ->where('fgp_orders.status', 'Delivered')
            ->where('fgp_orders.user_id', Auth::user()->franchise_id)
            ->sum('fgp_order_details.unit_number');
    }

    public function allocations()
    {
        return $this->hasMany(InventoryAllocation::class, 'fgp_item_id');
    }
}
