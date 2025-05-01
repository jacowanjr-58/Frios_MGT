<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use DB;


class FpgItem extends Model
{
    use HasFactory;

    protected $table = 'fpg_items'; // Ensure table name is correct
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

    // Many-to-many relationship with FpgCategory
    public function categories()
    {
        return $this->belongsToMany(FpgCategory::class, 'fpg_category_fpg_item', 'fgp_item_id', 'category_ID');
    }

    public function Orders() {
        return $this->hasMany(FpgOrder::class, 'fgp_item_id')->where('status', 'delivered');
    }
    public function InventoryAllocations() {
        return $this->hasMany(InventoryAllocation::class, 'fpg_item_id');
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
        ->join('fpg_orders', 'fpg_orders.fgp_ordersID', '=', 'fgp_order_details.fpg_order_id')
        ->where('fgp_order_details.fgp_item_id', $this->fgp_item_id)
        ->where('fpg_orders.status', 'Delivered')
        ->sum('fgp_order_details.unit_number');
}

}
