@extends('layouts.admin')

@section('title', 'Anggota Kelas - ' . $course->name)
@section('header_title', 'Detail Kelas')

@section('content')
<div class="fade-in space-y-6">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('admin.classes') }}" class="hover:text-indigo-600 transition-colors">Manajemen Kelas</a>
                <i class='bx bx-chevron-right'></i>
                <span>Anggota</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $course->name }}</h2>
            <span class="inline-block bg-indigo-100 text-indigo-700 text-xs px-2 py-1 rounded-md font-semibold mt-1">
                {{ $course->code }}
            </span>
        </div>
        
        <div class="flex flex-wrap gap-3">
            @if($pendingStudents->isNotEmpty())
            <button onclick="showPendingRequests()" class="px-4 py-2 bg-orange-100 text-orange-600 text-sm font-bold rounded-xl border border-orange-200 hover:bg-orange-200 transition-all flex items-center gap-2 animate-pulse-slow">
                <i class='bx bx-bell'></i>
                <span>{{ $pendingStudents->count() }} Permintaan</span>
            </button>
            @endif

            <a href="{{ route('admin.classes.groups', $course->id) }}" class="px-4 py-2 bg-white text-indigo-600 text-sm font-medium rounded-xl border border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700 transition-all flex items-center gap-2">
                <i class='bx bx-group text-lg'></i> Kelola Kelompok
            </a>
            
            <button onclick="openModal('addMemberModal')" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:-translate-y-0.5 transition-all flex items-center gap-2">
                <i class='bx bx-user-plus text-lg'></i> Tambah Anggota
            </button>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
            <h3 class="font-bold text-gray-800 text-lg">Daftar Mahasiswa Aktif <span class="text-gray-400 font-normal text-sm">({{ $activeStudents->count() }})</span></h3>
            
            <div class="relative w-full sm:w-64">
                <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400'></i>
                <input type="text" id="tableSearch" placeholder="Cari nama atau NIM..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/50 text-gray-500 text-xs uppercase font-semibold tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Mahasiswa</th>
                        <th class="px-6 py-4 hidden md:table-cell">Email</th>
                        <th class="px-6 py-4 hidden sm:table-cell">Tanggal Bergabung</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm" id="studentsTableBody">
                    @forelse($activeStudents as $student)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 p-[2px]">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($student->full_name) }}&background=random" class="w-full h-full rounded-full object-cover border-2 border-white">
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $student->full_name }}</p>
                                    <p class="text-xs text-gray-500 font-mono">{{ $student->username }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell text-gray-600">
                            {{ $student->email }}
                        </td>
                        <td class="px-6 py-4 hidden sm:table-cell text-gray-500">
                            <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">
                                <i class='bx bx-calendar'></i>
                                {{ $student->pivot->created_at ? $student->pivot->created_at->format('d M Y') : '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            {{-- REVISI: Menggunakan SweetAlert2 untuk konfirmasi delete --}}
                            <form action="{{ route('admin.classes.members.destroy', [$course->id, $student->id]) }}" method="POST" onsubmit="return deleteConfirm(event)">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors p-2 rounded-lg hover:bg-red-50" title="Keluarkan">
                                    <i class='bx bx-trash text-lg'></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i class='bx bx-user-x bx-lg text-gray-300'></i>
                                <p>Belum ada mahasiswa aktif di kelas ini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="addMemberModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeModal('addMemberModal')"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-2xl shadow-2xl p-6 fade-in">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-xl font-bold text-gray-800">Tambah Anggota Manual</h3>
                <button onclick="closeModal('addMemberModal')" class="text-gray-400 hover:text-red-500 transition-colors">
                    <i class='bx bx-x bx-md'></i>
                </button>
            </div>
            <form action="{{ route('admin.classes.members.store', $course->id) }}" method="POST">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Mahasiswa</label>
                    <div class="relative">
                        <i class='bx bx-search absolute left-3 top-3 text-gray-400'></i>
                        <select name="user_id" required class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all text-sm appearance-none bg-white">
                            <option value="" disabled selected>-- Pilih atau Cari Nama --</option>
                            @foreach($availableStudents as $student)
                                <option value="{{ $student->id }}">{{ $student->full_name }} ({{ $student->username }})</option>
                            @endforeach
                        </select>
                        <i class='bx bx-chevron-down absolute right-3 top-3 text-gray-400 pointer-events-none'></i>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeModal('addMemberModal')" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-colors">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all">Tambahkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    document.getElementById('tableSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#studentsTableBody tr');
        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });

    // REVISI: Fungsi Delete Confirm SweetAlert2
    function deleteConfirm(event) {
        event.preventDefault(); // Mencegah form submit langsung
        var form = event.target; // Ambil elemen form yang di-submit

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Mahasiswa akan dikeluarkan dari kelas ini.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Keluarkan!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); // Submit form manual jika user klik 'Ya'
            }
        });
    }

    // Fungsi Show Pending Requests (Sama seperti sebelumnya)
    function showPendingRequests() {
        let htmlContent = `
            <div class="flex flex-col gap-3 text-left">
                @foreach($pendingStudents as $student)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-200">
                    <div class="flex items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($student->full_name) }}&background=random" class="w-8 h-8 rounded-full">
                        <div class="text-sm">
                            <p class="font-bold text-gray-800">{{ $student->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $student->username }}</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <form action="{{ route('admin.classes.members.approve', [$course->id, $student->id]) }}" method="POST">
                            @csrf @method('PUT')
                            <button type="submit" class="px-3 py-1.5 bg-green-500 text-white text-xs font-bold rounded-lg hover:bg-green-600 transition-colors">Terima</button>
                        </form>
                        <form action="{{ route('admin.classes.members.destroy', [$course->id, $student->id]) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 bg-gray-200 text-gray-600 text-xs font-bold rounded-lg hover:bg-gray-300 transition-colors">Tolak</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        `;
        Swal.fire({
            title: 'Permintaan Bergabung',
            html: htmlContent,
            showConfirmButton: false,
            showCloseButton: true,
            width: '500px',
            customClass: { popup: 'rounded-2xl' }
        });
    }
</script>
@endpush
@endsection