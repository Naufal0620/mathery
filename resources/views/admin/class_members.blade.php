@extends('layouts.admin')

@section('title', 'Anggota Kelas - ' . $course->name)
@section('header_title', 'Manajemen Anggota')

@section('content')
<div class="fade-in">
    <div class="mb-6">
        <a href="{{ route('admin.classes') }}" class="inline-flex items-center gap-1 text-gray-500 hover:text-indigo-600 transition font-medium">
            <i class='bx bx-chevron-left text-xl'></i>Kembali ke Daftar Kelas
        </a>
    </div>

    {{-- Info Kelas Header (Tidak Berubah) --}}
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $course->name }}</h2>
            <p class="text-gray-500 font-mono text-sm mt-1">Kode Kelas: {{ $course->code }}</p>
        </div>
        <div class="flex items-center gap-2 bg-indigo-50 text-indigo-700 px-4 py-2 rounded-xl font-bold">
            <i class='bx bx-user'></i> {{ $course->students->count() }} Mahasiswa
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- KOLOM KIRI: FORM PENCARIAN & HASIL --}}
        <div class="lg:col-span-1">
            {{-- Card dengan border lebih halus (gray-100) dan shadow lembut --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-lg shadow-gray-100/50 sticky top-6 overflow-hidden">
                
                {{-- Header Card Lebih Rapi --}}
                <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                        <i class='bx bx-user-plus text-indigo-500'></i> Tambah Mahasiswa
                    </h3>
                </div>
                
                <div class="p-6">
                    {{-- Form Pencarian --}}
                    <form action="{{ route('admin.classes.members', $course->id) }}" method="GET" class="mb-4">
                        <div class="relative group">
                            <input type="text" name="search" value="{{ request('search') }}" autocomplete="off" placeholder="Ketik Nama atau NIM..." class="w-full bg-white border border-gray-200 text-gray-700 rounded-xl pl-4 pr-10 py-3 text-sm focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition shadow-sm group-hover:border-indigo-300">
                            <button type="submit" class="absolute right-2 top-2 p-1.5 text-gray-400 hover:text-indigo-600 transition">
                                <i class='bx bx-search text-lg'></i>
                            </button>
                        </div>
                    </form>

                    {{-- Logic Tampilan Hasil --}}
                    @if(request('search'))
                        <div class="mb-2 flex justify-between items-end">
                            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Hasil Pencarian</span>
                            <a href="{{ route('admin.classes.members', $course->id) }}" class="text-xs text-red-500 hover:text-red-700 font-medium transition">Reset</a>
                        </div>

                        <div class="overflow-hidden border border-gray-100 rounded-xl bg-white shadow-inner">
                            <div class="max-h-[350px] overflow-y-auto custom-scrollbar">
                                <table class="w-full text-left text-sm">
                                    <tbody class="divide-y divide-gray-50">
                                        @forelse($availableStudents as $student)
                                        <tr class="hover:bg-indigo-50/30 transition group">
                                            <td class="p-3 pl-4">
                                                <p class="font-bold text-gray-700 text-sm group-hover:text-indigo-700 transition">{{ $student->full_name }}</p>
                                                <p class="text-[11px] text-gray-400 font-mono mt-0.5">{{ $student->username }}</p>
                                            </td>
                                            
                                            {{-- Kolom Aksi Fit Content --}}
                                            <td class="p-3 pr-4 w-1 whitespace-nowrap text-right align-middle">
                                                <form action="{{ route('admin.classes.members.store', $course->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $student->id }}">
                                                    
                                                    {{-- Tombol Compact & Fit --}}
                                                    <button type="submit" class="inline-flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 p-2 rounded-lg transition-all shadow-sm active:scale-95" title="Tambahkan">
                                                        <i class='bx bx-plus font-bold text-xs'></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="2" class="p-8 text-center">
                                                <div class="flex flex-col items-center justify-center text-gray-400">
                                                    <i class='bx bx-ghost text-3xl mb-2 opacity-50'></i>
                                                    <p class="text-xs">Tidak ditemukan mahasiswa dengan kata kunci tersebut.</p>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        {{-- State Kosong (Belum Mencari) --}}
                        <div class="text-center py-8 px-4 border-2 border-dashed border-gray-100 rounded-xl bg-gray-50/50">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm text-indigo-400">
                                <i class='bx bx-search-alt text-2xl'></i>
                            </div>
                            <p class="text-sm font-medium text-gray-600">Mulai Pencarian</p>
                            <p class="text-xs text-gray-400 mt-1">Ketik nama atau NIM mahasiswa untuk menambahkannya ke kelas ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: LIST MAHASISWA --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- TABEL 1: PERMINTAAN PENDING (Hanya muncul jika ada request) --}}
            @if($pendingStudents->count() > 0)
            <div class="bg-orange-50 rounded-2xl border border-orange-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-orange-200 flex items-center justify-between bg-orange-100/50">
                    <h3 class="text-sm font-bold text-orange-800 flex items-center gap-2">
                        <i class='bx bx-time-five'></i> Menunggu Persetujuan
                        <span class="bg-orange-500 text-white text-[10px] px-2 py-0.5 rounded-full">{{ $pendingStudents->count() }}</span>
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <tbody class="divide-y divide-orange-200/50">
                            @foreach($pendingStudents as $student)
                            <tr class="hover:bg-orange-100/50 transition">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-orange-200 text-orange-700 flex items-center justify-center font-bold text-xs">
                                            {{ substr($student->full_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800 text-sm">{{ $student->full_name }}</p>
                                            <p class="text-xs text-gray-500 font-mono">{{ $student->username }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Form Approve --}}
                                        <form action="{{ route('admin.classes.members.approve', ['id' => $course->id, 'student_id' => $student->id]) }}" method="POST">
                                            @csrf
                                            @method('PUT') {{-- Gunakan PUT untuk update --}}
                                            <button type="submit" class="bg-green-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-green-600 transition shadow-sm flex items-center gap-1">
                                                <i class='bx bx-check'></i> Terima
                                            </button>
                                        </form>

                                        {{-- Form Reject --}}
                                        <form action="{{ route('admin.classes.members.destroy', ['id' => $course->id, 'student_id' => $student->id]) }}" method="POST" onsubmit="return confirm('Tolak permintaan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-white border border-red-200 text-red-500 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-50 transition flex items-center gap-1">
                                                <i class='bx bx-x'></i> Tolak
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

            {{-- TABEL 2: ANGGOTA AKTIF (Accepted) --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">Daftar Mahasiswa Aktif</h3>
                    <span class="text-xs bg-indigo-50 text-indigo-600 px-2 py-1 rounded-lg font-bold">{{ $activeStudents->count() }} Siswa</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase text-gray-500 font-semibold">
                                <th class="p-4">Nama Mahasiswa</th>
                                <th class="p-4">NIM</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($activeStudents as $student)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs">
                                            {{ substr($student->full_name, 0, 1) }}
                                        </div>
                                        <span class="font-medium text-gray-700">{{ $student->full_name }}</span>
                                    </div>
                                </td>
                                <td class="p-4 font-mono text-sm text-gray-500">{{ $student->username }}</td>
                                <td class="p-4 text-center">
                                    {{-- Tombol Keluarkan (Kick) --}}
                                    <button onclick="confirmRemove('{{ $student->id }}', '{{ $student->full_name }}')" class="text-gray-400 hover:text-red-500 transition" title="Keluarkan">
                                        <i class='bx bx-trash text-lg'></i>
                                    </button>
                                    <form id="remove-form-{{ $student->id }}" action="{{ route('admin.classes.members.destroy', ['id' => $course->id, 'student_id' => $student->id]) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="p-8 text-center text-gray-400">Belum ada mahasiswa aktif.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</div>

@push('scripts')
{{-- Script SweetAlert (Tidak Berubah) --}}
<script>
    function confirmRemove(studentId, studentName) {
        Swal.fire({
            title: 'Keluarkan Mahasiswa?',
            text: "Apakah Anda yakin ingin mengeluarkan \"" + studentName + "\" dari kelas ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Keluarkan',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl px-4 py-2',
                cancelButton: 'rounded-xl px-4 py-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('remove-form-' + studentId).submit();
            }
        })
    }
</script>
@endpush
@endsection