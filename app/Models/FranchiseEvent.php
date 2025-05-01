<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FranchiseEvent extends Model
{
    protected $guarded = ['id'];
    public function eventItems() {
        return $this->hasMany(FranchiseEventItem::class);
    }
}
