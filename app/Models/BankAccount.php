<?php
// BankAccount.php PLaid Integration
namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class BankAccount extends Model
{
    protected $guarded = [];
    public function franchise() { return $this->belongsTo(Franchise::class); }
}

// BankTransaction.php
class BankTransaction extends Model
{
    protected $guarded = [];
    public function bankAccount() { return $this->belongsTo(BankAccount::class); }
    public function franchise() { return $this->belongsTo(Franchise::class); }
}
