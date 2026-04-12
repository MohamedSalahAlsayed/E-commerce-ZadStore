<?php

namespace App\Models;

use App\Traits\SyncToMysql;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use SyncToMysql;
    use HasFactory;

    protected $fillable = ['name', 'logo', 'is_active', 'meta_title', 'meta_description', 'meta_keywords'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
