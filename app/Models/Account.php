<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Account extends Model
{
    protected $guarded = [];

    protected $casts = [
    'card_number' => 'encrypted',
    'card_expiry' => 'encrypted',
    'card_cvc' => 'encrypted',
];
}
