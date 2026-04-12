<?php
namespace App\Models;

use App\Traits\SyncToMysql;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    use SyncToMysql;
    use HasFactory;

    protected $fillable = ['key', 'value'];
}
