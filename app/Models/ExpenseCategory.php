<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $guarded = [];

    public function expenseSubCategories()
{
    return $this->hasMany(ExpenseSubCategory::class, 'expense_category_id');
}
}
