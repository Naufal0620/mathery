<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopicCommentLike extends Model
{
    use HasFactory;

    protected $fillable = ['topic_comment_id', 'user_id'];
}