<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Topic;
use App\Models\Course;
use App\Models\Group;
use App\Models\StudentProject;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // 1. Kelas Aktif (Bisa masuk)
        $myClasses = $user->classes()
            ->wherePivot('status', 'accepted')
            ->with('teacher')
            ->get();

        // 2. Kelas Pending (Sedang menunggu persetujuan)
        $pendingClasses = $user->classes()
            ->wherePivot('status', 'pending')
            ->with('teacher')
            ->get();
            
        // 3. Jadwal (Hanya dari kelas yang sudah accepted)
        $acceptedClassIds = $myClasses->pluck('id');
        $upcomingSchedules = Topic::whereIn('class_id', $acceptedClassIds)
            ->whereDate('meeting_date', '>=', now())
            ->orderBy('meeting_date', 'asc')
            ->with('course')
            ->take(5)
            ->get();

        return view('student.dashboard', compact('myClasses', 'pendingClasses', 'upcomingSchedules'));
    }

    public function searchClasses(Request $request)
    {
        $search = $request->get('search');
        $user = Auth::user();

        // Ambil ID kelas yang SUDAH diambil (active/pending) agar tidak muncul lagi
        $joinedClassIds = $user->classes()->pluck('classes.id')->toArray();

        $classes = collect();

        if (!empty($search)) {
            $classes = Course::with('teacher')
                ->where('name', 'like', "%{$search}%") // Cari berdasarkan nama
                ->whereNotIn('id', $joinedClassIds) // Kecualikan yang sudah join
                ->where('is_active', true)
                ->limit(10) // Batasi hasil
                ->get();
        }

        return response()->json($classes);
    }

    public function joinClass(Request $request)
    {
        // Ubah validasi dari 'code' menjadi 'class_id'
        $request->validate([
            'class_id' => 'required|exists:classes,id',
        ]);

        $user = Auth::user();
        $classId = $request->class_id;

        // Cek double (Guard)
        if ($user->classes()->where('classes.id', $classId)->exists()) {
            return redirect()->back()->with('error', 'Anda sudah tergabung di kelas ini.');
        }

        // Attach dengan status PENDING
        $user->classes()->attach($classId, ['status' => 'pending']);

        return redirect()->back()->with('success', 'Permintaan bergabung dikirim! Silakan tunggu persetujuan dosen.');
    }

    public function myClasses()
    {
        $user = Auth::user();

        // Ambil semua kelas yang statusnya 'accepted'
        $myClasses = $user->classes()
            ->wherePivot('status', 'accepted')
            ->with('teacher') // Load data dosen
            ->orderByPivot('created_at', 'desc')
            ->get();

        return view('student.my_classes', compact('myClasses'));
    }

    public function showClass($id)
    {
        $user = Auth::user();

        // 1. Cari kelasnya & pastikan user terdaftar (accepted)
        // Menggunakan 'whereHas' atau filter di collection untuk keamanan akses
        // $course = $user->classes()
        //     ->where('classes.id', $id)
        //     ->wherePivot('status', 'accepted')
        //     ->with([
        //         'teacher',
        //         'topics' => function($q) {
        //             $q->orderBy('meeting_date', 'asc');
        //         },
        //         'groups.students' // Load groups dan anggotanya
        //     ])
        //     ->first();

        $course = $user->classes()
            ->where('classes.id', $id)
            ->wherePivot('status', 'accepted')
            ->with([
                'teacher', 
                'topics.materials', // <--- TAMBAHKAN '.materials' DI SINI
                'groups.students'
            ])
            ->first();

        if (!$course) {
            return redirect()->route('student.dashboard')->with('error', 'Kelas tidak ditemukan atau Anda belum terdaftar.');
        }

        // 2. Ambil data membership user di kelas ini (untuk cek group_id user)
        $membership = $course->pivot; 

        return view('student.class_detail', compact('course', 'membership'));
    }

    public function joinGroup(Request $request, $groupId)
    {
        $user = Auth::user();
        $group = Group::findOrFail($groupId);

        // FIX: Tambahkan ->withPivot('status') di sini juga untuk keamanan ganda
        $membership = $user->classes()
            ->where('classes.id', $group->class_id)
            ->withPivot('status') 
            ->first();

        // Debugging (jika masih error, cek nilai ini)
        // dd($membership->pivot->status); 

        if (!$membership || $membership->pivot->status !== 'accepted') {
            return redirect()->back()->with('error', 'Akses ditolak. Anda belum resmi diterima di kelas ini.');
        }

        // Validasi: Apakah user sudah punya kelompok di kelas ini?
        if ($membership->pivot->group_id) {
            return redirect()->back()->with('error', 'Anda sudah memiliki kelompok di kelas ini.');
        }

        // Validasi: Apakah kelompok penuh?
        if ($group->isFull()) {
            return redirect()->back()->with('error', 'Kelompok penuh.');
        }

        // Update Pivot: Masukkan user ke kelompok
        $user->classes()->updateExistingPivot($group->class_id, [
            'group_id' => $group->id
        ]);

        return redirect()->back()->with('success', 'Berhasil bergabung dengan ' . $group->name);
    }

    public function requestLeaveGroup($classId)
    {
        $user = Auth::user();

        // Update Pivot: Set flag request leave
        $user->classes()->updateExistingPivot($classId, [
            'is_requesting_group_leave' => true
        ]);

        return redirect()->back()->with('success', 'Permintaan keluar kelompok dikirim ke dosen.');
    }

    public function allProjects(Request $request)
    {
        // Ambil kelas yang diikuti mahasiswa (untuk opsi upload nanti jika diperlukan)
        $myClasses = Auth::user()->classes()->wherePivot('status', 'accepted')->get();
        
        // Ambil semua kelas untuk filter
        $allClasses = \App\Models\Course::all();

        // Query Projek Global
        $query = StudentProject::with(['student', 'course', 'group']);

        if ($request->has('class_id') && $request->class_id != '') {
            $query->where('class_id', $request->class_id);
        }

        $projects = $query->latest()->paginate(12);

        return view('student.projects.index', compact('projects', 'allClasses', 'myClasses'));
    }

    public function createProject($classId)
    {
        $user = Auth::user();
        $course = Course::findOrFail($classId);

        // Validasi akses (User harus anggota kelas)
        // ... (gunakan logika validasi yang sudah ada sebelumnya) ...

        return view('student.project_create', compact('course'));
    }

    // UPDATE fungsi storeProject yang lama
    public function storeProject(Request $request, $classId)
    {
        $user = Auth::user();
        
        // 1. Cek Membership & Kelompok
        $membership = $user->classes()->where('classes.id', $classId)->withPivot('group_id')->first();

        if (!$membership || !$membership->pivot->group_id) {
            return back()->with('error', 'Anda harus bergabung ke dalam sebuah KELOMPOK terlebih dahulu sebelum upload projek.');
        }

        // ... (Validasi & Upload Gambar sama seperti sebelumnya) ...

        StudentProject::create([
            'student_id'  => $user->id,
            'class_id'    => $classId,
            'group_id'    => $membership->pivot->group_id, // <--- OTOMATIS ISI GROUP ID
            'title'       => $request->title,
            'slug'        => \Illuminate\Support\Str::slug($request->title) . '-' . time(),
            'description' => $request->description,
            'thumbnail'   => $thumbnailPath ?? 'project_default.jpg', // Pastikan variabel path didefinisikan
            'project_url' => $request->project_url,
            'repo_url'    => $request->repo_url,
        ]);

        return redirect()->route('student.projects.index')->with('success', 'Projek kelompok berhasil dipublikasikan!');
    }
}