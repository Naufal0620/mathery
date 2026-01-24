<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'name',
        'slug',
        'description',
        'meeting_date',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'class_id');
    }

    public function materials()
    {
        return $this->hasMany(Material::class, 'topic_id');
    }

    public function groups()
    {
        return $this->hasMany(Group::class, 'topic_id');
    }
}