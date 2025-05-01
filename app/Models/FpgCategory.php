<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FpgCategory extends Model
{
    use HasFactory;

    protected $table = 'fpg_categories';
    protected $primaryKey = 'category_ID';
    public $timestamps = true;
    protected $casts = [
        'type' => 'array',
    ];
    protected $fillable = [
        'name',
        'type'
    ];

    // Many-to-many relationship with FpgItem
    public function items()
    {
        return $this->belongsToMany(FpgItem::class, 'fpg_category_fpg_item', 'category_ID', 'fgp_item_id');
    }
    
}
