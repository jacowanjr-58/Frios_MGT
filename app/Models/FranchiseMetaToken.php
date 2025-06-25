<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FranchiseMetaToken extends Model
{
    protected $fillable = [
        'franchise_id', 'meta_page_id', 'meta_access_token', 'token_expires_at'
    ];
}
