<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryMaster extends Model
{
    use HasFactory;

    protected $table = 'inventory_master';
    protected $primaryKey = 'inventory_id';
    public $timestamps = true;

    protected $fillable = [
        'franchisee_id',
        'fgp_item_id',
        'custom_item_name',
        'total_quantity',
    ];

    public function flavor()
    {
        return $this->belongsTo(FgpItem::class, 'fgp_item_id', 'fgp_item_id');
    }

    public function franchisee()
    {
        return $this->belongsTo(Franchisee::class, 'franchisee_id', 'franchisee_id');
    }
}
