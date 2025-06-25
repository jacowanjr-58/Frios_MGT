<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledSocialPost extends Model
{
    protected $fillable = [
        'franchise_id', 'meta_page_id', 'access_token', 'message', 'scheduled_for', 'posted'
    ];
}
