<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini adalah tempat Anda mendaftarkan route untuk aplikasi Anda.
|
*/

// Redirect halaman utama ke dashboard admin (Opsional)
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Group Route Admin
// Prefix: URL akan diawali dengan /admin (contoh: /admin/dashboard)
// Name: Nama route diawali dengan admin. (contoh: admin.dashboard)
Route::prefix('admin')->name('admin.')->group(function () {
    
    // Menggunakan Controller Group agar tidak perlu menulis AdminController::class berulang kali
    Route::controller(AdminController::class)->group(function () {
        
        // DASHBOARD UTAMA
        Route::get('/dashboard', 'index')->name('dashboard');
        
        // MANAJEMEN KELAS
        Route::get('/classes', 'classes')->name('classes');
        Route::post('/classes', 'storeClass')->name('classes.store'); // Tambah Kelas
        Route::put('/classes/{id}', 'updateClass')->name('classes.update'); // Update Kelas
        Route::delete('/classes/{id}', 'destroyClass')->name('classes.destroy'); // Hapus Kelas
        
        // MANAJEMEN TOPIK
        Route::get('/syllabus', 'syllabus')->name('syllabus');
        Route::post('/syllabus', 'storeTopic')->name('syllabus.store'); // Tambah Topik
        Route::put('/syllabus/{id}', 'updateTopic')->name('syllabus.update'); // Update Topik
        Route::delete('/syllabus/{id}', 'destroyTopic')->name('syllabus.destroy'); // Hapus Topik
        
        // MANAJEMEN MAHASISWA
        Route::get('/users', 'users')->name('users');
        Route::post('/users', 'storeUser')->name('users.store');
        Route::put('/users/{id}', 'updateUser')->name('users.update');
        Route::delete('/users/{id}', 'destroyUser')->name('users.destroy');

        // MANAJEMEN ANGGOTA KELAS (MEMBERS)
        Route::get('/classes/{id}/members', 'classMembers')->name('classes.members'); // Halaman Detail
        Route::post('/classes/{id}/members', 'storeClassMember')->name('classes.members.store'); // Tambah Anggota
        Route::delete('/classes/{id}/members/{student_id}', 'destroyClassMember')->name('classes.members.destroy'); // Hapus Anggota

        // MANAJEMEN RIWAYAT AKTIVITAS
        Route::get('/activity', 'activity')->name('activity');   // Riwayat Aktivitas
    });

});

// Catatan: Jika Anda sudah menggunakan Auth (Login), tambahkan ->middleware(['auth']) 
// pada group di atas agar halaman admin terlindungi.