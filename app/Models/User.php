<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username', // Kita asumsikan ini NIM
        'email',
        'password',
        'full_name',
        'role',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relasi: Mahasiswa bisa memiliki banyak kelas (lewat tabel pivot class_members)\
    public function classes()
    {
        return $this->belongsToMany(Course::class, 'class_members', 'user_id', 'class_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}