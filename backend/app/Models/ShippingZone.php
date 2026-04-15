<?php
namespace App\Models;

use App\Traits\SyncToMysql;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    use SyncToMysql;
    use HasFactory;

    protected $fillable = ['name', 'is_active'];

    public function governorates()
    {
        return $this->hasMany(Governorate::class);
    }

    public function methods()
    {
        return $this->hasMany(ShippingMethod::class);
    }
}
