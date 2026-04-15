<?php

namespace App\Models;

use App\Traits\SyncToMysql;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    use SyncToMysql;
    use HasFactory;

    protected $fillable = ['name_ar', 'name_en', 'shipping_zone_id'];

    public function zone()
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }
}
