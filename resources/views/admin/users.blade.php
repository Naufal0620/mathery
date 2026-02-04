@extends('layouts.admin')

@section('title', 'Data Mahasiswa - Mathery')
@section('header_title', 'Data Mahasiswa')

@section('content')
<div class="fade-in space-y-6">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        
        <div class="w-full md:w-auto flex flex-col gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800 mb-1">Daftar Mahasiswa</h2>
                <p class="text-sm text-gray-500">Kelola akun mahasiswa yang terdaftar di sistem.</p>
            </div>

            <form id="searchForm" action="{{ route('admin.users') }}" method="GET" class="flex flex-col sm:flex-row gap-3 w-full">
                <div class="relative w-full sm:w-48">
                    <i class='bx bx-filter absolute left-3 top-1/2 -translate-y-1/2 text-gray-400'></i>
                    <select name="filter_class" onchange="this.form.submit()" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer hover:bg-gray-100">
                        <option value="">Semua Kelas</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('filter_class') == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="relative w-full sm:w-64">
                    <input type="text" id="searchInput" name="search" value="{{ request('search') }}" autocomplete="off" placeholder="Cari Nama / NIM..." 
                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                    
                    <div id="searchIcon" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 transition-all">
                        <i class='bx bx-search'></i>
                    </div>
                    <div id="loadingIcon" class="absolute left-3 top-1/2 -translate-y-1/2 text-indigo-500 hidden transition-all">
                        <i class='bx bx-loader-dots bx-spin'></i>
                    </div>
                </div>
            </form>
        </div>

        <button onclick="openModal('modalAddUser')" class="w-full md:w-auto px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
            <i class='bx bx-user-plus text-xl'></i> Tambah Mahasiswa
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase text-gray-500 font-semibold tracking-wider">
                        <th class="px-6 py-4">Mahasiswa</th>
                        <th class="px-6 py-4">NIM / Username</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($students as $student)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 text-indigo-600 flex items-center justify-center font-bold shadow-sm">
                                    {{ substr($student->full_name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">{{ $student->full_name }}</p>
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700 mt-0.5">Active Student</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-mono text-gray-600">
                            {{ $student->username }}
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            {{ $student->email }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                <button onclick="openEditModal('{{ $student->id }}', '{{ $student->full_name }}', '{{ $student->username }}', '{{ $student->email }}')"
                                    class="p-2 bg-gray-50 text-gray-500 hover:bg-orange-50 hover:text-orange-600 rounded-lg transition-colors" title="Edit">
                                    <i class='bx bx-edit-alt text-lg'></i>
                                </button>
                                
                                <form id="delete-form-{{ $student->id }}" action="{{ route('admin.users.destroy', $student->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete('{{ $student->id }}', '{{ $student->full_name }}')" class="p-2 bg-gray-50 text-gray-500 hover:bg-red-50 hover:text-red-600 rounded-lg transition-colors" title="Hapus">
                                        <i class='bx bx-trash text-lg'></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i class='bx bx-search-alt text-4xl text-gray-300'></i>
                                <p>Data mahasiswa tidak ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($students->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $students->links() }}
        </div>
        @endif
    </div>
</div>

<div id="modalAddUser" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto" onclick="closeModal('modalAddUser')">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 w-full max-w-lg p-6 fade-in" onclick="event.stopPropagation()">
                
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-xl font-bold text-gray-800">Tambah Mahasiswa</h3>
                    <button onclick="closeModal('modalAddUser')" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>
                
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="full_name" required placeholder="Nama Lengkap" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none text-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIM (Username)</label>
                                <input type="text" name="username" required placeholder="123456" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                <input type="password" name="password" required placeholder="******" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" required placeholder="email@mahasiswa.com" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none text-sm">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeModal('modalAddUser')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 text-sm font-medium">Batal</button>
                        <button type="submit" class="px-4 py-2 text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 text-sm font-medium shadow-lg shadow-indigo-200">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="modalEditUser" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto" onclick="closeModal('modalEditUser')">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 w-full max-w-lg p-6 fade-in" onclick="event.stopPropagation()">
                
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-xl font-bold text-gray-800">Edit Mahasiswa</h3>
                    <button onclick="closeModal('modalEditUser')" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>

                <form id="formEditUser" action="#" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" id="edit_full_name" name="full_name" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIM (Username)</label>
                            <input type="text" id="edit_username" name="username" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="edit_email" name="email" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none text-sm">
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100">
                            <label class="block text-xs font-bold text-yellow-700 uppercase tracking-wide mb-1">Ubah Password</label>
                            <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah" class="w-full px-4 py-2 rounded-lg border border-yellow-200 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-200 outline-none text-sm bg-white">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeModal('modalEditUser')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 text-sm font-medium">Batal</button>
                        <button type="submit" class="px-4 py-2 text-white bg-orange-500 rounded-xl hover:bg-orange-600 text-sm font-medium shadow-lg shadow-orange-200">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // --- Logic Open/Close Modal (Revisi) ---
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    function openEditModal(id, fullName, username, email) {
        let urlTemplate = "{{ route('admin.users.update', ':id') }}";
        document.getElementById('formEditUser').action = urlTemplate.replace(':id', id);
        document.getElementById('edit_full_name').value = fullName;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_email').value = email;
        openModal('modalEditUser');
    }

    // --- Logic Confirm Delete SweetAlert ---
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Mahasiswa?',
            text: "Akun \"" + name + "\" akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }

    // --- Logic AUTO SEARCH (Debounce) ---
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    const searchIcon = document.getElementById('searchIcon');
    const loadingIcon = document.getElementById('loadingIcon');
    let timeout = null;

    if(searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            searchIcon.classList.add('hidden');
            loadingIcon.classList.remove('hidden');
            timeout = setTimeout(() => {
                searchForm.submit();
            }, 800);
        });

        // Focus Retention
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('search')) {
                searchInput.focus();
                const val = searchInput.value;
                searchInput.value = '';
                searchInput.value = val;
            }
        });
    }
</script>
@endpush
@endsection