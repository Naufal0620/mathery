@extends('layouts.student')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- 1. HEADER KELAS --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-8 border border-gray-100">
        <div class="h-40 bg-linear-to-r from-indigo-600 to-purple-400 relative">
            <div class="absolute inset-0 bg-black/10"></div> <div class="absolute bottom-0 left-0 p-6 w-full text-white">
                <div class="flex items-end justify-between">
                    <div>
                        <h1 class="text-3xl font-bold mb-1 shadow-black drop-shadow-md">{{ $course->name }}</h1>
                        <p class="text-blue-100 text-sm font-medium flex items-center gap-4">
                            <span class="flex items-center gap-1 bg-white/20 px-2 py-1 rounded">
                                <i class="fas fa-barcode"></i> {{ $course->code }}
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="fas fa-chalkboard-teacher"></i> Dosen: {{ $course->teacher->full_name }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-6">
            <h3 class="text-gray-900 font-semibold mb-2">Tentang Kelas</h3>
            <p class="text-gray-600 leading-relaxed text-sm">
                {{ $course->description ?? 'Tidak ada deskripsi tambahan untuk kelas ini.' }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- 2. LIST MATERI & PERTEMUAN (Kolom Kiri - Lebar) --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between border-b border-gray-200 pb-3">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-book-reader text-blue-600"></i> Rencana Pembelajaran
                </h2>
                <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-1 rounded">
                    {{ $course->topics->count() }} Pertemuan
                </span>
            </div>

            @forelse($course->topics as $topic)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-300 group">
                    <div class="p-5">
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between mb-3 gap-2">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800 group-hover:text-blue-600 transition-colors">
                                    {{ $topic->name }}
                                </h3>
                                <p class="text-sm text-gray-600 mt-1 leading-snug">
                                    {{ $topic->description }}
                                </p>
                            </div>
                            <div class="shrink-0">
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 bg-gray-50 border border-gray-200 px-2 py-1 rounded-md whitespace-nowrap">
                                    <i class="far fa-calendar-alt text-blue-500"></i> 
                                    {{ \Carbon\Carbon::parse($topic->meeting_date)->translatedFormat('d M Y') }}
                                </span>
                            </div>
                        </div>

                        @if($topic->materials && $topic->materials->count() > 0)
                            <div class="mt-4 bg-blue-50/50 rounded-lg p-3 border border-blue-100">
                                <h4 class="text-[11px] font-bold text-blue-800 uppercase tracking-wider mb-2">Materi Pembelajaran</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($topic->materials as $material)
                                        <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="flex items-center gap-3 p-2 bg-white rounded border border-gray-200 hover:border-blue-400 hover:text-blue-600 transition group/file">
                                            {{-- Icon Berdasarkan Tipe --}}
                                            <div class="text-gray-400 group-hover/file:text-blue-500 transition-colors">
                                                @if($material->type == 'pdf') <i class="far fa-file-pdf fa-lg"></i>
                                                @elseif($material->type == 'video') <i class="far fa-file-video fa-lg"></i>
                                                @else <i class="far fa-file-alt fa-lg"></i> @endif
                                            </div>
                                            
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium truncate">{{ $material->title }}</p>
                                                <p class="text-[10px] text-gray-400 uppercase">{{ $material->type }}</p>
                                            </div>
                                            
                                            <i class="fas fa-download text-gray-300 group-hover/file:text-blue-500 text-xs"></i>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="mt-3 pt-3 border-t border-gray-50 text-xs text-gray-400 italic flex items-center gap-1">
                                <i class="fas fa-info-circle"></i> Belum ada materi yang diunggah dosen.
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-white rounded-xl border-2 border-dashed border-gray-200">
                    <div class="bg-gray-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                        <i class="fas fa-clipboard-list text-2xl"></i>
                    </div>
                    <h4 class="text-gray-600 font-medium">Belum ada topik pertemuan</h4>
                    <p class="text-gray-400 text-sm">Silakan tunggu dosen memperbarui silabus.</p>
                </div>
            @endforelse
        </div>

        {{-- 3. LIST KELOMPOK (Kolom Kanan / Sidebar) --}}
        <div class="space-y-6">
            <div class="flex items-center justify-between border-b border-gray-200 pb-3">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-users text-purple-600"></i> Kelompok
                </h2>
            </div>

            {{-- A. Kartu Kelompok Saya (Jika Ada) --}}
            @if($membership->group_id)
                @php 
                    $myGroup = $course->groups->where('id', $membership->group_id)->first(); 
                @endphp
                @if($myGroup)
                    <div class="bg-gradient-to-b from-green-50 to-white border border-green-200 rounded-xl p-5 shadow-sm relative overflow-hidden ring-2 ring-green-100">
                        <div class="absolute top-0 right-0 p-3 opacity-10">
                            <i class="fas fa-certificate text-7xl text-green-600"></i>
                        </div>
                        
                        <div class="relative z-10">
                            <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wide">
                                Kelompok Anda
                            </span>
                            <h4 class="font-bold text-green-900 text-lg mt-2 mb-1">{{ $myGroup->name }}</h4>
                            
                            @if($myGroup->topic)
                            <p class="text-xs text-green-700 mb-4 font-medium">
                                <i class="fas fa-tag mr-1"></i> Topik: {{ $myGroup->topic->name }}
                            </p>
                            @endif
                            
                            <div class="bg-white/80 rounded-lg p-3 border border-green-100 mb-4 shadow-sm">
                                <ul class="space-y-2">
                                    @foreach($myGroup->students as $member)
                                        <li class="flex items-center gap-2 text-sm text-gray-700">
                                            <div class="w-6 h-6 rounded-full bg-green-200 text-green-800 flex items-center justify-center text-xs font-bold">
                                                {{ substr($member->full_name, 0, 1) }}
                                            </div>
                                            <span class="{{ $member->id === auth()->id() ? 'font-bold' : '' }}">
                                                {{ $member->full_name }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            @if($membership->is_requesting_group_leave)
                                <button disabled class="w-full py-2 bg-gray-400 text-white rounded-lg text-sm font-semibold cursor-not-allowed flex items-center justify-center gap-2">
                                    <i class="fas fa-spinner fa-spin"></i> Menunggu Izin Keluar
                                </button>
                            @else
                                <form action="{{ route('student.group.leave', $course->id) }}" method="POST" onsubmit="return confirm('Yakin ingin keluar? Slot Anda mungkin akan diisi orang lain.');">
                                    @csrf
                                    <button type="submit" class="w-full py-2 bg-white text-red-500 border border-red-200 hover:bg-red-50 hover:border-red-300 rounded-lg text-sm font-semibold transition">
                                        Ajukan Keluar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            @endif

            {{-- B. Daftar Kelompok Lain --}}
            <div class="space-y-3">
                @forelse($course->groups as $group)
                    @if($membership->group_id == $group->id) @continue @endif
                    
                    <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition duration-200 relative group">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h5 class="font-bold text-gray-700 text-sm">{{ $group->name }}</h5>
                                @if($group->topic)
                                    <p class="text-[11px] text-gray-500 mt-0.5">Topik: {{ $group->topic->name }}</p>
                                @endif
                            </div>
                            <span class="px-2 py-1 rounded text-xs font-bold {{ $group->isFull() ? 'bg-red-50 text-red-600' : 'bg-blue-50 text-blue-600' }}">
                                {{ $group->students->count() }}/{{ $group->max_slots }}
                            </span>
                        </div>

                        <details class="text-xs text-gray-500 mb-3">
                            <summary class="cursor-pointer hover:text-blue-600 list-none flex items-center gap-1">
                                <i class="fas fa-chevron-right text-[10px] transition-transform group-open:rotate-90"></i>
                                Lihat {{ $group->students->count() }} Anggota
                            </summary>
                            <ul class="mt-2 pl-2 space-y-1 border-l-2 border-gray-100">
                                @foreach($group->students as $student)
                                    <li>{{ $student->full_name }}</li>
                                @endforeach
                                @if($group->students->isEmpty())
                                    <li class="italic opacity-50">Belum ada anggota</li>
                                @endif
                            </ul>
                        </details>

                        @if(!$membership->group_id)
                            @if(!$group->isFull())
                                <form action="{{ route('student.group.join', $group->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full py-1.5 bg-blue-600 text-white rounded text-xs font-semibold hover:bg-blue-700 transition shadow-sm">
                                        Gabung
                                    </button>
                                </form>
                            @else
                                <button disabled class="w-full py-1.5 bg-gray-100 text-gray-400 border border-gray-200 rounded text-xs font-semibold cursor-not-allowed">
                                    Penuh
                                </button>
                            @endif
                        @else
                             <button disabled class="w-full py-1.5 bg-gray-50 text-gray-300 border border-gray-100 rounded text-xs cursor-not-allowed opacity-50">
                                Gabung
                            </button>
                        @endif
                    </div>
                @empty
                    <div class="text-center p-4 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                        <p class="text-sm text-gray-500 italic">Belum ada kelompok.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection