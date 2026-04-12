<?php

namespace App\Models;

use App\Traits\SyncToMysql;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use SyncToMysql;
    protected $fillable = ['purchase_id', 'product_id', 'quantity', 'unit_price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
