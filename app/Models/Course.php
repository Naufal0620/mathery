<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    // Arahkan ke tabel 'classes' karena nama model beda dengan nama tabel
    protected $table = 'classes';

    protected $fillable = [
        'teacher_id',
        'name',
        'code',
        'description',
        'is_active',
    ];

    // Relasi ke User (Dosen)
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
    
    // Relasi ke Mahasiswa (Class Members)
    public function students()
    {
        // Relasi ke User lewat tabel pivot 'class_members'
        // withPivot('id') berguna agar kita bisa akses ID di tabel pivot jika perlu
        return $this->belongsToMany(User::class, 'class_members', 'class_id', 'user_id')
                    ->withPivot('id', 'joined_at')
                    ->withTimestamps();
    }
}