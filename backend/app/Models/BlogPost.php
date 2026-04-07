<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BlogPost extends Model {
    use HasFactory;

    protected $fillable = ['title', 'slug', 'summary', 'content', 'cover_image', 'is_published', 'views', 'author_id'];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function author() {
        return $this->belongsTo(User::class, 'author_id');
    }
}
