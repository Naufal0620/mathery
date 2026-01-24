<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'author_id', // ID User/Dosen pembuat
        'title',
        'slug',
        'description',
        'type',      // 'pdf', 'video', 'article', 'link', 'ppt', 'doc'
        'file_path',
        'is_published',
    ];

    /**
     * Relasi ke Topik Pertemuan
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }

    /**
     * Relasi ke Pembuat (Dosen/User)
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Helper untuk mendapatkan icon FontAwesome berdasarkan tipe file
     * Berguna untuk tampilan di Blade
     */
    public function getIconAttribute()
    {
        return match ($this->type) {
            'pdf' => 'bx bx-file-detail',
            'video' => 'bx bx-video',
            'ppt' => 'bx bx-slideshow',
            'doc' => 'bx bx-file-detail',
            'link' => 'bx bx-link',
            default => 'bx bx-file',
        };
    }
}