<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Modifikasi tabel 'topics' untuk menyimpan konten materi (Editor.js)
        Schema::table('topics', function (Blueprint $table) {
            // Kita gunakan longText karena output JSON dari Editor.js bisa sangat panjang
            // Menyimpan konten materi pertemuan
            $table->longText('content')->nullable()->after('description'); 
        });

        // 2. Tabel untuk Komentar Diskusi (Support Reply & Gambar)
        Schema::create('topic_comments', function (Blueprint $table) {
            $table->id();
            // Menghubungkan komentar ke topik pertemuan tertentu
            $table->foreignId('topic_id')->constrained('topics')->onDelete('cascade');
            // User yang berkomentar (Dosen/Mahasiswa)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // Fitur Reply: Jika null berarti komentar utama, jika ada isinya berarti balasan
            $table->foreignId('parent_id')->nullable()->constrained('topic_comments')->onDelete('cascade');
            
            $table->text('body')->nullable(); // Isi teks komentar
            $table->string('image_path')->nullable(); // Fitur embed foto
            
            $table->timestamps();
        });

        // 3. Tabel untuk Like Komentar
        Schema::create('topic_comment_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_comment_id')->constrained('topic_comments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Mencegah user like komentar yang sama berkali-kali
            $table->unique(['topic_comment_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topic_comment_likes');
        Schema::dropIfExists('topic_comments');
        
        Schema::table('topics', function (Blueprint $table) {
            $table->dropColumn('content');
        });
    }
};