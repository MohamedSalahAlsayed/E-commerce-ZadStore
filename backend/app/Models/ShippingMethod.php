<?php

namespace App\Models;

use App\Traits\SyncToMysql;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    use SyncToMysql;
    use HasFactory;

    protected $fillable = [
        'shipping_zone_id', 
        'name_ar', 
        'name_en', 
        'fee', 
        'is_weight_aware',
        'base_weight',
        'extra_weight_fee',
        'delivery_time', 
        'is_active', 
        'driver', 
        'provider_code'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'fee' => 'decimal:2',
    ];

    public function zone()
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }
}
