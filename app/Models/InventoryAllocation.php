<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryAllocation extends Model
{
    // 1) Use $fillable (or $guarded) so custom_item_name can be mass-assigned
    protected $fillable = [
        'fgp_item_id',
        'custom_item_name',
        'franchise_id',
        'location',
        'quantity',
    ];

    /**
     * If fgp_item_id is non-null, this gives the real flavor.
     */
    public function flavor()
    {
        return $this->belongsTo(FgpItem::class, 'fgp_item_id', 'fgp_item_id');
    }

    /**
     * If fgp_item_id is null, you read custom_item_name directly.
     */
}
