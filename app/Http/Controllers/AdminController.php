<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Course;
use App\Models\Topic;
use App\Models\User;
use App\Models\Group;
use App\Models\Material;
use App\Models\StudentProject;

class AdminController extends Controller
{
    public function index()
    {
        // 1. Mengambil Statistik Real-time
        $stats = [
            'students' => User::where('role', 'student')->count(),
            'projects' => StudentProject::count(),
            'materials' => Material::count(),
            'topics'   => Topic::count(),
        ];

        // 2. Mengambil "Aktivitas Terbaru" (Gabungan Materi & Projek)
        // Kita ambil 5 Materi terbaru
        $latestMaterials = Material::with(['author', 'topic.course'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'material',
                    'actor' => $item->author->full_name ?? 'Admin',
                    'action' => 'Mengupload Materi: ' . Str::limit($item->title, 20),
                    'class' => $item->topic->course->name ?? '-',
                    'time' => $item->created_at,
                    'icon' => 'bx-upload',
                    'color' => 'text-blue-500',
                ];
            });

        // Kita ambil 5 Projek terbaru
        $latestProjects = StudentProject::with(['student', 'course'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'project',
                    'actor' => $item->student->full_name ?? 'Mahasiswa',
                    'action' => 'Mengirim Projek: ' . Str::limit($item->title, 20),
                    'class' => $item->course->name ?? '-',
                    'time' => $item->created_at,
                    'icon' => 'bx-folder-plus',
                    'color' => 'text-purple-500',
                ];
            });

        // Gabungkan, urutkan berdasarkan waktu terbaru, dan ambil 5 teratas
        $recentActivities = $latestMaterials->merge($latestProjects)
            ->sortByDesc('time')
            ->take(6);

        return view('admin.dashboard', compact('stats', 'recentActivities'));
    }

    public function classes()
    {
        // REVISI: Tambahkan withCount 'topics' juga agar kartu lebih informatif
        $courses = Course::withCount(['students', 'topics'])
            ->latest()
            ->get();

        return view('admin.classes', compact('courses'));
    }

    public function storeClass(Request $request)
    {
        $request->validate([
            'course_name' => 'required|string|max:100',
            'class_name'  => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        // Generate Kode Unik: 3 huruf matkul + nama kelas + 3 random string
        $prefix = strtoupper(substr($request->course_name, 0, 3));
        $suffix = strtoupper(Str::random(3));
        // Membersihkan spasi di nama kelas agar kode lebih rapi
        $cleanClassName = str_replace(' ', '', strtoupper($request->class_name));
        
        $generatedCode = "{$prefix}-{$cleanClassName}-{$suffix}";

        Course::create([
            'teacher_id'  => Auth::id() ?? 1,
            'name'        => $request->course_name . ' - ' . $request->class_name,
            'code'        => $generatedCode,
            'description' => $request->description,
            'is_active'   => true,
        ]);

        return redirect()->route('admin.classes')->with('success', 'Kelas berhasil dibuat!');
    }

    public function updateClass(Request $request, $id)
    {
        $request->validate([
            'course_name' => 'required|string|max:100',
            'class_name'  => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        $course = Course::findOrFail($id);

        $course->update([
            'name'        => $request->course_name . ' - ' . $request->class_name,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.classes')->with('success', 'Informasi kelas diperbarui!');
    }

    public function classMembers(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        // Ambil siswa aktif
        $activeStudents = $course->students()
            ->wherePivot('status', 'accepted')
            ->orderBy('full_name', 'asc')
            ->get();

        // Ambil siswa pending
        $pendingStudents = $course->students()
            ->wherePivot('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        // Logika untuk dropdown "Tambah Anggota Manual"
        // Mengambil student yang belum masuk kelas ini
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

    public function storeClassMember(Request $request, $id)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $course = Course::findOrFail($id);

        // Menambahkan siswa dengan status langsung diterima
        $course->students()->attach($request->user_id, ['status' => 'accepted']);

        return redirect()->back()->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function approveMember($class_id, $student_id)
    {
        $course = Course::findOrFail($class_id);
        
        $course->students()->updateExistingPivot($student_id, ['status' => 'accepted']);

        return redirect()->back()->with('success', 'Mahasiswa resmi bergabung di kelas.');
    }

    public function destroyClassMember($class_id, $student_id)
    {
        $course = Course::findOrFail($class_id);
        $course->students()->detach($student_id);

        return redirect()->back()->with('success', 'Mahasiswa dihapus dari kelas.');
    }

    public function classGroups($id)
    {
        $course = Course::findOrFail($id);
        
        // 1. Ambil Groups dengan Eager Loading
        $groups = $course->groups()
            ->with(['topic', 'students'])
            ->get(); // Tanpa filter topik spesifik (tampilkan semua)

        // 2. Ambil Topik untuk dropdown 'Buat Kelompok'
        $topics = $course->topics()->orderBy('meeting_date', 'asc')->get();

        // 3. Ambil Mahasiswa yang BELUM punya kelompok di kelas ini
        // Asumsi: Di tabel pivot 'class_members', kolom 'group_id' bernilai NULL
        $ungroupedStudents = $course->students()
            ->wherePivot('status', 'accepted')
            ->wherePivot('group_id', null)
            ->orderBy('full_name', 'asc')
            ->get();

        // 4. Ambil Request Keluar (Fitur sebelumnya)
        $leavingStudents = $course->students()
            ->wherePivot('is_requesting_group_leave', true)
            ->with(['groups' => function($q) use ($id) {
                $q->where('class_id', $id);
            }])
            ->get();

        return view('admin.class_groups', compact('course', 'groups', 'topics', 'ungroupedStudents', 'leavingStudents'));
    }

    public function updateGroup(Request $request, $id)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'max_slots' => 'required|integer|min:1',
        ]);

        $group = Group::findOrFail($id);
        $group->update([
            'name'      => $request->name,
            'max_slots' => $request->max_slots,
        ]);

        return redirect()->back()->with('success', 'Informasi kelompok diperbarui.');
    }

    // Fitur Tambah Anggota Manual ke Kelompok
    public function storeGroupMember(Request $request, $group_id)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
        ]);

        $group = Group::findOrFail($group_id);
        
        // PERBAIKAN: Menggunakan 'course' sesuai nama fungsi di Model Group, bukan 'class'
        $course = $group->course; 

        // Cek apakah slot penuh
        if ($group->students()->count() >= $group->max_slots) {
            return redirect()->back()->with('error', 'Gagal! Kuota kelompok sudah penuh.');
        }

        // Update pivot table (class_members): Set group_id ke ID kelompok ini
        // Pastikan student tersebut memang anggota kelas ini (validasi tambahan opsional tapi disarankan)
        $course->students()->updateExistingPivot($request->student_id, [
            'group_id' => $group_id
        ]);

        return redirect()->back()->with('success', 'Anggota berhasil ditambahkan ke kelompok.');
    }

    // Fitur Hapus Anggota dari Kelompok (Kick)
    public function removeGroupMember($group_id, $student_id)
    {
        $group = Group::findOrFail($group_id);
        
        // PERBAIKAN: Menggunakan 'course' sesuai nama fungsi di Model Group
        $course = $group->course; 

        // Set group_id jadi NULL
        $course->students()->updateExistingPivot($student_id, [
            'group_id' => null,
            'is_requesting_group_leave' => false // Reset flag request juga jika ada
        ]);

        return redirect()->back()->with('success', 'Anggota dikeluarkan dari kelompok.');
    }

    public function storeGroup(Request $request)
    {
        $request->validate([
            'class_id'  => 'required|exists:classes,id',
            'topic_id'  => 'required|exists:topics,id',
            'name'      => 'required|string|max:100',
            'max_slots' => 'required|integer|min:1',
        ]);

        Group::create([
            'class_id'  => $request->class_id,
            'topic_id'  => $request->topic_id,
            'name'      => $request->name,
            'max_slots' => $request->max_slots,
        ]);

        return redirect()->back()->with('success', 'Kelompok berhasil dibuat.');
    }

    public function approveGroupLeave($class_id, $student_id)
    {
        $course = Course::findOrFail($class_id);
        
        // Update pivot class_members: set group_id jadi NULL dan reset flag request
        $course->students()->updateExistingPivot($student_id, [
            'group_id' => null,
            'is_requesting_group_leave' => false
        ]);

        return redirect()->back()->with('success', 'Mahasiswa diizinkan keluar dari kelompok.');
    }

    // 3. Menolak Permintaan Keluar
    public function rejectGroupLeave($class_id, $student_id)
    {
        $course = Course::findOrFail($class_id);
        
        // Hanya reset flag request, group_id tetap
        $course->students()->updateExistingPivot($student_id, [
            'is_requesting_group_leave' => false
        ]);

        return redirect()->back()->with('success', 'Permintaan keluar ditolak.');
    }

    // 4. Menghapus Kelompok
    public function destroyGroup($id)
    {
        $group = Group::findOrFail($id);
        
        // Otomatis foreign key di class_members akan jadi NULL (karena onDelete set null di migration)
        $group->delete(); 

        return redirect()->back()->with('success', 'Kelompok berhasil dihapus.');
    }

    public function syllabus(Request $request)
    {
        // Ambil semua kelas untuk dropdown filter
        $courses = Course::orderBy('name', 'asc')->get();
        
        $selectedCourse = null;
        $topics = collect(); // Default: Collection kosong (Rule: Jangan tampilkan apapun jika belum pilih kelas)

        // Logika Filter: Hanya ambil data jika ada request filter_class
        if ($request->filled('filter_class')) {
            $classId = $request->filter_class;
            $selectedCourse = Course::find($classId);
            
            if ($selectedCourse) {
                // Ambil topik berdasarkan kelas, urutkan dari tanggal pertemuan terlama (awal) ke terbaru
                $topics = Topic::where('class_id', $classId)
                    ->orderBy('meeting_date', 'asc')
                    ->get();
            }
        } 
        // Note: Tidak ada blok 'else' untuk memuat data default.

        return view('admin.syllabus', compact('courses', 'selectedCourse', 'topics'));
    }

    public function storeTopic(Request $request)
    {
        $request->validate([
            'class_id'     => 'required|exists:classes,id',
            'name'         => 'required|string|max:150',
            'meeting_date' => 'required|date',
            'description'  => 'nullable|string',
        ]);

        Topic::create([
            'class_id'     => $request->class_id,
            'name'         => $request->name,
            'meeting_date' => $request->meeting_date,
            'description'  => $request->description,
        ]);

        return redirect()->back()->with('success', 'Topik pertemuan berhasil ditambahkan.');
    }

    public function updateTopic(Request $request, $id)
    {
        $request->validate([
            'name'         => 'required|string|max:150',
            'meeting_date' => 'required|date',
            'description'  => 'nullable|string',
        ]);

        $topic = Topic::findOrFail($id);
        $topic->update([
            'name'         => $request->name,
            'meeting_date' => $request->meeting_date,
            'description'  => $request->description,
        ]);

        return redirect()->back()->with('success', 'Informasi topik diperbarui.');
    }

    public function destroyTopic($id)
    {
        $topic = Topic::findOrFail($id);
        $topic->delete();

        return redirect()->back()->with('success', 'Topik pertemuan dihapus.');
    }

    public function materials(Request $request)
    {
        // 1. Ambil list kelas untuk filter
        $courses = Course::orderBy('name', 'asc')->get();
        
        $selectedCourse = null;
        $topics = collect(); // Untuk dropdown di modal
        $materials = collect(); // Data utama

        if ($request->filled('filter_class')) {
            $classId = $request->filter_class;
            $selectedCourse = Course::find($classId);

            if ($selectedCourse) {
                // Ambil Topik dari kelas ini (untuk dropdown create/edit)
                $topics = Topic::where('class_id', $classId)
                    ->orderBy('meeting_date', 'asc')
                    ->get();

                // Ambil Materi yang topic-nya ada di kelas ini
                $materials = Material::with(['topic', 'author'])
                    ->whereHas('topic', function($q) use ($classId) {
                        $q->where('class_id', $classId);
                    })
                    ->latest()
                    ->get();
            }
        }

        return view('admin.materials_index', compact('courses', 'selectedCourse', 'topics', 'materials'));
    }

    public function storeMaterial(Request $request)
    {
        $request->validate([
            'topic_id'    => 'required|exists:topics,id',
            'title'       => 'required|string|max:150',
            'type'        => 'required|in:file,video,link',
            'file'        => 'required_if:type,file,video|file|max:20480', // Max 20MB
            'url'         => 'required_if:type,link|nullable|url',
            'description' => 'nullable|string',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // Simpan di folder: materials/TOPIC_ID/
            $filePath = $file->storeAs(
                'materials/' . $request->topic_id, 
                time() . '_' . $file->getClientOriginalName(), 
                'public'
            );
        }

        Material::create([
            'topic_id'    => $request->topic_id,
            'author_id'   => Auth::id(),
            'title'       => $request->title,
            'slug'        => Str::slug($request->title . '-' . Str::random(5)),
            'type'        => $request->type,
            'file_path'   => $filePath, // Null jika tipe link
            'url'         => $request->type === 'link' ? $request->url : null,
            'description' => $request->description,
            'is_published'=> true,
        ]);

        return redirect()->back()->with('success', 'Materi berhasil diupload.');
    }

    public function updateMaterial(Request $request, $id)
    {
        $request->validate([
            'title'       => 'required|string|max:150',
            'topic_id'    => 'required|exists:topics,id',
            'description' => 'nullable|string',
            // Kita tidak validasi file/url wajib disini karena mungkin user tidak ingin mengubahnya
        ]);

        $material = Material::findOrFail($id);
        
        $data = [
            'title'       => $request->title,
            'topic_id'    => $request->topic_id,
            'description' => $request->description,
        ];

        // Cek jika ada file baru
        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
                Storage::disk('public')->delete($material->file_path);
            }
            
            $file = $request->file('file');
            $data['file_path'] = $file->storeAs(
                'materials/' . $request->topic_id, 
                time() . '_' . $file->getClientOriginalName(), 
                'public'
            );
            $data['url'] = null; // Reset URL jika ganti jadi file
            $data['type'] = 'file'; // Asumsi jika upload file, tipe jadi file/video
        } 
        // Cek jika ada URL baru (dan tipe diubah jadi link)
        elseif ($request->filled('url')) {
             // Hapus file lama jika ada
             if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
                Storage::disk('public')->delete($material->file_path);
            }
            $data['url'] = $request->url;
            $data['file_path'] = null;
            $data['type'] = 'link';
        }

        $material->update($data);

        return redirect()->back()->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroyMaterial($id)
    {
        $material = Material::findOrFail($id);

        if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
            Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();

        return redirect()->back()->with('success', 'Materi dihapus.');
    }

    public function projects(Request $request)
    {
        $courses = Course::orderBy('name', 'asc')->get();
        $selectedCourse = null;
        $projects = collect();

        if ($request->filled('filter_class')) {
            $classId = $request->filter_class;
            $selectedCourse = Course::find($classId);

            if ($selectedCourse) {
                // Ambil projek, eager load student dan group
                $projects = StudentProject::with(['student', 'group'])
                    ->where('class_id', $classId)
                    ->latest()
                    ->get();
            }
        }

        // REVISI: Menggunakan view 'admin.class_projects' (bukan folder terpisah)
        return view('admin.class_projects', compact('courses', 'selectedCourse', 'projects'));
    }

    public function destroyProject($id)
    {
        $project = StudentProject::findOrFail($id);

        if ($project->file_path && Storage::disk('public')->exists($project->file_path)) {
            Storage::disk('public')->delete($project->file_path);
        }

        $project->delete();

        return redirect()->back()->with('success', 'Projek mahasiswa berhasil dihapus.');
    }

    public function toggleFeaturedProject($id)
    {
        $project = StudentProject::findOrFail($id);
        
        // Toggle status (true jadi false, false jadi true)
        $project->is_featured = !$project->is_featured;
        $project->save();

        $status = $project->is_featured ? 'ditandai sebagai Unggulan (Featured)' : 'dihapus dari daftar Unggulan';
        
        return redirect()->back()->with('success', "Projek berhasil $status.");
    }

    public function users(Request $request)
    {
        // Ambil daftar kelas aktif untuk filter dropdown
        $courses = Course::orderBy('name', 'asc')->get();

        // Query Dasar: Ambil user dengan role 'student'
        $query = User::where('role', 'student');

        // 1. Filter berdasarkan Kelas
        if ($request->filled('filter_class')) {
            // Asumsi relasi 'classes' (belongsToMany) ada di model User
            $query->whereHas('classes', function($q) use ($request) {
                $q->where('classes.id', $request->filter_class);
            });
        }

        // 2. Filter Pencarian (Nama / NIM / Email)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Urutkan dan Pagination
        $students = $query->orderBy('full_name', 'asc')->paginate(15)->withQueryString();

        return view('admin.users', compact('students', 'courses'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:100',
            'username'  => 'required|string|max:50|unique:users,username',
            'email'     => 'required|email|max:100|unique:users,email',
            'password'  => 'required|string|min:6',
        ]);

        User::create([
            'full_name' => $request->full_name,
            'username'  => $request->username,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => 'student',
            'avatar'    => 'default.jpg',
        ]);

        return redirect()->back()->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'full_name' => 'required|string|max:100',
            'username'  => 'required|string|max:50|unique:users,username,'.$user->id,
            'email'     => 'required|email|max:100|unique:users,email,'.$user->id,
            'password'  => 'nullable|string|min:6',
        ]);

        $data = [
            'full_name' => $request->full_name,
            'username'  => $request->username,
            'email'     => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Data mahasiswa diperbarui.');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        
        // Hapus file terkait jika perlu (opsional)
        // Storage::delete($user->avatar); 

        $user->delete();

        return redirect()->back()->with('success', 'Akun mahasiswa dihapus.');
    }

    public function activity()
    {
        return view('admin.activity');
    }
}
