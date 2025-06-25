<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class InventoryMaster extends Model
{
    use HasFactory;

    protected $table = 'inventory_master';

    protected $appends = ['item_name', 'cases', 'units',];
    public $timestamps = true;
    protected $casts = [
    'stock_count_date' => 'date',
    ];

     protected $fillable = [
            'franchise_id','fgp_item_id','custom_item_name',
    'total_quantity','split_total_quantity','default_cost',
    'split_factor','cogs_case','cogs_unit',
    'wholesale_case','wholesale_unit','retail_case','retail_unit',
    'image1','image2','image3',
];

    /**
     * If this row has an fgp_item_id, return the related FgpItem's name;
     * otherwise return custom_item_name.
     * use:  $inventoryMaster->item_name to invoke it (snake_case Laravel magic)
     */
    /**
     * Accessor: combined corporate and custom item name.
     */
    public function getItemNameAttribute(): string
    {
        $corporate = $this->flavor ? $this->flavor->name : '';
        $custom    = $this->custom_item_name ?: '';

        if ($corporate && $custom) {
            return "{$corporate} - {$custom}";
        }

        return $corporate ?: $custom;
    }

    /**
     * Accessor: number of full cases based on total_quantity and split_factor.
     */
    public function getCasesAttribute(): int
    {
        $split = (int) ($this->split_factor ?: 1);
        return intdiv((int) $this->total_quantity, $split);
    }

    /**
     * Accessor: remaining units after full cases.
     */
    public function getUnitsAttribute(): int
    {
        $split = (int) ($this->split_factor ?: 1);
        return ((int) $this->total_quantity) % $split;
    }

    /**
     * Relationship: corporate FGP item (flavor).
     */
    public function flavor()
    {
        return $this->belongsTo(FgpItem::class, 'fgp_item_id');
    }

    /**
     * Relationship: allocations per location.
     */
    public function allocations()
    {
        return $this->hasMany(InventoryAllocation::class, 'inventory_id');
    }

    /**
     * Relationship: owning franchise.
     */
    public function franchise()
    {
        return $this->belongsTo(Franchise::class, 'franchise_id');
    }
}
