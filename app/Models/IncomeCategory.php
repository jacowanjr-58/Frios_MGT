<?php
// Plaid integration for income categories
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomeCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
    ];

    public function bankTransactions()
    {
        return $this->hasMany(BankTransaction::class, 'income_category_id');
    }
}
