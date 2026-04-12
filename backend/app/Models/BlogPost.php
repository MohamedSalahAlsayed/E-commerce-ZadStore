<?php
namespace App\Models;

use App\Traits\SyncToMysql;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BlogPost extends Model {
    use SyncToMysql;
    use HasFactory;

    protected $fillable = ['title', 'slug', 'summary', 'content', 'cover_image', 'is_published', 'views', 'author_id', 'meta_title', 'meta_description', 'meta_keywords'];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function author() {
        return $this->belongsTo(User::class, 'author_id');
    }
}
