<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use DB;
use Illuminate\Support\Facades\Auth;

class FgpItem extends Model
{
    use HasFactory;

    protected $table = 'fgp_items'; // Ensure table name is correct
    protected $primaryKey = 'fgp_item_id'; // Explicitly define primary key
    public $timestamps = true; // Ensure timestamps are handled

    protected $fillable = [
        'category_ID',
        'name',
        'description',
        'case_cost',
        'internal_inventory',
        'dates_available',
        'image1',
        'image2',
        'image3',
        'orderable'
    ];

    // Many-to-many relationship with FgpCategory
    public function categories()
    {
        return $this->belongsToMany(FgpCategory::class, 'fgp_category_fgp_item', 'fgp_item_id', 'category_ID');
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
            ->join('fgp_orders', 'fgp_orders.fgp_ordersID', '=', 'fgp_order_details.fgp_order_id')
            ->where('fgp_order_details.fgp_item_id', $this->fgp_item_id)
            ->where('fgp_orders.status', 'Delivered')
            ->where('fgp_orders.status', 'Delivered')
            ->where('fgp_orders.user_ID', Auth::user()->franchisee_id)
            ->sum('fgp_order_details.unit_number');
    }

    public function allocations()
    {
        return $this->hasMany(InventoryAllocation::class, 'fgp_item_id', 'fgp_item_id');
    }
}
