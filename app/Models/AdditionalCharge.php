<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    protected $casts = [
        'charge_price' => 'decimal:2',
    ];
}
