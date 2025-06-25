<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $guarded = [];

    public function customer(){
        return $this->belongsTo(Customer::class , 'customer_id' , 'id');
    }

    public function franchise(){
        return $this->belongsTo(Franchise::class, 'franchise_id');
    }
}
