@extends('layouts.student')

@section('title', 'Dashboard Mahasiswa')
@section('header_title', 'Dashboard')

@section('content')
<div class="fade-in max-w-6xl mx-auto">
    
    {{-- 1. Welcome Banner --}}
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-3xl p-8 mb-8 text-white relative overflow-hidden shadow-xl shadow-indigo-200">
        {{-- Background Decoration --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-16 -mt-16 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/10 rounded-full -ml-10 -mb-10 blur-2xl"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div>
                <h2 class="text-3xl font-bold mb-2">Halo, {{ Auth::user()->full_name }}! 👋</h2>
                <p class="text-indigo-100 text-lg">Siap untuk belajar matematika hari ini?</p>
                
                <div class="flex gap-4 mt-6">
                    <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-xl border border-white/10 flex items-center gap-2">
                        <i class='bx bx-book-open'></i>
                        <span class="font-bold">{{ $myClasses->count() }} Kelas Aktif</span>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-xl border border-white/10 flex items-center gap-2">
                        <i class='bx bx-calendar-event'></i>
                        <span class="font-bold">{{ $upcomingSchedules->count() }} Jadwal Dekat</span>
                    </div>
                </div>
            </div>
            
            {{-- Optional Illustration/Icon --}}
            <div class="hidden md:block text-9xl text-white/20">
                <i class='bx bx-rocket'></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- 2. Kolom Kiri: Kelas Saya (Grid) --}}
        <div class="lg:col-span-2">
            <div class="flex justify-between items-end mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Kelas Saya</h3>
                    <p class="text-sm text-gray-500">Daftar kelas yang Anda ambil semester ini</p>
                </div>
                
                {{-- Tombol Trigger Modal Gabung --}}
                <button onclick="toggleModal('modalJoinClass')" class="bg-white border border-gray-200 text-indigo-600 px-4 py-2 rounded-xl font-bold text-sm hover:bg-indigo-50 hover:border-indigo-200 transition shadow-sm flex items-center gap-2">
                    <i class='bx bx-plus-circle text-lg'></i> Gabung Kelas Baru
                </button>
            </div>

            {{-- SECTION 1: KELAS PENDING (Jika ada) --}}
            @if($pendingClasses->count() > 0)
            <div class="mb-8">
                <h4 class="text-sm font-bold text-orange-600 mb-3 uppercase tracking-wide flex items-center gap-2">
                    <i class='bx bx-time'></i> Menunggu Persetujuan
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
                    @foreach($pendingClasses as $class)
                    <div class="bg-orange-50 rounded-2xl p-5 border border-orange-200/60 opacity-80">
                        <div class="flex justify-between items-start mb-2">
                            <span class="bg-orange-200 text-orange-800 text-[10px] font-bold px-2 py-1 rounded">{{ $class->code }}</span>
                            <i class='bx bx-loader-alt bx-spin text-orange-400'></i>
                        </div>
                        <h4 class="font-bold text-gray-700">{{ $class->name }}</h4>
                        <p class="text-xs text-orange-600 mt-2">Permintaan terkirim. Menunggu dosen...</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- SECTION 2: KELAS AKTIF (Ubah loop $myClasses yang lama) --}}
            @if($myClasses->isEmpty() && $pendingClasses->isEmpty())
                {{-- Empty State --}}
                <div class="bg-white rounded-2xl p-8 text-center border border-gray-200 border-dashed mb-8">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                        <i class='bx bx-whiteboard text-3xl'></i>
                    </div>
                    <h4 class="text-gray-800 font-bold mb-1">Belum ada kelas</h4>
                    <p class="text-gray-500 text-sm mb-4">Minta kode kelas ke dosen Anda untuk bergabung.</p>
                    <button onclick="toggleModal('modalJoinClass')" class="text-indigo-600 font-bold text-sm hover:underline">Gabung Sekarang</button>
                </div>
            @else
                {{-- Grid Kelas Aktif --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                    @foreach($myClasses as $class)
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md hover:border-indigo-100 transition group cursor-pointer relative overflow-hidden">
                        {{-- Stripe Biru --}}
                        <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-indigo-500 to-purple-500 group-hover:w-2 transition-all duration-300"></div>
                        
                        <div class="pl-3">
                            <div class="flex justify-between items-start mb-3">
                                <span class="bg-indigo-50 text-indigo-700 text-xs font-bold px-2 py-1 rounded-md">{{ $class->code }}</span>
                                <i class='bx bx-chevron-right text-gray-300 group-hover:text-indigo-500 text-xl transition'></i>
                            </div>
                            
                            <h4 class="text-lg font-bold text-gray-800 mb-1 line-clamp-1 group-hover:text-indigo-700 transition">{{ $class->name }}</h4>
                            
                            <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                                <i class='bx bx-user-voice'></i>
                                <span>{{ $class->teacher->full_name ?? 'Dosen' }}</span>
                            </div>

                            <div class="pt-4 border-t border-gray-50 flex justify-between items-center">
                                <span class="text-xs text-green-600 font-bold bg-green-50 px-2 py-1 rounded">Aktif</span>
                                <button class="text-xs bg-gray-800 text-white px-3 py-1.5 rounded-lg hover:bg-gray-700 transition">
                                    Masuk Kelas
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif

            {{-- MODAL GABUNG KELAS --}}
            {{-- <div id="modalJoinClass" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md transform scale-100 transition-all">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-800">Gabung Kelas Baru</h3>
                        <button onclick="toggleModal('modalJoinClass')" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 transition">
                            <i class='bx bx-x bx-sm'></i>
                        </button>
                    </div>
                    
                    <form action="{{ route('student.joinClass') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kode Kelas</label>
                            <div class="relative">
                                <input type="text" name="code" required placeholder="Contoh: KAL-PSIK25A-FWR" class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500/20 outline-none transition font-mono uppercase">
                                <i class='bx bx-hash absolute left-3 top-3.5 text-gray-400 text-lg'></i>
                            </div>
                            <p class="text-xs text-gray-400 mt-2">Dapatkan kode unik kelas dari dosen pengampu.</p>
                        </div>

                        <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3 rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 hover:shadow-none">
                            Minta Bergabung
                        </button>
                    </form>
                </div>
            </div> --}}
        </div>

        {{-- 3. Kolom Kanan: Jadwal Upcoming (Timeline) --}}
        <div>
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">Jadwal Mendatang</h3>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                @if($upcomingSchedules->isEmpty())
                    <div class="text-center py-8">
                        <i class='bx bx-coffee text-4xl text-gray-300 mb-2'></i>
                        <p class="text-sm text-gray-500">Tidak ada jadwal kuliah dalam waktu dekat.</p>
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach($upcomingSchedules as $topic)
                        <div class="relative pl-6 border-l-2 border-indigo-100 last:border-0">
                            {{-- Dot Indicator --}}
                            <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-white border-4 border-indigo-500"></div>
                            
                            <div>
                                <h5 class="text-sm font-bold text-gray-800">{{ $topic->name }}</h5>
                                <p class="text-xs text-indigo-600 font-medium mb-1">{{ $topic->course->name ?? 'Kelas' }}</p>
                                
                                <div class="flex items-center gap-4 text-xs text-gray-500 mt-2">
                                    <span class="flex items-center gap-1 bg-gray-50 px-2 py-1 rounded border border-gray-100">
                                        <i class='bx bx-calendar'></i> {{ \Carbon\Carbon::parse($topic->meeting_date)->translatedFormat('d M Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
            
            {{-- Mini Widget: Project Stats (Placeholder) --}}
            <div class="mt-6 bg-gradient-to-br from-orange-400 to-red-500 rounded-2xl p-6 text-white shadow-lg shadow-orange-100 relative overflow-hidden">
                <i class='bx bx-trophy absolute -right-4 -bottom-4 text-8xl text-white/20'></i>
                <h4 class="text-lg font-bold mb-1">Proyek Anda</h4>
                <p class="text-orange-100 text-sm mb-4">Kumpulkan tugas tepat waktu!</p>
                <div class="text-3xl font-bold">0 <span class="text-sm font-normal text-orange-100">Selesai</span></div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL GABUNG KELAS (REDESIGNED) --}}
<div id="modalJoinClass" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg transform scale-100 transition-all overflow-hidden">
        
        {{-- Header Modal --}}
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-800">Cari & Gabung Kelas</h3>
            <button onclick="toggleModal('modalJoinClass')" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 transition">
                <i class='bx bx-x bx-sm'></i>
            </button>
        </div>
        
        <div class="p-6">
            {{-- Search Input --}}
            <div class="relative group mb-6">
                <input type="text" id="classSearchInput" autocomplete="off" placeholder="Ketik Nama Mata Kuliah..." class="w-full bg-white border border-gray-200 text-gray-700 rounded-xl pl-4 pr-10 py-3 text-sm focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition shadow-sm group-hover:border-indigo-300">
                
                {{-- Icons --}}
                <div id="searchIconDefault" class="absolute right-3 top-3 text-gray-400">
                    <i class='bx bx-search text-lg'></i>
                </div>
                <div id="searchIconLoading" class="absolute right-3 top-3 text-indigo-500 hidden">
                    <i class='bx bx-loader-alt bx-spin text-lg'></i>
                </div>
            </div>

            {{-- Results Container --}}
            <div class="min-h-[150px]">
                <p id="startMessage" class="text-center text-gray-400 text-sm py-8">
                    <i class='bx bx-search-alt text-3xl mb-2 opacity-50 block'></i>
                    Mulai ketik nama kelas untuk mencari.
                </p>

                <p id="noResultsMessage" class="hidden text-center text-gray-400 text-sm py-8">
                    <i class='bx bx-ghost text-3xl mb-2 opacity-50 block'></i>
                    Kelas tidak ditemukan.
                </p>
                
                {{-- List Hasil (Scrollable) --}}
                <div id="resultsList" class="hidden max-h-[300px] overflow-y-auto custom-scrollbar border border-gray-100 rounded-xl bg-white shadow-inner">
                    <table class="w-full text-left text-sm">
                        <tbody id="resultsTableBody" class="divide-y divide-gray-50">
                            {{-- Data akan di-inject lewat JS di sini --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            // Fokus ke input saat modal dibuka
            if(modalId === 'modalJoinClass') {
                setTimeout(() => document.getElementById('classSearchInput').focus(), 100);
            }
        } else {
            modal.classList.add('hidden');
        }
    }

    // --- AJAX SEARCH LOGIC ---
    const searchInput = document.getElementById('classSearchInput');
    const searchIconDefault = document.getElementById('searchIconDefault');
    const searchIconLoading = document.getElementById('searchIconLoading');
    
    const resultsList = document.getElementById('resultsList');
    const resultsTableBody = document.getElementById('resultsTableBody');
    const startMessage = document.getElementById('startMessage');
    const noResultsMessage = document.getElementById('noResultsMessage');

    let searchTimeout = null;

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();

        // 1. UI State: Loading
        clearTimeout(searchTimeout);
        searchIconDefault.classList.add('hidden');
        searchIconLoading.classList.remove('hidden');

        // 2. Debounce (Tunggu user berhenti ngetik)
        searchTimeout = setTimeout(() => {
            if (query.length === 0) {
                // Reset jika kosong
                resetSearchUI();
                return;
            }
            fetchClasses(query);
        }, 600);
    });

    function fetchClasses(query) {
        // Panggil Route AJAX
        fetch(`{{ route('student.searchClasses') }}?search=${query}`)
            .then(response => response.json())
            .then(data => {
                renderResults(data);
            })
            .catch(error => {
                console.error('Error:', error);
            })
            .finally(() => {
                // Matikan Loading
                searchIconDefault.classList.remove('hidden');
                searchIconLoading.classList.add('hidden');
            });
    }

    function renderResults(classes) {
        // Bersihkan hasil lama
        resultsTableBody.innerHTML = '';

        // Toggle Visibility
        startMessage.classList.add('hidden');
        
        if (classes.length === 0) {
            resultsList.classList.add('hidden');
            noResultsMessage.classList.remove('hidden');
        } else {
            noResultsMessage.classList.add('hidden');
            resultsList.classList.remove('hidden');

            // Loop data dan buat HTML Row
            classes.forEach(cls => {
                const row = `
                    <tr class="hover:bg-indigo-50/30 transition group">
                        <td class="p-3 pl-4">
                            <p class="font-bold text-gray-700 text-sm group-hover:text-indigo-700 transition">${cls.name}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-[10px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded font-mono">${cls.code}</span>
                                <span class="text-[11px] text-gray-400 flex items-center gap-1"><i class='bx bx-user'></i> ${cls.teacher ? cls.teacher.full_name : '-'}</span>
                            </div>
                        </td>
                        <td class="p-3 pr-4 w-1 whitespace-nowrap text-right align-middle">
                            <form action="{{ route('student.joinClass') }}" method="POST">
                                @csrf
                                <input type="hidden" name="class_id" value="${cls.id}">
                                <button type="submit" class="inline-flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 px-3 py-1.5 rounded-lg transition-all shadow-sm text-xs font-bold gap-1">
                                    <i class='bx bx-plus'></i> Gabung
                                </button>
                            </form>
                        </td>
                    </tr>
                `;
                resultsTableBody.insertAdjacentHTML('beforeend', row);
            });
        }
    }

    function resetSearchUI() {
        searchIconDefault.classList.remove('hidden');
        searchIconLoading.classList.add('hidden');
        resultsList.classList.add('hidden');
        noResultsMessage.classList.add('hidden');
        startMessage.classList.remove('hidden');
        resultsTableBody.innerHTML = '';
    }
</script>
@endpush
@endsection