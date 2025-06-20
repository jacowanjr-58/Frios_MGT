<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ExpenseSubCategory extends Model
{
    protected $guarded = [];

    public function category(){
        return $this->belongsTo(ExpenseCategory::class);
    }

    protected static function booted()
    {
        static::creating(function ($expenseSubCategory) {
            if (Auth::check()) {
                $expenseSubCategory->created_by = Auth::id();
                $expenseSubCategory->updated_by = Auth::id();
                $expenseSubCategory->franchisee_id = session('franchisee_id') ?? null;
            }
        });

        static::updating(function ($expenseSubCategory) {
            if (Auth::check()) {
                $expenseSubCategory->updated_by = Auth::id();
                $expenseSubCategory->franchisee_id = session('franchisee_id') ?? null;
            }
        });
    }
}
