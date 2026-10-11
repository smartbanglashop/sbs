<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAnalytics extends Model
{
    use HasFactory;
      protected $fillable = [
        'product_id',
        'event',
        'value',
        'ip_address',
        'user_agent'
    ];
        public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
