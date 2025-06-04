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
     * Define custom primary key
     *
     * @var string
     */
    protected $primaryKey = 'user_id'; // Specify the custom primary key

    /**
     * Indicate that the primary key is auto-incrementing
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Set primary key type
     *
     * @var string
     */
    protected $keyType = 'int';

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
        'franchisee_id',
        'created_date',
        'clearance',
        'security',
        'stripe_account_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'created_date' => 'date',
    ];

    /**
     * Define relationship with Franchisee
     */
    public function franchisee()
    {
        return $this->belongsTo(Franchisee::class, 'franchisee_id', 'franchisee_id');
    }

    public function franchisees()
    {
        return $this->belongsToMany(Franchisee::class, 'user_franchisees', 'user_id', 'franchisee_id');
    }




}
