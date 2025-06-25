<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class OrderTransaction extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($orderTransaction) {
            if (Auth::check()) {
                $orderTransaction->created_by = Auth::id();
                $orderTransaction->updated_by = Auth::id();
                $orderTransaction->franchise_id = session('franchise_id') ?? null;
            }
        });

        static::updating(function ($orderTransaction) {
            if (Auth::check()) {
                $orderTransaction->updated_by = Auth::id();
                $orderTransaction->franchise_id = session('franchise_id') ?? null;
            }
        });
    }
}
