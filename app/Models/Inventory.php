<?php

namespace App\Models;

use App\Models\FgpItem;
use App\Models\Location;
use App\Models\Franchisee;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    // 1) Link to the correct table & primary key
    protected $table = 'inventory';
    protected $primaryKey = 'inventory_id';
    public $timestamps = true; // since created_at/updated_at exist

    // 2) Allow fillable fields
    protected $fillable = [
        'franchisee_id',
        'fgp_item_id',
        'stock_on_hand',
        'stock_count_date',
        'locations_ID',
        'pops_on_hand',
        'whole_sale_price_case',
        'retail_price_pop',
    ];

    // 3) (Optional) Define relationships
    public function item()
    {
        return $this->belongsTo(FgpItem::class, 'fgp_item_id', 'fgp_item_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'locations_ID', 'locations_ID');
    }

    public function franchisee()
    {
        return $this->belongsTo(Franchisee::class, 'franchisee_id', 'franchisee_id');
    }
}
