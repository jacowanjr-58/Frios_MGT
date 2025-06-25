<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FgpCategory extends Model
{
    use HasFactory;

    protected $table = 'fgp_categories';
    public $timestamps = true;
    protected $casts = [
        'type' => 'array',
    ];
    protected $fillable = [
        'name',
        'type'
    ];

    protected static function booted()
    {
        static::creating(function ($fgpCategory) {
            if (Auth::check()) {
                $fgpCategory->created_by = Auth::id();
                $fgpCategory->updated_by = Auth::id();
                $fgpCategory->franchise_id = session('franchise_id') ?? null;
            }
        });

        static::updating(function ($fgpCategory) {
            if (Auth::check()) {
                $fgpCategory->updated_by = Auth::id();
                $fgpCategory->franchise_id = session('franchise_id') ?? null;
            }
        });
    }

    // Many-to-many relationship with FgpItem
    public function items()
    {
        return $this->belongsToMany(FgpItem::class, 'fgp_category_fgp_item', 'category_id', 'fgp_item_id');
    }

    public function franchise()
    {
        return $this->belongsTo(Franchise::class, 'franchise_id');
    }

    public function getFormattedCreatedAtAttribute()
    {
        return Carbon::parse($this->created_at)->format('m/d/Y');
    }
}
