<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['class_id', 'topic_id', 'name', 'max_slots'];

    // Relasi ke Kelas
    public function course()
    {
        return $this->belongsTo(Course::class, 'class_id');
    }

    // Relasi ke Topik (Pertemuan)
    public function topic()
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }

    // Relasi ke Mahasiswa (Anggota Kelompok)
    // Kita ambil user yang ada di class_members dengan group_id ini
    public function students()
    {
        return $this->belongsToMany(User::class, 'class_members', 'group_id', 'user_id')
            ->where('class_members.class_id', $this->class_id) // Pastikan konteks kelasnya sama
            ->withPivot('status', 'is_requesting_group_leave');
    }
    
    // Helper untuk cek penuh atau tidak
    public function isFull()
    {
        return $this->students()->count() >= $this->max_slots;
    }
}