<?php
namespace App\Models;

use App\Traits\SyncToMysql;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use SyncToMysql;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'comment',
        'is_approved',
        'admin_reply',
        'replied_at',
        'user_reply',
        'user_replied_at',
        'is_admin_read_reply',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'is_admin_read_reply' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
