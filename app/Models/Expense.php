<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $guarded = [];

    public function category(){
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function sub_category(){
        return $this->belongsTo(ExpenseSubCategory::class);
    }

    public function franchisee()
    {
        return $this->belongsTo(User::class, 'franchisee_id', 'franchisee_id');
    }
}
