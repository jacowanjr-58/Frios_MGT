<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryMaster extends Model
{
    use HasFactory;

    protected $table = 'inventory_master';
    protected $primaryKey = 'inventory_id';

    protected $appends = ['item_name'];
    public $timestamps = true;

     protected $fillable = [
        'franchisee_id',
        'fgp_item_id',
        'custom_item_name',
        'total_quantity',
        'default_cost',
    ];

    /**
     * If this row has an fgp_item_id, return the related FgpItem’s name;
     * otherwise return custom_item_name.
     */
    public function getItemNameAttribute()
    {
        if ($this->fgp_item_id && $this->flavor) {
            return $this->flavor->name;
        }

        return $this->custom_item_name;
    }

     /**
     * Relationship to FgpItem (pop flavor).
     */
    public function flavor()
    {
        return $this->belongsTo(FgpItem::class, 'fgp_item_id', 'fgp_item_id');
    }

    public function franchisee()
    {
        return $this->belongsTo(Franchisee::class, 'franchisee_id', 'franchisee_id');
    }
}
