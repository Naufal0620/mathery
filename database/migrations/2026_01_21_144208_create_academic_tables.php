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
        // 1. Tabel Classes (Induk dari semuanya)
        // Tidak ada dependensi ke tabel akademik lain, hanya ke users
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->string('name', 150);
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Tabel Topics (Harus dibuat sebelum Groups & Materials)
        // Dependensi: Classes
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('slug', 150)->unique(); // Slug pertemuan
            $table->text('description')->nullable();
            $table->date('meeting_date')->nullable();
            $table->timestamps();
        });

        // 3. Tabel Groups (Harus dibuat sebelum Class Members)
        // Dependensi: Classes, Topics
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            // Jika topik dihapus, set null agar grup tidak hilang
            $table->foreignId('topic_id')->nullable()->constrained('topics')->onDelete('set null'); 
            $table->string('name');
            $table->integer('max_slots')->default(5);
            $table->timestamps();
        });

        // 4. Tabel Class Members (Pivot)
        // Dependensi: Classes, Users, Groups
        Schema::create('class_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Relasi ke Groups (Nullable karena awal masuk belum punya kelompok)
            $table->foreignId('group_id')->nullable()->constrained('groups')->onDelete('set null');
            
            // Kolom Status & Flagging
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->boolean('is_requesting_group_leave')->default(false);
            
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();
            
            // Mencegah duplikasi user di kelas yang sama
            $table->unique(['class_id', 'user_id']); 
        });

        // 5. Tabel Materials
        // Dependensi: Topics, Users
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('topics')->onDelete('cascade');
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->string('title', 200);
            $table->string('slug', 200)->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['pdf', 'video', 'article', 'link', 'ppt', 'doc'])->default('article');
            $table->string('file_path')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // 6. Tabel Student Projects
        // Dependensi: Users, Classes
        Schema::create('student_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->string('title', 200);
            $table->string('slug', 200)->unique();
            $table->text('description')->nullable();
            $table->string('thumbnail')->default('project_default.jpg');
            $table->string('project_url')->nullable();
            $table->string('repo_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus dengan urutan TERBALIK untuk menghindari error foreign key
        Schema::dropIfExists('student_projects');
        Schema::dropIfExists('materials');
        Schema::dropIfExists('class_members'); // Hapus member dulu sebelum group
        Schema::dropIfExists('groups');        // Hapus group dulu sebelum topic/class
        Schema::dropIfExists('topics');
        Schema::dropIfExists('classes');
    }
};