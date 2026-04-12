<?php
namespace App\Models;

use App\Traits\SyncToMysql;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use SyncToMysql;
    use HasFactory;

    protected $fillable = ['name', 'email', 'subject', 'message', 'is_read', 'user_id', 'reply_text', 'replied_at'];

    protected $casts = [
        'is_read' => 'boolean',
        'replied_at' => 'datetime',
    ];
    
    public function user() {
        return $this->belongsTo(User::class);
    }
}
