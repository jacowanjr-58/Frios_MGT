<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'locations';

    protected $primaryKey = 'locations_ID';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'franchisee_id',
    ];
}
