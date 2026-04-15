<?php

namespace App\Models;

use App\Traits\SyncToMysql;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use SyncToMysql;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'customer_name',
        'phone',
        'address',
        'governorate_id',
        'shipping_method_id',
        'status',
        'payment_status',
        'payment_method',
        'subtotal',
        'shipping_fee',
        'total',
        'total_weight',
        'is_urgent',
        'return_request_status',
        'tracking_number',
        'shipping_provider',
        'shipping_metadata',
    ];

    protected $casts = [
        'shipping_metadata' => 'array',
        'is_urgent' => 'boolean',
    ];

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
