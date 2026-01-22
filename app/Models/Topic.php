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

    // Relasi ke Kelas (Ingat, kita pakai nama model Course untuk tabel classes)
    public function course()
    {
        return $this->belongsTo(Course::class, 'class_id');
    }
}