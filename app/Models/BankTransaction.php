<?php
// File: app/Models/BankTransaction.php Plaid Integration


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{
    use HasFactory;

     protected $fillable = [
        'franchise_id',
        'bank_account_id',
        'transaction_id',
        'date',
        'name',
        'amount',
        'category',
        'sub_category',
        'expense_category_id',
        'expense_sub_category_id',
        'income_category_id',
    ];

    public function franchise()
    {
        return $this->belongsTo(Franchise::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
    public function expenseSubCategory()
    {
        return $this->belongsTo(ExpenseSubCategory::class, 'expense_sub_category_id');
    }
    public function incomeCategory()
    {
        return $this->belongsTo(IncomeCategory::class, 'income_category_id');
    }
}
