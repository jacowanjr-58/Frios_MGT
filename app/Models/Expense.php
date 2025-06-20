<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Expense extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($expense) {
            if (Auth::check()) {
                $expense->created_by = Auth::id();
                $expense->updated_by = Auth::id();
                $expense->franchisee_id = session('franchisee_id') ?? null;
            }
        });

        static::updating(function ($expense) {
            if (Auth::check()) {
                $expense->updated_by = Auth::id();
                $expense->franchisee_id = session('franchisee_id') ?? null;
            }
        });
    }

    public function category(){
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function sub_category(){
        return $this->belongsTo(ExpenseSubCategory::class);
    }

    public function franchisee()
    {
        return $this->belongsTo(Franchisee::class, 'franchisee_id', 'franchisee_id');
    }
    // In App\Models\Expense.php

public function subcategory()
{
    return $this->belongsTo(Subcategory::class);
}


}
