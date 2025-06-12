<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Franchisee extends Model
{
    use HasFactory;

    protected $primaryKey = 'franchisee_id';
    public $incrementing = true;
    protected $fillable = [
        'user_id',
        'business_name',
        'address1',
        'address2',
        'city',
        'zip_code',
        'state',
        'location_zip',
        'ACH_data_API',
        'pos_service_API'
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // public function users()
    // {
    //     return $this->hasMany(User::class, 'franchisee_id');
    // }
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_franchisees', 'franchisee_id', 'user_id');
    }
    


    /**
     * Define many-to-many relationship with User through user_franchisees
     */
    public function franchiseUsers()
    {
        return $this->belongsToMany(User::class, 'user_franchisees', 'franchisee_id', 'user_id');
    }

    public function inventoryMasters()
    {
        return $this->hasMany(InventoryMaster::class, 'franchisee_id', 'franchisee_id');
    }

}
