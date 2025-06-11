<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers'; // Table name
    protected $primaryKey = 'customer_id'; // Primary key

    protected $guarded = [];


    public function franchisee(): BelongsTo
    {
        return $this->belongsTo(Franchisee::class, 'franchisee_id', 'franchisee_id');
    }
}
