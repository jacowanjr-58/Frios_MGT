<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Franchise extends Model
{
    use HasFactory;

    protected $table = 'franchises';
    protected $primaryKey = 'id'; // Ensure this matches database

    protected $fillable = [
        'user_id',
        'business_name',
        'contact_number',
        'frios_territory_name',
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
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Define many-to-many relationship with User through user_franchises
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_franchises', 'franchise_id', 'user_id');
    }

    /**
     * Define many-to-many relationship with User through user_franchises
     */
    public function franchiseUsers()
    {
        return $this->belongsToMany(User::class, 'user_franchises', 'franchise_id', 'user_id');
    }

    public function inventoryMasters()
    {
        return $this->hasMany(InventoryMaster::class, 'franchise_id');
    }

    // Parent-child franchise relationships
    public function parentFranchise()
    {
        return $this->belongsTo(Franchise::class, 'parent_franchise_id');
    }

    public function childFranchises()
    {
        return $this->hasMany(Franchise::class, 'parent_franchise_id');
    }

    // Customers relationship
    public function customers()
    {
        return $this->hasMany(Customer::class, 'franchise_id');
    }

    // Orders relationship
    public function orders()
    {
        return $this->hasMany(FgpOrder::class, 'franchise_id');
    }

    // Items relationship
    public function items()
    {
        return $this->hasMany(FgpItem::class, 'franchise_id');
    }

    // Categories relationship
    public function categories()
    {
        return $this->hasMany(FgpCategory::class, 'franchise_id');
    }

    // Events relationship
    public function events()
    {
        return $this->hasMany(Event::class, 'franchise_id');
    }

    // Sales relationship
    public function sales()
    {
        return $this->hasMany(Sale::class, 'franchise_id');
    }
} 