<?php

namespace App\Models;

use App\Traits\SyncToMysql;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use SyncToMysql;
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'name',
        'price',
        'quantity',
        'image',
        'purchase_price'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
