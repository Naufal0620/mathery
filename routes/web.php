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
        
        // Dashboard Utama
        Route::get('/dashboard', 'index')->name('dashboard');
        
        // Akademik
        Route::get('/classes', 'classes')->name('classes');     // Manajemen Kelas
        Route::get('/syllabus', 'syllabus')->name('syllabus');   // Jadwal & Topik
        Route::get('/users', 'users')->name('users');           // Data Mahasiswa
        
        // Sistem
        Route::get('/activity', 'activity')->name('activity');   // Riwayat Aktivitas
        
        // CRUD Operations (Contoh untuk form submit nanti)
        Route::post('/classes', 'storeClass')->name('classes.store');
    });

});

// Catatan: Jika Anda sudah menggunakan Auth (Login), tambahkan ->middleware(['auth']) 
// pada group di atas agar halaman admin terlindungi.