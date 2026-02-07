<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopicComment extends Model
{
    use HasFactory;

    protected $fillable = ['topic_id', 'user_id', 'parent_id', 'body', 'image_path'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(TopicComment::class, 'parent_id');
    }

    public function likes()
    {
        return $this->hasMany(TopicCommentLike::class);
    }

    // Helper untuk cek apakah user tertentu sudah like
    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }
}