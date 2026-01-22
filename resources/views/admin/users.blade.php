@extends('layouts.admin')

@section('title', 'Data Mahasiswa - Mathery')
@section('header_title', 'Data Mahasiswa')

@section('content')
<div class="fade-in">
    {{-- Header Actions --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        
        {{-- Search & Filter Group --}}
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            {{-- Filter Kelas (Optional Logic) --}}
            <form action="{{ route('admin.users') }}" method="GET" class="flex flex-col sm:flex-row gap-3 w-full">
                
                {{-- Dropdown Filter --}}
                <div class="relative w-full sm:w-48">
                    <select name="filter_class" onchange="this.form.submit()" class="w-full bg-white border border-gray-300 text-gray-700 rounded-xl px-4 py-2.5 appearance-none focus:ring-2 focus:ring-indigo-500/20 outline-none">
                        <option value="">Semua Kelas</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('filter_class') == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                    <i class='bx bx-chevron-down absolute right-4 top-3 text-gray-400'></i>
                </div>

                {{-- Search Bar --}}
                <div class="relative w-full sm:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / NIM..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500/20 outline-none">
                    <i class='bx bx-search absolute left-3 top-3 text-gray-400'></i>
                </div>
                
                {{-- Tombol Cari Mobile (Optional, tekan enter di input juga bisa) --}}
                <button type="submit" class="hidden sm:block px-4 py-2 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition">
                    <i class='bx bx-check'></i>
                </button>
            </form>
        </div>

        {{-- Add Button --}}
        <button onclick="toggleModal('modalAddUser')" class="w-full md:w-auto bg-gradient-to-r from-indigo-700 to-purple-600 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-indigo-200 hover:shadow-none hover:opacity-90 transition-all flex items-center justify-center gap-2">
            <i class='bx bx-user-plus'></i> Tambah Mahasiswa
        </button>
    </div>

    {{-- Tabel Users --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase text-gray-500 font-semibold">
                        <th class="p-4">Mahasiswa</th>
                        <th class="p-4">NIM / Username</th>
                        <th class="p-4">Email</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    @forelse($students as $student)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                {{-- Avatar Initials --}}
                                <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                                    {{ substr($student->full_name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">{{ $student->full_name }}</p>
                                    <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold">Student</span>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 font-mono text-gray-600">
                            {{ $student->username }}
                        </td>
                        <td class="p-4 text-gray-500">
                            {{ $student->email }}
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button 
                                    onclick="openEditModal('{{ $student->id }}', '{{ $student->full_name }}', '{{ $student->username }}', '{{ $student->email }}')"
                                    class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-600 rounded-lg hover:bg-orange-50 hover:text-orange-500 transition">
                                    <i class='bx bx-pencil'></i>
                                </button>
                                
                                <button type="button" onclick="confirmDelete('{{ $student->id }}', '{{ $student->full_name }}')" class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-600 rounded-lg hover:bg-red-50 hover:text-red-500 transition">
                                    <i class='bx bx-trash'></i>
                                </button>
                                
                                <form id="delete-form-{{ $student->id }}" action="{{ route('admin.users.destroy', $student->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-400">
                            <i class='bx bx-user-x text-4xl mb-2'></i>
                            <p>Data mahasiswa tidak ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalAddUser" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-8 w-full max-w-lg transform scale-100 transition-all max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-gray-800">Tambah Mahasiswa</h3>
            <button onclick="toggleModal('modalAddUser')" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 transition">
                <i class='bx bx-x bx-sm'></i>
            </button>
        </div>
        
        <form class="space-y-4" action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" name="full_name" required placeholder="Nama Lengkap" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">NIM (Username)</label>
                    <input type="text" name="username" required placeholder="Contoh: 123456" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" required placeholder="******" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input type="email" name="email" required placeholder="email@mahasiswa.com" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modalAddUser')" class="px-6 py-3 text-gray-600 hover:bg-gray-100 rounded-xl font-medium transition">Batal</button>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-700 to-purple-600 text-white rounded-xl font-medium shadow-lg hover:shadow-none transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="modalEditUser" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-8 w-full max-w-lg transform scale-100 transition-all max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-gray-800">Edit Mahasiswa</h3>
            <button onclick="toggleModal('modalEditUser')" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 transition">
                <i class='bx bx-x bx-sm'></i>
            </button>
        </div>
        
        <form id="formEditUser" action="#" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" id="edit_full_name" name="full_name" required class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">NIM (Username)</label>
                <input type="text" id="edit_username" name="username" required class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input type="email" id="edit_email" name="email" required class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
            </div>
            
            <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100">
                <label class="block text-sm font-semibold text-yellow-800 mb-2">Ubah Password (Opsional)</label>
                <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah" class="w-full bg-white border border-yellow-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-yellow-500/20 outline-none transition text-sm">
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modalEditUser')" class="px-6 py-3 text-gray-600 hover:bg-gray-100 rounded-xl font-medium transition">Batal</button>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-medium shadow-lg hover:shadow-none transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
        } else {
            modal.classList.add('hidden');
        }
    }

    function openEditModal(id, fullName, username, email) {
        let urlTemplate = "{{ route('admin.users.update', 'ID_PLACEHOLDER') }}";
        document.getElementById('formEditUser').action = urlTemplate.replace('ID_PLACEHOLDER', id);
        
        document.getElementById('edit_full_name').value = fullName;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_email').value = email;
        
        toggleModal('modalEditUser');
    }

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Mahasiswa?',
            text: "Akun \"" + name + "\" akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl px-4 py-2',
                cancelButton: 'rounded-xl px-4 py-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endpush
@endsection