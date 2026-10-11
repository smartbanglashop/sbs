<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;
    public function shipping_charge()
    {
        return $this->hasOne(ShippingCharge::class, 'id', 'area');
    }
        public function district()
    {
        return $this->belongsTo(ShippingDistrict::class,'shipping_district_id');
    }
     public function thana()
    {
        return $this->belongsTo(Thana::class,'thana_id');
    }
}
