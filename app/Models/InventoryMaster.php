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
        'split_total_quantity',
        'default_cost',
        'split_factor',
        'cogs_case','cogs_unit',
        'wholesale_case','wholesale_unit',
        'retail_case','retail_unit',
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
     * Break total_quantity into cases + units given split_factor.
     *
     * @return array{total:int, cases:int, units:int}
     */
    public function getQuantityBreakdownAttribute(): array
    {
        $total = (int) $this->total_quantity;
        $split = (int) ($this->split_factor ?: 1);

        return [
            'total' => $total,
            'cases' => intdiv($total, $split),
            'units' => $total % $split,
        ];
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

    public function allocations()
    {

        return $this->hasMany(InventoryAllocation::class,
                              'inventory_id',   // FK on the allocations table
                              'inventory_id');  // PK on this table
    }
}
