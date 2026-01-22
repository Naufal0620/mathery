<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Course;
use App\Models\Topic;

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

    public function users()
    {
        return view('admin.users');
    }

    public function activity()
    {
        return view('admin.activity');
    }
}
