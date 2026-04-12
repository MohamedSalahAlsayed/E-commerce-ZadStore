<?php

namespace App\Models;

use App\Traits\SyncToMysql;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    use SyncToMysql;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_name',
        'phone',
        'store_category',
        'tax_id',
        'commercial_record',
        'city',
        'address',
        'logo_path',
        'documents_paths',
        'is_active',
    ];

    protected $casts = [
        'documents_paths' => 'array',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
