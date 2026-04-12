<?php
namespace App\Models;

use App\Traits\SyncToMysql;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use SyncToMysql;
    use HasFactory;

    protected $fillable = ['code', 'type', 'value', 'min_order_amount', 'expires_at', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];
}
