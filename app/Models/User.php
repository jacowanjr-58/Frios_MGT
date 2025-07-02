<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone_number',
        'franchise_id',
        'clearance',
        'security',
        'stripe_account_id',
        'ein_ssn_hash',
        'contract_document_path',
        'date_joined',
        'stripe_account_id',
        'stripe_onboarding_complete',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        // 'ein_ssn_hash', // Hide the hashed EIN/SSN from serialization
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'date_joined' => 'date',
    ];

    

    /**
     * Define relationship with Franchise
     */
    public function franchise()
    {
        return $this->belongsTo(Franchise::class, 'franchise_id');
    }

    public function franchises()
    {
        return $this->belongsToMany(Franchise::class, 'user_franchises', 'user_id', 'franchise_id');
    }

    /**
     * Set the EIN/SSN hash
     */
    public function setEinSsnAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['ein_ssn_hash'] = encrypt($value); // Correct: encrypt, not decrypt
        }
    }


    /**
     * Check if the provided EIN/SSN matches the stored hash
     */
    public function getEinSsnAttribute()
    {
        if (!empty($this->attributes['ein_ssn_hash'])) {
            return decrypt($this->attributes['ein_ssn_hash']);
        }

        return null;
    }
    public function verifyEinSsn($einSsn)
    {
        if (empty($this->ein_ssn_hash)) {
            return false;
        }

        return decrypt($this->ein_ssn_hash) === $einSsn;
    }

    /**
     * Get the date_joined formatted as mm-dd-yyyy
     */
    public function getDateJoinedAttribute($value)
    {
        return $value ? date('m-d-Y', strtotime($value)) : null;
    }

    /**
     * Get the created_at formatted as mm-dd-yyyy
     */
    public function getCreatedAtAttribute($value)
    {
        return $value ? date('m-d-Y', strtotime($value)) : null;
    }

}
