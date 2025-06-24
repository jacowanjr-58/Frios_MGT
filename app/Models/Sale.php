<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $guarded = [];

    public function franchise(){
        return $this->belongsTo(Franchise::class, 'franchise_id');
    }
}
