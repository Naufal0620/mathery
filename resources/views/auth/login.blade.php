<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mathery</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Link CSS Lokal (Pastikan file style.css ada di folder public/css) -->
    @vite('resources/css/app.css?v=' . time())

    <!-- Boxicons v3.0.8 -->
    <link href="https://cdn.boxicons.com/3.0.8/fonts/basic/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.boxicons.com/3.0.8/fonts/filled/boxicons-filled.min.css" rel="stylesheet">
    <link href="https://cdn.boxicons.com/3.0.8/fonts/brands/boxicons-brands.min.css" rel="stylesheet">
    <link href="https://cdn.boxicons.com/transformations.min.css" rel="stylesheet">
    <link href="https://cdn.boxicons.com/animations.min.css" rel="stylesheet">

    {{-- Toastr --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        {{-- Header Image / Logo Area --}}
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-8 text-center">
            <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto mb-4 text-white text-3xl font-bold shadow-inner">
                <i class='bx bx-planet'></i>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-wide">Mathery</h1>
            <p class="text-indigo-100 text-sm mt-1">Student-Centered Learning Platform</p>
        </div>

        {{-- Form Area --}}
        <div class="p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-6 text-center">Masuk ke Akun Anda</h2>

            {{-- Error Message --}}
            @if($errors->any())
                <div class="mb-4 bg-red-50 text-red-500 text-sm p-3 rounded-xl border border-red-100 flex items-start gap-2">
                    <i class='bx bx-error-circle mt-0.5'></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 bg-green-50 text-green-600 text-sm p-3 rounded-xl border border-green-100 flex items-start gap-2">
                    <i class='bx bx-check-circle mt-0.5'></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                @csrf
                
                {{-- Username / NIM --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Username / NIM</label>
                    <div class="relative">
                        <input type="text" name="username" value="{{ old('username') }}" required autofocus placeholder="Masukkan Username atau NIM" class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                        <i class='bx bx-user absolute left-3 top-3.5 self-center text-gray-400 text-lg'></i>
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <input type="password" name="password" required placeholder="••••••••" class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                        <i class='bx bx-lock absolute left-3 top-3.5 text-gray-400 text-lg'></i>
                    </div>
                </div>

                {{-- Remember Me & Forgot --}}
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 cursor-pointer text-gray-600">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-gray-300">
                        <span>Ingat Saya</span>
                    </label>
                    <a href="#" class="text-indigo-600 hover:text-indigo-800 font-medium hover:underline">Lupa Password?</a>
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-indigo-200 hover:shadow-none hover:opacity-90 transition-all transform active:scale-[0.98]">
                    Masuk Sekarang
                </button>
            </form>
        </div>
        
        <div class="bg-gray-50 p-4 text-center border-t border-gray-100">
            <p class="text-xs text-gray-400">&copy; {{ date('Y') }} Mathery Learning Platform</p>
        </div>
    </div>

</body>
</html>