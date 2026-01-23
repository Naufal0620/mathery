<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 1. Tampilkan Halaman Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // 2. Proses Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Coba Login (Laravel otomatis cek password hash)
        // Kita gunakan 'remember' (opsional, true/false)
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Cek Role untuk Redirect
            if ($user->role === 'admin' || $user->role === 'teacher') {
                return redirect()->route('admin.dashboard')->with('success', 'Selamat datang kembali, ' . $user->full_name . '!');
                } else if ($user->role === 'student') {
                return redirect()->route('student.dashboard')->with('success', 'Selamat datang kembali, ' . $user->full_name . '!');
            }

            return redirect()->intended('/')->with('success', 'Login berhasil!'); 
        }

        // Jika Gagal
        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    // 3. Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah keluar.');
    }
}