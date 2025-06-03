<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $table = 'inventory_transactions';
    protected $primaryKey = 'transaction_id';
    public $timestamps = true;

    protected $fillable = [
        'inventory_id',
        'type',
        'quantity',
        'reference',
        'notes',
        'created_by',
    ];

    public function inventoryMaster()
    {
        return $this->belongsTo(InventoryMaster::class, 'inventory_id', 'inventory_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
