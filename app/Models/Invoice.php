<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = [];

     protected $casts = [
        'direction' => 'string',
    ];

    public function isPayable(): bool
    {
        return $this->direction === 'payable';
    }

    public function isReceivable(): bool
    {
        return $this->direction === 'receivable';
    }

    public function customer(){
        return $this->belongsTo(Customer::class , 'customer_id' , 'customer_id');
    }

    public function franchisee(){
        return $this->belongsTo(Franchisee::class , 'franchisee_id' , 'franchisee_id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }


}
