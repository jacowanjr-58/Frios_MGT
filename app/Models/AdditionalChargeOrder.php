<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalChargeOrder extends Model
{
    use HasFactory;

    protected $table = 'fgp_order_charges'; // Table name
   
    protected $fillable = [
        'order_id',
        'charge_name',
        'charge_amount',
        'charge_type',
    ];

   
    protected $casts = [
        'charge_price' => 'decimal:2',
    ];
}
