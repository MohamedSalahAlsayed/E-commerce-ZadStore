<?php

namespace App\Models;

use App\Traits\SyncToMysql;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use SyncToMysql;
    protected $fillable = ['name', 'phone', 'email', 'address'];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
