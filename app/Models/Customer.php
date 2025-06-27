<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Customer extends Model
{
    use HasFactory;


    protected $table = 'customers'; // Table name
    protected $primaryKey = 'id';
    protected $guarded = [];


    protected static function booted()
    {
        static::creating(function ($expense) {
            if (Auth::check()) {
                $expense->created_by = Auth::id();
                $expense->updated_by = Auth::id();
                $expense->franchise_id = session('franchise_id') ?? null;
            }
        });

        static::updating(function ($expense) {
            if (Auth::check()) {
                $expense->updated_by = Auth::id();
                $expense->franchise_id = session('franchise_id') ?? null;
            }
        });
    }
    public function franchise(): BelongsTo
    {
        return $this->belongsTo(Franchise::class, 'franchise_id');
    }
}
