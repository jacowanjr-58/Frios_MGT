<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLocation extends Model
{
    protected $table = 'inventory_locations';

    protected $primaryKey = 'id';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'franchise_id',
    ];

    public function inventoryMasters()
    {
        return $this->hasMany(InventoryMaster::class, 'franchise_id', 'franchise_id');
    }
    public function franchise()
    {
        return $this->belongsTo(Franchise::class, 'franchise_id');
    }
}
