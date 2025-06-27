<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FgpCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type'
    ];

    // Many-to-many relationship with FgpItem
    public function items()
    {
        return $this->hasMany(FgpItem::class);
    }

    public function getFormattedCreatedAtAttribute()
    {
        return Carbon::parse($this->created_at)->format('M d, Y');
    }
}
