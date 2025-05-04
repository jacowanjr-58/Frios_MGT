<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseSubCategory extends Model
{
    protected $guarded = [];

    public function category(){
        return $this->belongsTo(ExpenseCategory::class);
    }
}
