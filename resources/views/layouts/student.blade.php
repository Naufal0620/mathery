<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mathery - Student Dashboard')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @vite('resources/css/app.css?v=' . time())

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
        .fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-track { background: transparent; }
    </style>
</head>

<body class="flex h-screen bg-indigo-50 overflow-hidden text-gray-800">

    <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-20 hidden transition-opacity opacity-0 md:hidden"></div>

    <aside id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 bg-gray-50 border-r-2 border-gray-300/60 flex flex-col transition-transform duration-300 transform -translate-x-full md:relative md:translate-x-0 md:inset-0 shadow-2xl md:shadow-none">
        
        <div class="h-20 flex items-center px-6 justify-between">
            <div class="flex items-center gap-2">
                <i class="bx bx-planet bx-md bg-gradient-to-r from-indigo-700 to-purple-500 bg-clip-text text-transparent"></i>
                <span class="text-2xl font-bold bg-gradient-to-r from-indigo-700 to-purple-500 bg-clip-text text-transparent">MATHERY</span>
            </div>
            <button onclick="toggleSidebar()" class="md:hidden text-gray-500 hover:text-red-500">
                <i class='bx bx-x bx-md'></i>
            </button>
        </div>

        <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
            <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 mt-2">Menu Utama</p>
            
            <a href="{{ route('student.dashboard') }}" 
               class="nav-item flex items-center space-x-3 w-full p-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('student.dashboard') ? 'text-white bg-gradient-to-r from-indigo-700 to-purple-600 shadow-md' : 'text-gray-600 hover:text-white hover:bg-gradient-to-r hover:from-indigo-700 hover:to-purple-600' }}">
                <i class='bx bx-home-alt bx-sm'></i>
                <span class="font-medium">Beranda</span>
            </a>

            <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 mt-6">Akademik</p>

            <a href="#" 
               class="nav-item flex items-center space-x-3 w-full p-3 rounded-xl transition-all duration-300 group hover:text-white hover:bg-gradient-to-r hover:from-indigo-700 hover:to-purple-600 text-gray-600">
                <i class='bx bx-book-open bx-sm'></i>
                <span class="font-medium">Kelas Saya</span>
            </a>

            <a href="{{ route('student.projects.index') }}" 
               class="nav-item flex items-center space-x-3 w-full p-3 rounded-xl transition-all duration-300 group hover:text-white hover:bg-gradient-to-r hover:from-indigo-700 hover:to-purple-600 text-gray-600">
                <i class='bx bx-task bx-sm'></i>
                <span class="font-medium">Galeri Projek</span>
            </a>

            <a href="#" 
               class="nav-item flex items-center space-x-3 w-full p-3 rounded-xl transition-all duration-300 group hover:text-white hover:bg-gradient-to-r hover:from-indigo-700 hover:to-purple-600 text-gray-600">
                <i class='bx bx-trophy bx-sm'></i>
                <span class="font-medium">Pencapaian</span>
            </a>
        </nav>

        <div class="p-4 border-t border-gray-200">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center space-x-3 w-full p-3 rounded-xl text-red-500 hover:bg-red-50 transition-colors">
                    <i class='bx bx-arrow-out-right-square-half bx-sm'></i>
                    <span class="font-medium">Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="flex flex-col flex-1 w-full min-w-0">
        
        <nav class="h-20 px-4 md:px-8 flex justify-between items-center bg-gray-50 border-b-2 border-gray-300/60 shrink-0 sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-600 hover:bg-gray-200 rounded-lg">
                    <i class='bx bx-menu bx-md'></i>
                </button>
                <h2 class="text-lg md:text-xl font-bold text-gray-700 truncate">@yield('header_title', 'Student Area')</h2>
            </div>
            
            <div class="flex items-center gap-4">
                {{-- Notifikasi (Opsional) --}}
                <button class="relative p-2 text-gray-400 hover:text-indigo-600 transition">
                    <i class='bx bx-bell bx-sm'></i>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                </button>

                <div class="flex items-center gap-4 pl-4 border-l border-gray-300">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->full_name }}</p>
                        <p class="text-xs text-indigo-600 font-medium">Mahasiswa</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-gradient-to-r from-indigo-700 to-purple-500 p-[2px]">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->full_name) }}&background=random" class="w-full h-full rounded-full border-2 border-white object-cover">
                    </div>
                </div>
            </div>
        </nav>

        <main class="flex-1 overflow-y-auto p-4 md:p-8 relative">
            @yield('content')
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                setTimeout(() => { overlay.classList.remove('opacity-0'); }, 10);
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('opacity-0');
                setTimeout(() => { overlay.classList.add('hidden'); }, 300);
            }
        }
    </script>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
            "timeOut": "3000",
        };

        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif
        
        @if($errors->any())
            @foreach($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif
    </script>

    @stack('scripts')
</body>
</html>