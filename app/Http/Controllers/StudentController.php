<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Topic;

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

    public function joinClass(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:classes,code',
        ]);

        $course = Course::where('code', $request->code)->firstOrFail();
        $user = Auth::user();

        // Cek apakah sudah tergabung/pending
        if ($user->classes()->where('class_id', $course->id)->exists()) {
            return redirect()->back()->with('error', 'Anda sudah tergabung atau sedang menunggu persetujuan di kelas ini.');
        }

        // Attach dengan status PENDING
        $user->classes()->attach($course->id, ['status' => 'pending']);

        return redirect()->back()->with('success', 'Permintaan bergabung dikirim! Silakan tunggu persetujuan dosen.');
    }
}