<?php
namespace App\Models;

use App\Traits\SyncToMysql;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model {
    use SyncToMysql;
    protected $fillable = ['user_id', 'title', 'description', 'icon', 'color', 'type', 'is_read'];

    protected $casts = [
        'is_read' => 'boolean'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
