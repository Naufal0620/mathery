@extends('layouts.admin')

@section('title', 'Manajemen Kelas - Mathery')
@section('header_title', 'Manajemen Kelas')

@section('content')
<div class="fade-in">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <p class="text-gray-500 text-sm">Kelola data kelas yang aktif pada semester ini.</p>
        <button onclick="toggleModal('modalAddClass')" class="w-full sm:w-auto bg-gradient-to-r from-indigo-700 to-purple-600 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-indigo-200 hover:shadow-none hover:opacity-90 transition-all flex items-center justify-center gap-2">
            <i class='bx bx-plus'></i> Buat Kelas Baru
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        
        {{-- LOOPING DATA KELAS DARI DATABASE --}}
        @forelse($courses as $course)
        <div class="group bg-white p-6 rounded-2xl border border-gray-200 hover:border-indigo-400 shadow-sm hover:shadow-md transition-all">
            <div class="flex justify-between items-start mb-4">
                {{-- Ambil huruf pertama nama kelas untuk ikon --}}
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl font-bold group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                    {{ substr($course->name, 0, 1) }}
                </div>
                <span class="{{ $course->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} px-3 py-1 rounded-full text-xs font-bold">
                    {{ $course->is_active ? 'Aktif' : 'Non-Aktif' }}
                </span>
            </div>
            
            <h3 class="font-bold text-gray-800 text-lg">{{ $course->name }}</h3>
            <p class="text-sm text-gray-500 mb-4">Kode: <span class="font-mono bg-gray-100 px-1 rounded text-gray-800">{{ $course->code }}</span></p>
            
            <div class="flex items-center gap-4 text-sm text-gray-500 border-t border-gray-100 pt-4">
                {{-- Menggunakan withCount 'students' yang kita definisikan di Controller --}}
                <span class="flex items-center gap-1"><i class='bx bx-user'></i> {{ $course->students_count ?? 0 }} Mhs</span>
                <span class="flex items-center gap-1"><i class='bx bx-folder'></i> - Topik</span>
            </div>
            
            <div class="flex flex-col gap-2 mt-4">
                {{-- Tombol Edit dengan Data Attributes --}}
                {{-- Kita pecah string "Kalkulus II - Kelas A" menjadi dua bagian untuk diumpan ke form --}}
                @php
                    $parts = explode(' - ', $course->name, 2);
                    $courseNameOnly = $parts[0] ?? $course->name;
                    $classNameOnly = $parts[1] ?? '';
                @endphp

                <a href="{{ route('admin.classes.members', $course->id) }}" class="w-full py-2 text-sm font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-600 hover:text-white rounded-lg transition text-center flex items-center justify-center gap-2">
                    <i class='bx bx-group'></i> Atur Anggota
                </a>

                <div class="flex gap-2">
                    <button 
                        onclick="openEditModal('{{ $course->id }}', '{{ $courseNameOnly }}', '{{ $classNameOnly }}', '{{ $course->description }}')"
                        class="flex-1 px-3 py-2 text-sm font-medium text-gray-600 bg-gray-50 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition">
                        <i class='bx bx-edit-alt'></i> Edit
                    </button>
                    
                    {{-- Form Delete --}}
                    <form action="{{ route('admin.classes.destroy', $course->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Yakin ingin menghapus kelas ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-3 py-2 text-sm font-medium text-red-500 bg-red-50 hover:bg-red-100 rounded-lg transition">
                            <i class='bx bx-trash'></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-1 md:col-span-2 text-center py-10 text-gray-400">
            <i class='bx bx-ghost text-4xl mb-2'></i>
            <p>Belum ada kelas yang dibuat.</p>
        </div>
        @endforelse

    </div>
</div>

<div id="modalAddClass" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-8 w-full max-w-lg transform scale-100 transition-all max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-800">Buat Kelas Baru</h3>
                <p class="text-sm text-gray-500">Isi detail kelas untuk semester ini</p>
            </div>
            <button onclick="toggleModal('modalAddClass')" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 transition">
                <i class='bx bx-x bx-sm'></i>
            </button>
        </div>
        
        {{-- UPDATE ACTION FORM DI SINI --}}
        <form class="space-y-5" action="{{ route('admin.classes.store') }}" method="POST">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Mata Kuliah</label>
                <input type="text" name="course_name" required placeholder="Contoh: Kalkulus II" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Kelas</label>
                    <input type="text" name="class_name" required placeholder="Contoh: Kelas A" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kode (Auto)</label>
                    <input type="text" value="Auto-Generated" disabled class="w-full bg-gray-200 border border-gray-300 rounded-xl px-4 py-3 text-gray-500 font-mono">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                <textarea rows="3" name="description" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"></textarea>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modalAddClass')" class="px-6 py-3 text-gray-600 hover:bg-gray-100 rounded-xl font-medium transition">Batal</button>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-700 to-purple-600 text-white rounded-xl font-medium shadow-lg shadow-indigo-200 hover:shadow-none transition">Simpan Kelas</button>
            </div>
        </form>
    </div>
</div>

<div id="modalEditClass" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-8 w-full max-w-lg transform scale-100 transition-all max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-800">Edit Kelas</h3>
                <p class="text-sm text-gray-500">Perbarui informasi kelas</p>
            </div>
            <button onclick="toggleModal('modalEditClass')" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 transition">
                <i class='bx bx-x bx-sm'></i>
            </button>
        </div>
        
        {{-- Form Update --}}
        <form id="formEditClass" action="#" method="POST" class="space-y-5">
            @csrf
            @method('PUT') {{-- Method Spoofing untuk PUT --}}
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Mata Kuliah</label>
                <input type="text" id="edit_course_name" name="course_name" required class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Kelas</label>
                <input type="text" id="edit_class_name" name="class_name" required class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                <textarea rows="3" id="edit_description" name="description" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"></textarea>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modalEditClass')" class="px-6 py-3 text-gray-600 hover:bg-gray-100 rounded-xl font-medium transition">Batal</button>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-medium shadow-lg shadow-orange-200 hover:shadow-none transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Fungsi Toggle Modal (Bisa dipakai untuk Add maupun Edit)
    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
        } else {
            modal.classList.add('hidden');
        }
    }

    // Fungsi Khusus untuk Membuka Modal Edit dan Mengisi Data
    function openEditModal(id, courseName, className, description) {
        // 1. Set Action URL pada Form agar mengarah ke ID yang benar
        // Kita buat URL template lalu replace string 'ID_PLACEHOLDER' dengan ID asli
        let urlTemplate = "{{ route('admin.classes.update', 'ID_PLACEHOLDER') }}";
        let finalUrl = urlTemplate.replace('ID_PLACEHOLDER', id);
        
        document.getElementById('formEditClass').action = finalUrl;

        // 2. Isi Value Input
        document.getElementById('edit_course_name').value = courseName;
        document.getElementById('edit_class_name').value = className;
        document.getElementById('edit_description').value = description;

        // 3. Tampilkan Modal
        toggleModal('modalEditClass');
    }
</script>
@endpush
@endsection