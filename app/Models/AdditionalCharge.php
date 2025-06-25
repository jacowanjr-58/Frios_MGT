<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AdditionalCharge extends Model
{
    use HasFactory;

    protected $table = 'additionalcharges'; // Table name
    protected $primaryKey = 'additionalcharges_id'; // Primary key

    protected $fillable = [
        'charge_name',
        'charge_price',
        'charge_optional',
        'charge_type',
        'status'
    ];

    protected static function booted()
    {
        static::creating(function ($additionalCharge) {
            if (Auth::check()) {
                $additionalCharge->created_by = Auth::id();
                $additionalCharge->updated_by = Auth::id();
                $additionalCharge->franchise_id = session('franchise_id') ?? null;
            }
        });

        static::updating(function ($additionalCharge) {
            if (Auth::check()) {
                $additionalCharge->updated_by = Auth::id();
                $additionalCharge->franchise_id = session('franchise_id') ?? null;
            }
        });
    }

    protected $casts = [
        'charge_price' => 'decimal:2',
    ];
}
