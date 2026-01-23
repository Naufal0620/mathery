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
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
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
            <div id="modalJoinClass" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
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
            </div>
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
@endsection