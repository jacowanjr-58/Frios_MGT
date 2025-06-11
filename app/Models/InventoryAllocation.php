<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryAllocation extends Model
{
    use HasFactory;

    protected $table = 'inventory_allocations';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'inventory_id',
        'location_id',
        'location',
        'franchise_id',
        'quantity',
        'allocated_quantity',
        'allocated_cases',
        'allocated_units',
        'created_at',
        'updated_at',
    ];

    public function inventoryMaster()
    {
        return $this->belongsTo(InventoryMaster::class, 'inventory_id', 'inventory_id');
    }

    public function location()
    {
        return $this->belongsTo(InventoryLocation::class, 'location_id', 'locations_ID');
    }
}
