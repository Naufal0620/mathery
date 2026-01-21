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
        // 1. Tabel Classes (Mata Kuliah / Kelas)
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            // Relasi ke Users (Dosen) dengan onDelete Cascade
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->string('name', 150);
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Tabel Class Members (Mahasiswa yang join kelas)
        Schema::create('class_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('joined_at')->useCurrent();
            
            // Mencegah user join kelas yang sama dua kali
            $table->unique(['class_id', 'user_id']); 
        });

        // 3. Tabel Topics (Pertemuan/Topik)
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('slug', 150)->unique();
            $table->text('description')->nullable();
            $table->date('meeting_date')->nullable();
            $table->timestamps();
        });

        // 4. Tabel Materials (Materi Pembelajaran)
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('topics')->onDelete('cascade');
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->string('title', 200);
            $table->string('slug', 200)->unique();
            $table->text('description')->nullable();
            // Tipe materi
            $table->enum('type', ['pdf', 'video', 'article', 'link', 'ppt', 'doc'])->default('article');
            $table->string('file_path')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // 5. Tabel Student Projects (Portofolio Projek)
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
        // Drop urut dari anak ke induk untuk menghindari foreign key constraint error
        Schema::dropIfExists('student_projects');
        Schema::dropIfExists('materials');
        Schema::dropIfExists('topics');
        Schema::dropIfExists('class_members');
        Schema::dropIfExists('classes');
    }
};