<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'group_id',
        'title',
        'slug',
        'description',
        'thumbnail',
        'project_url',
        'repo_url',
        'is_featured',
    ];

    // Relasi ke Mahasiswa Pembuat
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Relasi ke Kelas
    public function course()
    {
        return $this->belongsTo(Course::class, 'class_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}