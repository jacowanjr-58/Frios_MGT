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
        return $this->belongsTo(Franchisee::class, 'franchisee_id', 'franchisee_id');
    }
    // In App\Models\Expense.php

public function subcategory()
{
    return $this->belongsTo(Subcategory::class);
}


}
