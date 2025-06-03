<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryRemovalQueue extends Model
{
    use HasFactory;

    protected $table = 'inventory_removal_queue';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'inventory_id',
        'location_id',
        'quantity',
        'sale_reference',
        'status',
        'requested_by',
    ];

    public function inventoryMaster()
    {
        return $this->belongsTo(InventoryMaster::class, 'inventory_id', 'inventory_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'locations_ID');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by', 'id');
    }
}
