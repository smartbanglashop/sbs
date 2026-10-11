<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacebookSetting extends Model
{
    use HasFactory;
        protected $fillable = [
        'pixel_id',
        'access_token',
        'test_event_code',
        'browser_tracking',
        'server_tracking',
        'page_view',
        'view_content',
        'add_to_cart',
        'initiate_checkout',
        'purchase',
        'status'
    ];
}
