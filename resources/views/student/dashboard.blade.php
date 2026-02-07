@extends('layouts.student')

@section('title', 'Dashboard - Mathery Mahasiswa')
@section('header_title', 'Dashboard')

@section('content')
<div class="fade-in max-w-7xl mx-auto pb-20 md:pb-0">
    
    {{-- 1. Welcome Banner --}}
    <div class="relative bg-gradient-to-br from-indigo-600 to-purple-700 rounded-3xl p-6 md:p-10 mb-10 text-white overflow-hidden shadow-2xl shadow-indigo-200/50">
        
        {{-- Decorative Background Elements --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-16 -mt-16 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/10 rounded-full -ml-10 -mb-10 blur-2xl pointer-events-none"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full h-full bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-white/10 via-transparent to-transparent opacity-30 pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-8">
            <div class="w-full lg:w-2/3">
                {{-- REVISI: Menghapus Badge 'Semester Genap' --}}
                
                <h2 class="text-3xl md:text-5xl font-extrabold leading-tight mb-3 tracking-tight">
                    Halo, {{ Str::before(Auth::user()->full_name, ' ') }}! 👋
                </h2>
                <p class="text-indigo-100 text-base md:text-lg mb-4 max-w-lg leading-relaxed opacity-90">
                    Siap melanjutkan progres belajarmu? Cek jadwal dan materi terbaru hari ini.
                </p>
            </div>
            
            {{-- Illustration --}}
            <div class="hidden lg:block relative w-1/3 h-full min-h-[200px]">
                <i class='bx bx-rocket text-[12rem] text-white/5 absolute top-1/2 left-1/2 transform translate-x-1/2 animate-float'></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        {{-- KOLOM KIRI: DAFTAR KELAS --}}
        <div class="lg:col-span-2 space-y-8">
            
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <i class='bx bx-whiteboard-alt text-indigo-600'></i> Kelas Saya
                    </h3>
                    <p class="text-sm text-gray-500">Mata kuliah yang sedang Anda tempuh.</p>
                </div>
                
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
                <div class="bg-white rounded-3xl p-10 text-center border-2 border-dashed border-gray-200">
                    <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4 text-indigo-300">
                        <i class='bx bx-whiteboard text-4xl'></i>
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
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
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

                            <div class="w-full bg-gray-100 rounded-full h-1.5 group-hover:bg-black/20">
                                <div class="bg-indigo-500 group-hover:bg-white h-1.5 rounded-full" style="width: 0%"></div>
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

        {{-- KOLOM KANAN: SIDEBAR WIDGETS --}}
        <div class="space-y-6">
            
            {{-- Widget: Upcoming Schedule --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm flex flex-col">
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
                        <div class="flex flex-col">
                            @foreach($upcomingSchedules as $topic)
                            <div class="flex gap-4 group min-h-[4rem]"> 
                                
                                {{-- Timeline Graphic --}}
                                <div class="relative flex flex-col items-center w-4">
                                    @if($upcomingSchedules->count() > 1)
                                        <div class="absolute w-0.5 bg-gray-100 left-1/2 -translate-x-1/2
                                            {{ $loop->first ? 'top-2' : 'top-0' }}
                                            {{ $loop->last ? 'h-4' : 'h-full' }}
                                        "></div>
                                    @endif
                                    <div class="w-3.5 h-3.5 rounded-full bg-white border-[3px] border-indigo-500 group-hover:border-orange-500 transition-colors z-10 mt-1 relative"></div>
                                </div>
                                
                                {{-- Content --}}
                                <div class="pb-6">
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

            {{-- Widget: Quick Motivation --}}
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

{{-- MODAL GABUNG KELAS --}}
<div id="modalJoinClass" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto" onclick="closeModal('modalJoinClass')">
        <div class="flex min-h-full items-end sm:items-center justify-center p-0 sm:p-4 text-center">
            <div class="relative transform overflow-hidden rounded-t-2xl sm:rounded-2xl bg-white text-left shadow-2xl transition-all w-full sm:max-w-lg p-0 fade-in-up" onclick="event.stopPropagation()">
                
                <div class="bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center sticky top-0 z-20">
                    <h3 class="text-lg font-bold text-gray-800">Cari Kelas Baru</h3>
                    <button onclick="closeModal('modalJoinClass')" class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-red-50 hover:text-red-500 transition">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>
                
                <div class="p-6">
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

                    <div id="searchResultsArea" class="min-h-[200px] max-h-[350px] overflow-y-auto custom-scrollbar">
                        <div id="stateStart" class="flex flex-col items-center justify-center py-10 text-gray-400">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                <i class='bx bx-search-alt text-3xl'></i>
                            </div>
                            <p class="text-sm">Ketik minimal 3 karakter untuk mencari.</p>
                        </div>

                        <div id="stateEmpty" class="hidden flex flex-col items-center justify-center py-10 text-gray-400">
                            <i class='bx bx-ghost text-4xl mb-2 opacity-50'></i>
                            <p class="text-sm">Kelas tidak ditemukan.</p>
                        </div>

                        <div id="resultsContainer" class="hidden space-y-3"></div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-3 text-center border-t border-gray-100">
                    <p class="text-xs text-gray-400">Pastikan Anda memilih kelas yang benar sesuai jadwal.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openModal(id) { 
        document.getElementById(id).classList.remove('hidden');
        if(id === 'modalJoinClass') { setTimeout(() => document.getElementById('classSearchInput').focus(), 100); }
    }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    const searchInput = document.getElementById('classSearchInput');
    const searchLoader = document.getElementById('searchLoader');
    const stateStart = document.getElementById('stateStart');
    const stateEmpty = document.getElementById('stateEmpty');
    const resultsContainer = document.getElementById('resultsContainer');
    let typingTimer;

    searchInput.addEventListener('input', function() {
        clearTimeout(typingTimer);
        const query = this.value.trim();
        if (query.length < 3) { resetSearchState(); return; }
        searchLoader.classList.remove('hidden');
        typingTimer = setTimeout(() => { fetchClasses(query); }, 600);
    });

    function fetchClasses(query) {
        fetch(`{{ route('student.searchClasses') }}?search=${query}`)
            .then(response => response.json())
            .then(data => { renderClasses(data); })
            .catch(err => { Swal.fire({icon: 'error', title: 'Oops...', text: 'Gagal mencari kelas.', confirmButtonColor: '#4f46e5'}); })
            .finally(() => { searchLoader.classList.add('hidden'); });
    }

    function renderClasses(classes) {
        stateStart.classList.add('hidden');
        resultsContainer.innerHTML = '';
        if (classes.length === 0) {
            stateEmpty.classList.remove('hidden');
            resultsContainer.classList.add('hidden');
        } else {
            stateEmpty.classList.add('hidden');
            resultsContainer.classList.remove('hidden');
            classes.forEach(cls => {
                const item = document.createElement('div');
                item.className = 'p-4 mx-3 bg-gray-100 border border-gray-100 rounded-xl hover:border-indigo-200 hover:shadow-md transition-all group';
                item.innerHTML = `
                    <div class="flex items-center gap-4 mb-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold shrink-0">${cls.name.charAt(0)}</div>
                        <div>
                            <h5 class="font-bold text-gray-800 text-sm group-hover:text-indigo-700 transition-colors">${cls.name}</h5>
                            <div class="flex items-center gap-2 text-xs text-gray-500 mt-0.5">
                                <span class="bg-gray-100 px-1.5 py-0.5 rounded font-mono">${cls.code}</span>
                                <span class="flex items-center gap-1"><i class='bx bx-user'></i> ${cls.teacher ? cls.teacher.full_name : 'Dosen'}</span>
                            </div>
                        </div>
                    </div>
                    <button onclick="confirmJoin('${cls.id}', '${cls.name}')" class="w-full px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 hover:shadow-none whitespace-nowrap">Gabung</button>
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

    function confirmJoin(classId, className) {
        Swal.fire({
            title: 'Gabung Kelas?',
            text: `Anda akan mengirim permintaan untuk bergabung ke kelas "${className}".`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#d1d5db',
            confirmButtonText: 'Ya, Gabung!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: { popup: 'rounded-2xl font-poppins', confirmButton: 'rounded-xl px-4 py-2', cancelButton: 'rounded-xl px-4 py-2 text-gray-800' }
        }).then((result) => { if (result.isConfirmed) { submitJoinRequest(classId); } });
    }

    function submitJoinRequest(classId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('student.joinClass') }}";
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden'; csrfToken.name = '_token'; csrfToken.value = "{{ csrf_token() }}";
        form.appendChild(csrfToken);
        const inputId = document.createElement('input');
        inputId.type = 'hidden'; inputId.name = 'class_id'; inputId.value = classId;
        form.appendChild(inputId);
        document.body.appendChild(form);
        form.submit();
    }
</script>

<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes float { 0%, 100% { transform: translate(-50%, -50%) translateY(0px); } 50% { transform: translate(-50%, -50%) translateY(-15px); } }
    .fade-in-up { animation: fadeInUp 0.3s ease-out; }
    .animate-float { animation: float 6s ease-in-out infinite; }
    .font-poppins { font-family: 'Poppins', sans-serif; }
</style>
@endpush
@endsection