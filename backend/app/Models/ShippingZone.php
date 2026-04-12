<?php
namespace App\Models;

use App\Traits\SyncToMysql;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    use SyncToMysql;
    use HasFactory;

    protected $fillable = ['name', 'fee', 'is_active', 'delivery_time', 'cod_fee'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
