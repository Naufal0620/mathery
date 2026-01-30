<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // Jangan lupa import Auth
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AuthController;

// --- ROOT ROUTE (Logika Penentu Arah) ---
// Route ini DILUAR middleware 'guest' agar bisa diakses oleh siapa saja
Route::get('/', function () {
    // 1. Jika User Sudah Login
    if (Auth::check()) {
        $user = Auth::user();
        
        // Cek Role dan arahkan ke dashboard yang sesuai
        if ($user->role === 'admin' || $user->role === 'teacher') {
            return redirect()->route('admin.dashboard');
        } else if ($user->role === 'student') {
            return redirect()->route('student.dashboard');
        }
        
        // Fallback jika role tidak dikenali
        return redirect()->route('login');
    }

    // 2. Jika Belum Login (Tamu)
    return redirect()->route('login');
});

// --- GUEST ROUTES (Hanya untuk yang BELUM Login) ---
Route::middleware('guest')->group(function () {
    // Hapus Route::get('/') dari sini
    
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// --- AUTH ROUTES (Harus Sudah Login) ---
Route::middleware('auth')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // --- ADMIN ROUTES ---
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::controller(AdminController::class)->group(function () {
            Route::get('/dashboard', 'index')->name('dashboard');
            
            Route::get('/classes', 'classes')->name('classes');
            Route::post('/classes', 'storeClass')->name('classes.store');
            Route::put('/classes/{id}', 'updateClass')->name('classes.update');
            Route::delete('/classes/{id}', 'destroyClass')->name('classes.destroy');

            Route::get('/classes/{id}/members', 'classMembers')->name('classes.members');
            Route::post('/classes/{id}/members', 'storeClassMember')->name('classes.members.store');
            Route::delete('/classes/{id}/members/{student_id}', 'destroyClassMember')->name('classes.members.destroy');
            Route::put('/classes/{id}/members/{student_id}/approve', 'approveMember')->name('classes.members.approve');
            
            Route::get('/syllabus', 'syllabus')->name('syllabus');
            Route::post('/syllabus', 'storeTopic')->name('syllabus.store');
            Route::put('/syllabus/{id}', 'updateTopic')->name('syllabus.update');
            Route::delete('/syllabus/{id}', 'destroyTopic')->name('syllabus.destroy');

            Route::get('/users', 'users')->name('users');
            Route::post('/users', 'storeUser')->name('users.store');
            Route::put('/users/{id}', 'updateUser')->name('users.update');
            Route::delete('/users/{id}', 'destroyUser')->name('users.destroy');

            // Route Management Group
            Route::get('/classes/{id}/groups', 'classGroups')->name('classes.groups');
            Route::post('/groups', 'storeGroup')->name('groups.store');
            Route::delete('/groups/{id}', 'destroyGroup')->name('groups.destroy');
            
            // Approval Keluar Kelompok
            Route::put('/classes/{class_id}/members/{student_id}/leave-approve', 'approveGroupLeave')->name('groups.leave.approve');
            Route::put('/classes/{class_id}/members/{student_id}/leave-reject', 'rejectGroupLeave')->name('groups.leave.reject');

            Route::get('/projects-gallery', [AdminController::class, 'allProjects'])->name('projects.index');
            Route::get('/classes/{id}/projects', 'classProjects')->name('classes.projects');
            Route::put('/projects/{id}/toggle-featured', 'toggleFeaturedProject')->name('projects.toggle-featured');
            Route::delete('/projects/{id}', 'destroyProject')->name('projects.destroy');
            
            Route::get('/activity', 'activity')->name('activity');
        });
    });

    // --- STUDENT ROUTES ---
    Route::prefix('student')->name('student.')->group(function () {
        Route::controller(StudentController::class)->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard');

            // --- TAMBAHAN BARU ---
            Route::get('/my-classes', 'myClasses')->name('myClasses'); // Halaman "Kelas Saya"
            Route::get('/class/{id}', 'showClass')->name('class.show'); // Halaman "Detail Kelas"
            // ---------------------

            // Route Join (Update)
            Route::post('/join-class', 'joinClass')->name('joinClass');

            // Route AJAX Search (Baru)
            Route::get('/ajax/search-classes', 'searchClasses')->name('searchClasses');

            Route::get('/class/{id}', 'showClass')->name('class.show'); // Halaman detail kelas
            Route::post('/group/{groupId}/join', 'joinGroup')->name('group.join');
            Route::post('/class/{classId}/group/leave', 'requestLeaveGroup')->name('group.leave');

            Route::get('/projects-gallery', [StudentController::class, 'allProjects'])->name('projects.index');
            Route::get('/class/{classId}/project/create', 'createProject')->name('project.create');
            Route::post('/class/{classId}/project/store', 'storeProject')->name('project.store');
        });
    });
});