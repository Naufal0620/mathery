<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Topic;
use App\Models\Course;

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
}