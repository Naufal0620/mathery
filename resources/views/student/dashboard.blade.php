@extends('layouts.student')

@section('title', 'Dashboard - Mathery Mahasiswa')
@section('header_title', 'Dashboard')

@section('content')
<div class="fade-in max-w-7xl mx-auto pb-20 md:pb-0">
    
    {{-- 1. Welcome Banner (Mobile Optimized) --}}
    <div class="relative bg-gradient-to-br from-indigo-600 to-purple-700 rounded-3xl p-6 md:p-8 mb-8 text-white overflow-hidden shadow-lg shadow-indigo-200/50">
        {{-- Decorative Circles --}}
        <div class="absolute top-0 right-0 w-32 md:w-64 h-32 md:h-64 bg-white/10 rounded-full -mr-10 -mt-10 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-24 md:w-40 h-24 md:h-40 bg-white/10 rounded-full -ml-8 -mb-8 blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="w-full">
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-xs font-semibold border border-white/10">
                        Semester Genap
                    </span>
                </div>
                <h2 class="text-2xl md:text-4xl font-bold leading-tight mb-2">Halo, {{ Str::limit(Auth::user()->full_name, 15) }}! 👋</h2>
                <p class="text-indigo-100 text-sm md:text-lg mb-6 max-w-lg">
                    Siap melanjutkan progres belajarmu? Jangan lupa cek jadwal hari ini.
                </p>
                
                {{-- Stats Cards (Mobile: Stack/Grid, Desktop: Flex) --}}
                <div class="grid grid-cols-2 sm:flex gap-3">
                    <div class="bg-white/10 backdrop-blur-md px-4 py-3 rounded-2xl border border-white/10 flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-3 hover:bg-white/20 transition-colors">
                        <i class='bx bx-book-open text-2xl'></i>
                        <div>
                            <span class="block text-xl font-bold leading-none">{{ $myClasses->count() }}</span>
                            <span class="text-[10px] sm:text-xs text-indigo-100 uppercase font-medium">Kelas Aktif</span>
                        </div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md px-4 py-3 rounded-2xl border border-white/10 flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-3 hover:bg-white/20 transition-colors">
                        <i class='bx bx-calendar-event text-2xl'></i>
                        <div>
                            <span class="block text-xl font-bold leading-none">{{ $upcomingSchedules->count() }}</span>
                            <span class="text-[10px] sm:text-xs text-indigo-100 uppercase font-medium">Agenda</span>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Illustration (Hidden on Mobile for space) --}}
            <div class="hidden md:block relative">
                <i class='bx bx-rocket text-[10rem] text-white/10 absolute -top-10 -right-10 animate-pulse-slow'></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        
        {{-- KOLOM KIRI: DAFTAR KELAS (Prioritas Mobile) --}}
        <div class="xl:col-span-2 space-y-8">
            
            {{-- Header Section Kelas --}}
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <i class='bx bxs-school text-indigo-600'></i> Kelas Saya
                    </h3>
                    <p class="text-sm text-gray-500">Mata kuliah yang sedang Anda tempuh.</p>
                </div>
                
                {{-- Tombol Gabung (Full Width di Mobile) --}}
                <button onclick="openModal('modalJoinClass')" class="w-full sm:w-auto bg-white border border-indigo-100 text-indigo-600 hover:bg-indigo-600 hover:text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-all shadow-sm hover:shadow-md flex items-center justify-center gap-2 group">
                    <i class='bx bx-plus-circle text-xl group-hover:scale-110 transition-transform'></i> 
                    Gabung Kelas Baru
                </button>
            </div>

            {{-- SECTION 1: KELAS PENDING --}}
            @if($pendingClasses->count() > 0)
            <div class="space-y-3">
                <div class="flex items-center gap-2 text-orange-600 bg-orange-50 px-3 py-1.5 rounded-lg w-fit text-xs font-bold uppercase tracking-wider">
                    <i class='bx bx-time-five'></i> Menunggu Persetujuan
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($pendingClasses as $class)
                    <div class="bg-white rounded-2xl p-5 border border-orange-200 shadow-sm relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-3 opacity-10">
                            <i class='bx bx-loader-circle text-6xl text-orange-500'></i>
                        </div>
                        <div class="relative z-10">
                            <span class="inline-block bg-orange-100 text-orange-700 text-[10px] font-bold px-2 py-1 rounded mb-2">
                                {{ $class->code }}
                            </span>
                            <h4 class="font-bold text-gray-800 text-lg mb-1">{{ $class->name }}</h4>
                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                <i class='bx bx-user-voice'></i> {{ $class->teacher->full_name ?? 'Dosen' }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- SECTION 2: KELAS AKTIF --}}
            @if($myClasses->isEmpty() && $pendingClasses->isEmpty())
                {{-- Empty State --}}
                <div class="bg-white rounded-3xl p-10 text-center border-2 border-dashed border-gray-200">
                    <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4 text-indigo-300">
                        <i class='bx bx-chalkboard text-4xl'></i>
                    </div>
                    <h4 class="text-gray-800 font-bold text-lg mb-2">Belum ada kelas aktif</h4>
                    <p class="text-gray-500 text-sm mb-6 max-w-xs mx-auto">Anda belum mengambil mata kuliah apapun. Minta kode kelas ke dosen untuk memulai.</p>
                    <button onclick="openModal('modalJoinClass')" class="text-indigo-600 font-bold text-sm hover:underline hover:text-indigo-800 transition-colors">
                        Cari Kelas Sekarang &rarr;
                    </button>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @foreach($myClasses as $class)
                    <a href="{{ route('student.class.show', $class->id) }}" class="group relative bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer overflow-hidden">
                        
                        {{-- Hover Gradient Background --}}
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
                        {{-- Content --}}
                        <div class="relative z-10">
                            <div class="flex justify-between items-start mb-4">
                                <span class="bg-indigo-50 text-indigo-700 text-xs font-bold px-2 py-1 rounded-lg group-hover:bg-white/20 group-hover:text-white transition-colors">
                                    {{ $class->code }}
                                </span>
                                <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center group-hover:bg-white/20 text-gray-400 group-hover:text-white transition-colors">
                                    <i class='bx bx-chevron-right text-xl'></i>
                                </div>
                            </div>
                            
                            <h4 class="text-lg font-bold text-gray-800 mb-1 line-clamp-1 group-hover:text-white transition-colors">
                                {{ $class->name }}
                            </h4>
                            
                            <div class="flex items-center gap-2 text-sm text-gray-500 group-hover:text-indigo-100 transition-colors mb-6">
                                <i class='bx bx-user'></i>
                                <span>{{ $class->teacher->full_name ?? 'Dosen' }}</span>
                            </div>

                            {{-- Progress Bar (Dummy/Placeholder) --}}
                            <div class="w-full bg-gray-100 rounded-full h-1.5 group-hover:bg-black/20">
                                <div class="bg-indigo-500 group-hover:bg-white h-1.5 rounded-full" style="width: 0%"></div> {{-- Logic progress bisa ditambahkan nanti --}}
                            </div>
                            <div class="flex justify-between mt-2">
                                <span class="text-[10px] font-bold text-gray-400 group-hover:text-indigo-200">STATUS: AKTIF</span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- KOLOM KANAN: SIDEBAR WIDGETS (Stacked on Mobile) --}}
        <div class="space-y-6">
            
            {{-- Widget: Upcoming Schedule --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm flex flex-col h-full md:h-auto">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class='bx bx-calendar text-orange-500'></i> Jadwal
                    </h3>
                    <span class="text-xs font-medium text-gray-400">Terdekat</span>
                </div>

                <div class="p-5">
                    @if($upcomingSchedules->isEmpty())
                        <div class="text-center py-6">
                            <div class="bg-orange-50 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3 text-orange-400">
                                <i class='bx bx-calendar-x text-2xl'></i>
                            </div>
                            <p class="text-xs text-gray-500">Tidak ada jadwal kuliah dalam waktu dekat.</p>
                        </div>
                    @else
                        <div class="relative space-y-6 pl-2">
                            {{-- Timeline Line --}}
                            <div class="absolute left-[15px] top-2 bottom-2 w-0.5 bg-gray-100"></div>

                            @foreach($upcomingSchedules as $topic)
                            <div class="relative pl-8 group">
                                {{-- Timeline Dot --}}
                                <div class="absolute left-[9px] top-1.5 w-3.5 h-3.5 rounded-full bg-white border-[3px] border-indigo-500 group-hover:border-orange-500 transition-colors z-10"></div>
                                
                                <div>
                                    <h5 class="text-sm font-bold text-gray-800 leading-tight group-hover:text-indigo-600 transition-colors cursor-pointer">
                                        {{ $topic->name }}
                                    </h5>
                                    <p class="text-xs text-gray-500 mt-1 mb-2">{{ $topic->course->name ?? 'Kelas' }}</p>
                                    
                                    <div class="inline-flex items-center gap-2 bg-gray-50 border border-gray-200 px-2 py-1 rounded text-[10px] text-gray-600 font-medium">
                                        <i class='bx bx-time'></i>
                                        {{ \Carbon\Carbon::parse($topic->meeting_date)->translatedFormat('l, d M Y') }}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Widget: Quick Motivation / Stats --}}
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
                <i class='bx bxs-quote-right absolute top-4 right-4 text-4xl text-white/10'></i>
                <p class="text-sm font-medium text-gray-300 italic mb-4">"Pendidikan adalah senjata paling ampuh untuk mengubah dunia."</p>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-xs font-bold">M</div>
                    <span class="text-xs font-bold text-white/80">Nelson Mandela</span>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- MODAL GABUNG KELAS (NEW STRUCTURE - BACKDROP CLICK CLOSE) --}}
<div id="modalJoinClass" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto" onclick="closeModal('modalJoinClass')">
        <div class="flex min-h-full items-end sm:items-center justify-center p-0 sm:p-4 text-center">
            
            <div class="relative transform overflow-hidden rounded-t-2xl sm:rounded-2xl bg-white text-left shadow-2xl transition-all w-full sm:max-w-lg p-0 fade-in-up" onclick="event.stopPropagation()">
                
                {{-- Header Modal --}}
                <div class="bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center sticky top-0 z-20">
                    <h3 class="text-lg font-bold text-gray-800">Cari Kelas Baru</h3>
                    <button onclick="closeModal('modalJoinClass')" class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-red-50 hover:text-red-500 transition">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>
                
                <div class="p-6">
                    {{-- Search Input Group --}}
                    <div class="relative mb-6">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class='bx bx-search text-gray-400 text-xl'></i>
                        </div>
                        <input type="text" id="classSearchInput" 
                            class="block w-full pl-10 pr-10 py-3.5 border-2 border-gray-100 rounded-xl leading-5 bg-gray-50 text-gray-900 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-0 transition-colors sm:text-sm" 
                            placeholder="Ketik Nama / Kode Kelas..." autocomplete="off">
                        
                        <div id="searchLoader" class="absolute inset-y-0 right-0 pr-3 flex items-center hidden">
                            <i class='bx bx-loader-alt bx-spin text-indigo-500 text-xl'></i>
                        </div>
                    </div>

                    {{-- Dynamic Results Area --}}
                    <div id="searchResultsArea" class="min-h-[200px] max-h-[350px] overflow-y-auto custom-scrollbar">
                        
                        {{-- State: Start --}}
                        <div id="stateStart" class="flex flex-col items-center justify-center py-10 text-gray-400">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                <i class='bx bx-search-alt text-3xl'></i>
                            </div>
                            <p class="text-sm">Ketik minimal 3 karakter untuk mencari.</p>
                        </div>

                        {{-- State: No Results --}}
                        <div id="stateEmpty" class="hidden flex flex-col items-center justify-center py-10 text-gray-400">
                            <i class='bx bx-ghost text-4xl mb-2 opacity-50'></i>
                            <p class="text-sm">Kelas tidak ditemukan.</p>
                        </div>

                        {{-- State: List Results --}}
                        <div id="resultsContainer" class="hidden space-y-3">
                            {{-- Items will be injected here via JS --}}
                        </div>
                    </div>
                </div>
                
                {{-- Footer Hint --}}
                <div class="bg-gray-50 px-6 py-3 text-center border-t border-gray-100">
                    <p class="text-xs text-gray-400">Pastikan Anda memilih kelas yang benar sesuai jadwal.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- Load SweetAlert2 from CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // --- MODAL LOGIC (New Standard) ---
    function openModal(id) { 
        document.getElementById(id).classList.remove('hidden');
        // Auto focus input on desktop logic, slightly delayed for animation
        if(id === 'modalJoinClass') {
            setTimeout(() => document.getElementById('classSearchInput').focus(), 100);
        }
    }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    // --- SEARCH LOGIC (AJAX) ---
    const searchInput = document.getElementById('classSearchInput');
    const searchLoader = document.getElementById('searchLoader');
    const stateStart = document.getElementById('stateStart');
    const stateEmpty = document.getElementById('stateEmpty');
    const resultsContainer = document.getElementById('resultsContainer');
    let typingTimer;

    searchInput.addEventListener('input', function() {
        clearTimeout(typingTimer);
        const query = this.value.trim();

        if (query.length < 3) {
            resetSearchState();
            return;
        }

        // Show loading
        searchLoader.classList.remove('hidden');
        
        typingTimer = setTimeout(() => {
            fetchClasses(query);
        }, 600); // Debounce 600ms
    });

    function fetchClasses(query) {
        // Gunakan URL yang sudah ada di controller
        fetch(`{{ route('student.searchClasses') }}?search=${query}`)
            .then(response => response.json())
            .then(data => {
                renderClasses(data);
            })
            .catch(err => {
                console.error(err);
                // Gunakan SweetAlert2 untuk error koneksi
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Gagal mencari kelas. Periksa koneksi internet Anda.',
                    confirmButtonColor: '#4f46e5'
                });
            })
            .finally(() => {
                searchLoader.classList.add('hidden');
            });
    }

    function renderClasses(classes) {
        stateStart.classList.add('hidden');
        resultsContainer.innerHTML = ''; // Clear previous

        if (classes.length === 0) {
            stateEmpty.classList.remove('hidden');
            resultsContainer.classList.add('hidden');
        } else {
            stateEmpty.classList.add('hidden');
            resultsContainer.classList.remove('hidden');

            classes.forEach(cls => {
                // Buat Elemen HTML untuk setiap hasil
                const item = document.createElement('div');
                item.className = 'flex items-center justify-between p-4 bg-white border border-gray-100 rounded-xl hover:border-indigo-200 hover:shadow-md transition-all group';
                item.innerHTML = `
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold shrink-0">
                            ${cls.name.charAt(0)}
                        </div>
                        <div>
                            <h5 class="font-bold text-gray-800 text-sm group-hover:text-indigo-700 transition-colors">${cls.name}</h5>
                            <div class="flex items-center gap-2 text-xs text-gray-500 mt-0.5">
                                <span class="bg-gray-100 px-1.5 py-0.5 rounded font-mono">${cls.code}</span>
                                <span class="flex items-center gap-1"><i class='bx bx-user'></i> ${cls.teacher ? cls.teacher.full_name : 'Dosen'}</span>
                            </div>
                        </div>
                    </div>
                    <button onclick="confirmJoin('${cls.id}', '${cls.name}')" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 hover:shadow-none whitespace-nowrap">
                        Gabung
                    </button>
                `;
                resultsContainer.appendChild(item);
            });
        }
    }

    function resetSearchState() {
        stateStart.classList.remove('hidden');
        stateEmpty.classList.add('hidden');
        resultsContainer.classList.add('hidden');
        resultsContainer.innerHTML = '';
        searchLoader.classList.add('hidden');
    }

    // --- JOIN CLASS ACTION (SweetAlert2) ---
    function confirmJoin(classId, className) {
        // Tutup modal pencarian dulu (opsional, tapi lebih rapi)
        // closeModal('modalJoinClass'); 

        Swal.fire({
            title: 'Gabung Kelas?',
            text: `Anda akan mengirim permintaan untuk bergabung ke kelas "${className}".`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5', // Indigo-600
            cancelButtonColor: '#d1d5db', // Gray-300
            confirmButtonText: 'Ya, Gabung!',
            cancelButtonText: 'Batal',
            cancelButtonColor: '#6b7280',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-2xl font-poppins',
                confirmButton: 'rounded-xl px-4 py-2',
                cancelButton: 'rounded-xl px-4 py-2 text-gray-800'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                submitJoinRequest(classId);
            }
        });
    }

    function submitJoinRequest(classId) {
        // Buat form hidden dinamis untuk submit POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('student.joinClass') }}";
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = "{{ csrf_token() }}";
        form.appendChild(csrfToken);

        const inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = 'class_id';
        inputId.value = classId;
        form.appendChild(inputId);

        document.body.appendChild(form);
        form.submit();
    }

    // --- SESSION ALERTS (Menggantikan Toastr bawaan layout jika ada conflict, atau mempercantik) ---
    // Karena layout student sudah pakai Toastr, kita bisa override atau biarkan. 
    // Sesuai request "Ganti alert js biasa ke SweetAlert2", kode di bawah menangani flash message via SweetAlert2 (Toast Style)
    
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    @if(session('success'))
        Toast.fire({
            icon: 'success',
            title: "{{ session('success') }}"
        });
    @endif

    @if(session('error'))
        Toast.fire({
            icon: 'error',
            title: "{{ session('error') }}"
        });
    @endif

    @if($errors->any())
        Toast.fire({
            icon: 'warning',
            title: "{{ $errors->first() }}"
        });
    @endif

</script>

<style>
    /* Animation helper for modal bottom sheet on mobile */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .fade-in-up { animation: fadeInUp 0.3s ease-out; }
    
    /* Font Adjustment for SweetAlert */
    .font-poppins { font-family: 'Poppins', sans-serif; }
</style>
@endpush
@endsection