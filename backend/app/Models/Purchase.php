<?php

namespace App\Models;

use App\Traits\SyncToMysql;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use SyncToMysql;
    protected $fillable = ['supplier_id', 'invoice_number', 'purchase_date', 'total_amount', 'status', 'notes'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
