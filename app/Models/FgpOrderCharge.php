<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FgpOrderCharge extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fgp_order_charges';

    public $timestamps = true; // if you have timestamps (created_at, updated_at)


    protected $fillable = [
        'order_id',
        'charge_name',
        'charge_amount',
        'charge_type',
    ];

    protected $guarded = [];

}

