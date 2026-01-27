@extends('layouts.admin')

@section('title', 'Anggota Kelas - ' . $course->name)

@section('content')
<div class="container-fluid px-4">
    
    {{-- HEADER & TABS --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $course->name }}</h1>
                <p class="text-gray-500 text-sm mb-4">Kode Kelas: {{ $course->code }}</p>
            </div>
            <a href="{{ route('admin.classes') }}" class="text-gray-500 hover:text-blue-600 text-sm font-medium transition flex items-center gap-1">
                <i class='bx bx-arrow-back text-lg'></i> Kembali
            </a>
        </div>

        {{-- Tab Navigation --}}
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <a href="{{ route('admin.classes.members', $course->id) }}" 
                   class="border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                   <i class='bx bx-group mr-2 text-lg'></i> Anggota Kelas
                </a>

                <a href="{{ route('admin.classes.groups', $course->id) }}" 
                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                   <i class='bx bx-layer mr-2 text-lg'></i> Manajemen Kelompok
                </a>
            </nav>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- KOLOM KIRI: FORM PENCARIAN & HASIL --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 sticky top-6 overflow-hidden">
                
                <div class="p-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class='bx bx-user-plus text-blue-500 text-xl'></i> Tambah Mahasiswa
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Cari dan tambahkan mahasiswa ke kelas ini.</p>
                </div>
                
                <div class="p-5">
                    {{-- Form Pencarian --}}
                    <form action="{{ route('admin.classes.members', $course->id) }}" method="GET" class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" autocomplete="off" placeholder="Nama atau NIM..." 
                            class="w-full bg-white border border-gray-300 text-gray-700 rounded-lg pl-10 pr-8 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class='bx bx-search text-gray-400 text-lg'></i>
                        </div>
                        @if(request('search'))
                            <a href="{{ route('admin.classes.members', $course->id) }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-500 cursor-pointer">
                                <i class='bx bx-x-circle text-lg'></i>
                            </a>
                        @endif
                    </form>

                    {{-- Hasil Pencarian --}}
                    <div class="mt-4">
                        @if(request('search'))
                            <div class="flex justify-between items-end mb-2">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Hasil Pencarian</span>
                            </div>

                            <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
                                <div class="max-h-[350px] overflow-y-auto custom-scrollbar">
                                    @forelse($availableStudents as $student)
                                        <div class="flex items-center justify-between p-3 border-b border-gray-100 last:border-0 hover:bg-blue-50 transition group">
                                            <div class="flex items-center gap-3 overflow-hidden">
                                                <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-xs font-bold shrink-0">
                                                    {{ substr($student->full_name, 0, 1) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-semibold text-gray-700 truncate group-hover:text-blue-700">{{ $student->full_name }}</p>
                                                    <p class="text-[10px] text-gray-500 font-mono">{{ $student->username }}</p>
                                                </div>
                                            </div>
                                            
                                            <form action="{{ route('admin.classes.members.store', $course->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $student->id }}">
                                                <button type="submit" class="w-7 h-7 flex items-center justify-center rounded-full bg-white border border-blue-200 text-blue-600 hover:bg-blue-600 hover:text-white transition shadow-sm" title="Tambahkan">
                                                    <i class='bx bx-plus text-lg'></i>
                                                </button>
                                            </form>
                                        </div>
                                    @empty
                                        <div class="p-6 text-center text-gray-400">
                                            <i class='bx bx-search-alt text-3xl mb-2 opacity-50'></i>
                                            <p class="text-xs">Tidak ditemukan.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @else
                            {{-- State Kosong --}}
                            <div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-lg bg-gray-50">
                                <p class="text-xs text-gray-400">Silakan cari mahasiswa untuk ditambahkan.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: LIST MAHASISWA --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- TABEL 1: PERMINTAAN PENDING --}}
            @if($pendingStudents->count() > 0)
            <div class="bg-orange-50 rounded-xl border border-orange-200 shadow-sm overflow-hidden animate-fade-in-down">
                <div class="px-5 py-3 border-b border-orange-200 flex items-center justify-between bg-orange-100/50">
                    <h3 class="text-sm font-bold text-orange-800 flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></div>
                        Menunggu Persetujuan
                        <span class="bg-orange-500 text-white text-[10px] px-2 py-0.5 rounded-full">{{ $pendingStudents->count() }}</span>
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <tbody class="divide-y divide-orange-200/50">
                            @foreach($pendingStudents as $student)
                            <tr class="hover:bg-orange-100/40 transition">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-orange-200 text-orange-800 flex items-center justify-center font-bold text-sm shadow-sm border-2 border-white">
                                            {{ substr($student->full_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800 text-sm">{{ $student->full_name }}</p>
                                            <p class="text-xs text-gray-500 font-mono">{{ $student->username }} &bull; {{ $student->pivot->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <form action="{{ route('admin.classes.members.approve', ['id' => $course->id, 'student_id' => $student->id]) }}" method="POST">
                                            @csrf @method('PUT')
                                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white w-8 h-8 rounded-lg shadow-sm flex items-center justify-center transition" title="Terima">
                                                <i class='bx bx-check text-lg'></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.classes.members.destroy', ['id' => $course->id, 'student_id' => $student->id]) }}" method="POST" onsubmit="return confirm('Tolak permintaan ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="bg-white border border-red-200 text-red-500 hover:bg-red-50 w-8 h-8 rounded-lg flex items-center justify-center transition" title="Tolak">
                                                <i class='bx bx-x text-lg'></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- TABEL 2: ANGGOTA AKTIF --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/30">
                    <h3 class="font-bold text-gray-800">Daftar Mahasiswa Aktif</h3>
                    <span class="text-xs bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full font-semibold border border-gray-200">
                        Total: {{ $activeStudents->count() }}
                    </span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 font-semibold tracking-wide">
                                <th class="px-6 py-3">Mahasiswa</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($activeStudents as $student)
                            <tr class="hover:bg-gray-50 transition group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">
                                            {{ substr($student->full_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800 text-sm">{{ $student->full_name }}</p>
                                            <p class="text-[11px] text-gray-400 font-mono">{{ $student->username }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($student->pivot->group_id)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                            Punya Kelompok
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                            Tanpa Kelompok
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button onclick="confirmRemove('{{ $student->id }}', '{{ $student->full_name }}')" 
                                        class="text-gray-400 hover:text-red-600 p-1.5 rounded-full hover:bg-red-50 transition" title="Keluarkan dari Kelas">
                                        <i class='bx bx-trash text-lg'></i>
                                    </button>
                                    <form id="remove-form-{{ $student->id }}" action="{{ route('admin.classes.members.destroy', ['id' => $course->id, 'student_id' => $student->id]) }}" method="POST" class="hidden">
                                        @csrf @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                            <i class='bx bx-user-x text-2xl opacity-50'></i>
                                        </div>
                                        <p class="text-sm font-medium">Belum ada mahasiswa aktif.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- CSS Custom Scrollbar untuk Hasil Pencarian --}}
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #d1d5db; 
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #9ca3af; 
    }
</style>

@push('scripts')
<script>
    function confirmRemove(studentId, studentName) {
        // Cek apakah SweetAlert (Swal) tersedia
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Keluarkan Mahasiswa?',
                html: "Anda akan mengeluarkan <b>" + studentName + "</b> dari kelas ini.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Ya, Keluarkan',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('remove-form-' + studentId).submit();
                }
            })
        } else {
            // Fallback jika SweetAlert tidak ada
            if(confirm("Apakah Anda yakin ingin mengeluarkan " + studentName + " dari kelas ini?")) {
                document.getElementById('remove-form-' + studentId).submit();
            }
        }
    }
</script>
@endpush
@endsection