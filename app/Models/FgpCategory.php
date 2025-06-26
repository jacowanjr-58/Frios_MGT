<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FgpCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type'
    ];

    // Many-to-many relationship with FgpItem
    public function items()
    {
        return $this->belongsToMany(FgpItem::class, 'fgp_category_fgp_item', 'category_id', 'fgp_item_id');
    }

    public function getFormattedCreatedAtAttribute()
    {
        return Carbon::parse($this->created_at)->format('M d, Y');
    }
}
