<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    use HasFactory;

    protected $table = 'business_settings'; // আপনার ডাটাবেজের টেবিল নাম
    protected $fillable = ['type', 'value'];
    public $timestamps = false; // যদি created_at/updated_at না থাকে
}