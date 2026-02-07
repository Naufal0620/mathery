<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Topic;
use App\Models\Group;
use App\Models\Course;
use App\Models\Material;
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
            ->orderBy('name', 'asc')
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
        $course = Course::with('teacher')->findOrFail($id);

        if (!$course->students()->where('user_id', $user->id)->exists()) {
            return redirect()->route('student.dashboard')->with('error', 'Anda tidak terdaftar.');
        }

        // Ambil Topik + Komentar (Eager Load user & likes untuk performa)
        $topics = Topic::where('class_id', $id)
            ->with(['comments.user', 'comments.replies.user', 'comments.likes']) 
            ->orderBy('meeting_date', 'asc')
            ->get();

        return view('student.class_detail', compact('course', 'topics'));
    }

    // --- FITUR DISKUSI ---

    public function storeComment(Request $request, $topicId)
    {
        $request->validate([
            'body' => 'required_without:image|nullable|string',
            'image' => 'nullable|image|max:2048', // Max 2MB
            'parent_id' => 'nullable|exists:topic_comments,id'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('comments', 'public');
        }

        TopicComment::create([
            'topic_id' => $topicId,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'body' => $request->body,
            'image_path' => $imagePath
        ]);

        return redirect()->back()->with('success', 'Komentar terkirim.');
    }

    public function toggleLikeComment($commentId)
    {
        $user = Auth::user();
        $like = TopicCommentLike::where('topic_comment_id', $commentId)
            ->where('user_id', $user->id)
            ->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            TopicCommentLike::create([
                'topic_comment_id' => $commentId,
                'user_id' => $user->id
            ]);
            $liked = true;
        }

        // Return JSON untuk AJAX
        $count = TopicCommentLike::where('topic_comment_id', $commentId)->count();
        return response()->json(['liked' => $liked, 'count' => $count]);
    }

    // METHOD BARU: Gabung Kelompok
    public function joinGroup(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
        ]);

        $user = Auth::user();
        $group = Group::findOrFail($request->group_id);

        // 1. Validasi: Apakah User sudah punya kelompok di kelas ini?
        $existingGroup = Group::where('class_id', $group->class_id)
            ->whereHas('students', function($q) use ($user) {
                $q->where('users.id', $user->id);
            })->exists();

        if ($existingGroup) {
            return redirect()->back()->with('error', 'Anda sudah terdaftar di kelompok lain dalam kelas ini.');
        }

        // 2. Validasi: Apakah kelompok penuh? (Opsional, misalnya max 5)
        // if ($group->students()->count() >= 5) {
        //     return redirect()->back()->with('error', 'Kelompok ini sudah penuh.');
        // }

        // 3. Masukkan User ke Kelompok
        $group->students()->attach($user->id);

        return redirect()->back()->with('success', 'Berhasil bergabung dengan kelompok ' . $group->name);
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

    public function materialsIndex(Request $request)
    {
        $user = Auth::user();
        
        // 1. Ambil Kelas Mahasiswa (Accepted Only), urutkan dari yang terbaru bergabung
        $myClasses = $user->classes()
            ->wherePivot('status', 'accepted')
            ->orderByPivot('created_at', 'desc')
            ->get();
        
        // 2. Tentukan Kelas yang Dipilih
        $selectedClass = null;
        if ($request->has('class_id') && $request->class_id != '') {
            $selectedClass = $myClasses->where('id', $request->class_id)->first();
        } else {
            // Default: Ambil kelas paling baru
            $selectedClass = $myClasses->first();
        }

        $topics = collect(); // Default kosong
        $myAssignedTopic = null;

        if ($selectedClass) {
            // 3. Ambil Topik beserta Materinya (Group by Topic secara struktur data)
            $topics = Topic::where('class_id', $selectedClass->id)
                ->with(['materials' => function($q) {
                    $q->with('author')->latest(); // Urutkan materi dari terbaru
                }])
                ->orderBy('meeting_date', 'asc') // Urutkan pertemuan
                ->get();

            // 4. Cek Hak Akses Upload (Tugas Kelompok)
            $membership = $user->classes()
                ->where('classes.id', $selectedClass->id)
                ->withPivot('group_id')
                ->first();

            if ($membership && $membership->pivot->group_id) {
                $group = \App\Models\Group::find($membership->pivot->group_id);
                if ($group && $group->topic_id) {
                    $myAssignedTopic = Topic::find($group->topic_id);
                }
            }
        }

        return view('student.materials_index', compact('topics', 'myClasses', 'selectedClass', 'myAssignedTopic'));
    }

    public function storeMaterial(Request $request)
    {
        // 1. Aturan Dasar
        $rules = [
            'class_id' => 'required',
            'title'    => 'required|string|max:200',
            'type'     => 'required|in:pdf,ppt,link', // Mahasiswa biasanya hanya upload dokumen/ppt/link
        ];

        // 2. Whitelist Ekstensi Mahasiswa
        if ($request->type === 'pdf') {
            $rules['file'] = 'required|file|mimes:pdf|max:5120'; // Max 5MB
        } 
        elseif ($request->type === 'ppt') {
            $rules['file'] = 'required|file|mimes:ppt,pptx|max:5120';
        } 
        elseif ($request->type === 'link') {
            $rules['url'] = 'required|url';
        }

        $request->validate($rules);

        $user = Auth::user();

        // 1. Cek User anggota kelas
        $membership = $user->classes()->where('classes.id', $request->class_id)->withPivot('group_id')->first();
        if (!$membership || !$membership->pivot->group_id) {
            return back()->with('error', 'Anda belum memiliki kelompok di kelas ini.');
        }

        // 2. Cek Kelompok punya Topik
        $group = \App\Models\Group::find($membership->pivot->group_id);
        if (!$group || !$group->topic_id) {
            return back()->with('error', 'Kelompok Anda belum diberikan topik tugas oleh Dosen.');
        }

        // Proses Upload
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('materials', 'public');
        }

        // ... (Create Material) ...
        Material::create([
            'topic_id'     => $group->topic_id, // OTOMATIS KE TOPIK KELOMPOK
            'author_id'    => $user->id,
            'title'        => $request->title,
            'slug'         => Str::slug($request->title) . '-' . time(),
            'type'         => $request->type,
            'file_path'    => $filePath ?? $request->url,
            'is_published' => true,
        ]);
        
        return back()->with('success', 'Materi kelompok berhasil diunggah!');
    }

    public function projectsIndex(Request $request)
    {
        $user = Auth::user();

        // 1. Data untuk Modal Upload (Hanya kelas yg statusnya accepted)
        $myClasses = $user->classes()
            ->wherePivot('status', 'accepted')
            ->withPivot('group_id') // Penting untuk cek apakah punya kelompok
            ->get();

        // 2. Data untuk Filter & List Projek Global
        $allCourses = Course::all();
        
        $query = StudentProject::with(['student', 'course', 'group']);

        if ($request->has('class_id') && $request->class_id != '') {
            $query->where('class_id', $request->class_id);
        }

        $projects = $query->orderBy('is_featured', 'desc')
                          ->latest()
                          ->paginate(12);

        return view('student.projects.index', compact('projects', 'myClasses', 'allCourses'));
    }

    public function storeProject(Request $request)
    {
        $request->validate([
            'class_id'    => 'required|exists:classes,id',
            'title'       => 'required|string|max:200',
            'description' => 'required|string',
            'thumbnail'   => 'nullable|image|max:2048', // Max 2MB
            'project_url' => 'nullable|url',
            'repo_url'    => 'nullable|url',
        ]);

        $user = Auth::user();

        // 1. Cek Validasi Kelompok
        // User harus ada di kelas tsb DAN punya group_id
        $membership = $user->classes()
            ->where('classes.id', $request->class_id)
            ->withPivot('group_id')
            ->first();

        if (!$membership || !$membership->pivot->group_id) {
            return back()->with('error', 'Gagal Upload! Anda belum bergabung dengan KELOMPOK manapun di kelas tersebut.');
        }

        // 2. Handle Upload
        $thumbnailPath = 'project_default.jpg';
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('projects', 'public');
        }

        // 3. Simpan Data
        StudentProject::create([
            'student_id'  => $user->id,
            'class_id'    => $request->class_id,
            'group_id'    => $membership->pivot->group_id, // Ambil otomatis dari pivot
            'title'       => $request->title,
            'slug'        => Str::slug($request->title) . '-' . time(),
            'description' => $request->description,
            'thumbnail'   => $thumbnailPath,
            'project_url' => $request->project_url,
            'repo_url'    => $request->repo_url,
            'is_featured' => false,
        ]);

        return back()->with('success', 'Projek kelompok berhasil dipublikasikan!');
    }
}