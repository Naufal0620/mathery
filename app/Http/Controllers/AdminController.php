<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Course;
use App\Models\Topic;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        // Menghitung statistik ringkas untuk dashboard
        $totalClasses = Course::count();
        // Anda bisa menambahkan count lain jika modelnya sudah ada (Student, Topic, dll)
        
        return view('admin.dashboard', compact('totalClasses'));
    }

    public function classes()
    {
        // Ambil semua data kelas, urutkan dari yang terbaru
        // Kita gunakan 'withCount' untuk menghitung jumlah mahasiswa (students) secara otomatis
        $courses = Course::withCount('students')->latest()->get();

        return view('admin.classes', compact('courses'));
    }

    public function storeClass(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'course_name' => 'required|string|max:100',
            'class_name'  => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        // 2. Generate Kode Unik (Misal: KAL-A-X7Z)
        // Ambil 3 huruf pertama nama matkul + nama kelas + random string
        $prefix = strtoupper(substr($request->course_name, 0, 3));
        $suffix = strtoupper(Str::random(3));
        $generatedCode = $prefix . '-' . str_replace(' ', '', strtoupper($request->class_name)) . '-' . $suffix;

        // 3. Simpan ke Database
        Course::create([
            'teacher_id'  => Auth::id() ?? 1, // Gunakan ID user login, atau 1 jika belum ada auth
            'name'        => $request->course_name . ' - ' . $request->class_name,
            'code'        => $generatedCode,
            'description' => $request->description,
            'is_active'   => true,
        ]);

        return redirect()->route('admin.classes')->with('success', 'Kelas berhasil dibuat!');
    }

    public function updateClass(Request $request, $id)
    {
        // 1. Validasi
        $request->validate([
            'course_name' => 'required|string|max:100',
            'class_name'  => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        // 2. Cari Kelas
        $course = Course::findOrFail($id);

        // 3. Update Data
        // Kita update nama gabungan agar tetap konsisten
        $course->update([
            'name'        => $request->course_name . ' - ' . $request->class_name,
            'description' => $request->description,
            // Note: Kode kelas (code) biasanya tidak diubah agar tidak merusak relasi
        ]);

        // 4. Redirect dengan pesan sukses (akan ditangkap Toastr)
        return redirect()->route('admin.classes')->with('success', 'Data kelas berhasil diperbarui!');
    }

    public function destroyClass($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return redirect()->back()->with('success', 'Kelas berhasil dihapus.');
    }

    public function syllabus(Request $request)
    {
        // Ambil data kelas untuk dropdown filter dan modal tambah
        $courses = Course::where('is_active', true)->get();

        // Query Dasar
        $query = Topic::with('course')->orderBy('class_id', 'asc')->orderBy('meeting_date', 'asc');

        // Cek apakah ada filter kelas yang dipilih
        if ($request->has('filter_class') && $request->filter_class != '') {
            $query->where('class_id', $request->filter_class);
        }

        $topics = $query->get();

        return view('admin.syllabus', compact('topics', 'courses'));
    }

    public function storeTopic(Request $request)
    {
        $request->validate([
            'class_id'     => 'required|exists:classes,id',
            'name'         => 'required|string|max:100',
            'meeting_date' => 'required|date',
            'description'  => 'nullable|string',
        ]);

        // Buat Slug otomatis (Contoh: "pertemuan-1-kalkulus")
        // Menggunakan helper Str::slug()
        $slugRaw = $request->name . '-' . time(); // Tambah time agar unik
        $slug = Str::slug($slugRaw);

        Topic::create([
            'class_id'     => $request->class_id,
            'name'         => $request->name,
            'slug'         => $slug,
            'meeting_date' => $request->meeting_date,
            'description'  => $request->description,
        ]);

        return redirect()->route('admin.syllabus')->with('success', 'Topik pertemuan berhasil ditambahkan!');
    }

    public function updateTopic(Request $request, $id)
    {
        $request->validate([
            'class_id'     => 'required|exists:classes,id',
            'name'         => 'required|string|max:100',
            'meeting_date' => 'required|date',
            'description'  => 'nullable|string',
        ]);

        $topic = Topic::findOrFail($id);
        
        // Update slug jika nama berubah (opsional, tapi disarankan agar konsisten)
        $slug = Str::slug($request->name . '-' . $topic->id);

        $topic->update([
            'class_id'     => $request->class_id,
            'name'         => $request->name,
            'slug'         => $slug,
            'meeting_date' => $request->meeting_date,
            'description'  => $request->description,
        ]);

        return redirect()->route('admin.syllabus')->with('success', 'Topik pertemuan berhasil diperbarui!');
    }

    public function destroyTopic($id)
    {
        $topic = Topic::findOrFail($id);
        $topic->delete();

        return redirect()->back()->with('success', 'Topik berhasil dihapus.');
    }

    public function users(Request $request)
    {
        // Ambil daftar kelas untuk filter
        $courses = Course::where('is_active', true)->get();

        // Query Dasar: Ambil hanya yang role-nya 'student'
        $query = User::where('role', 'student')->orderBy('created_at', 'desc');

        // Filter berdasarkan Kelas (Jika dipilih)
        if ($request->has('filter_class') && $request->filter_class != '') {
            // Cari user yang punya relasi ke kelas ID tersebut
            $query->whereHas('classes', function($q) use ($request) {
                $q->where('classes.id', $request->filter_class);
            });
        }

        // Filter Pencarian Nama/NIM (Opsional, tambahan fitur search sederhana)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $students = $query->get();

        return view('admin.users', compact('students', 'courses'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:100',
            'username'  => 'required|string|max:50|unique:users,username', // NIM harus unik
            'email'     => 'required|email|max:100|unique:users,email',
            'password'  => 'required|string|min:6',
        ]);

        User::create([
            'full_name' => $request->full_name,
            'username'  => $request->username,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => 'student', // Paksa role jadi student
            'avatar'    => 'default.jpg',
        ]);

        return redirect()->route('admin.users')->with('success', 'Mahasiswa berhasil ditambahkan!');
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'full_name' => 'required|string|max:100',
            // Ignore unique validation untuk ID user ini sendiri
            'username'  => 'required|string|max:50|unique:users,username,'.$user->id,
            'email'     => 'required|email|max:100|unique:users,email,'.$user->id,
            'password'  => 'nullable|string|min:6', // Password boleh kosong jika tidak ingin diganti
        ]);

        $data = [
            'full_name' => $request->full_name,
            'username'  => $request->username,
            'email'     => $request->email,
        ];

        // Jika password diisi, update password baru
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users')->with('success', 'Data mahasiswa berhasil diperbarui!');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'Mahasiswa berhasil dihapus.');
    }

    public function classMembers(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        // 1. Ambil Anggota Aktif (Accepted)
        $activeStudents = $course->students()
            ->wherePivot('status', 'accepted') // Filter Pivot
            ->orderBy('full_name', 'asc')
            ->get();

        // 2. Ambil Permintaan Pending (Pending)
        $pendingStudents = $course->students()
            ->wherePivot('status', 'pending') // Filter Pivot
            ->orderBy('created_at', 'asc')
            ->get();

        // 3. Logic untuk Dropdown Tambah Manual (Hanya user yang BELUM ada di tabel pivot sama sekali)
        $existingIds = $course->students()->pluck('users.id')->toArray();
        
        $query = User::where('role', 'student')->whereNotIn('id', $existingIds);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $availableStudents = $query->limit(20)->get();

        return view('admin.class_members', compact('course', 'activeStudents', 'pendingStudents', 'availableStudents'));
    }

    // Fitur Tambah Manual oleh Admin (Langsung Accepted)
    public function storeClassMember(Request $request, $id)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $course = Course::findOrFail($id);

        // Admin nambahin = Langsung Accepted
        $course->students()->attach($request->user_id, ['status' => 'accepted']);

        return redirect()->back()->with('success', 'Mahasiswa berhasil ditambahkan secara manual.');
    }

    // Fitur Approve (Terima Mahasiswa)
    public function approveMember($class_id, $student_id)
    {
        $course = Course::findOrFail($class_id);
        
        // Update status di pivot table jadi 'accepted'
        $course->students()->updateExistingPivot($student_id, ['status' => 'accepted']);

        return redirect()->back()->with('success', 'Permintaan bergabung disetujui!');
    }

    // Fitur Reject (Tolak Mahasiswa) - Sama dengan delete/remove
    public function destroyClassMember($class_id, $student_id)
    {
        $course = Course::findOrFail($class_id);
        $course->students()->detach($student_id);

        return redirect()->back()->with('success', 'Mahasiswa dihapus/ditolak dari kelas.');
    }

    public function activity()
    {
        return view('admin.activity');
    }
}
