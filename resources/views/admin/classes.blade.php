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
        <!-- Card Kelas A -->
        <div class="group bg-white p-6 rounded-2xl border border-gray-200 hover:border-indigo-400 shadow-sm hover:shadow-md transition-all">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl font-bold group-hover:bg-indigo-600 group-hover:text-white transition-colors">A</div>
                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Aktif</span>
            </div>
            <h3 class="font-bold text-gray-800 text-lg">Kalkulus II - Kelas A</h3>
            <p class="text-sm text-gray-500 mb-4">Kode: <span class="font-mono bg-gray-100 px-1 rounded text-gray-800">MTH2024-A</span></p>
            <div class="flex items-center gap-4 text-sm text-gray-500 border-t border-gray-100 pt-4">
                <span class="flex items-center gap-1"><i class='bx bx-user'></i> 42 Mhs</span>
                <span class="flex items-center gap-1"><i class='bx bx-folder'></i> 14 Topik</span>
            </div>
            <div class="flex gap-2 mt-4">
                    <button class="flex-1 px-3 py-2 text-sm font-medium text-gray-600 bg-gray-50 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition"><i class='bx bx-edit-alt'></i> Edit</button>
                    <button class="flex-1 px-3 py-2 text-sm font-medium text-red-500 bg-red-50 hover:bg-red-100 rounded-lg transition"><i class='bx bx-trash'></i> Hapus</button>
            </div>
        </div>

        <!-- Card Kelas B -->
        <div class="group bg-white p-6 rounded-2xl border border-gray-200 hover:border-purple-400 shadow-sm hover:shadow-md transition-all">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-xl font-bold group-hover:bg-purple-600 group-hover:text-white transition-colors">B</div>
                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Aktif</span>
            </div>
            <h3 class="font-bold text-gray-800 text-lg">Aljabar Linear - Kelas B</h3>
            <p class="text-sm text-gray-500 mb-4">Kode: <span class="font-mono bg-gray-100 px-1 rounded text-gray-800">MTH2024-B</span></p>
            <div class="flex items-center gap-4 text-sm text-gray-500 border-t border-gray-100 pt-4">
                <span class="flex items-center gap-1"><i class='bx bx-user'></i> 38 Mhs</span>
                <span class="flex items-center gap-1"><i class='bx bx-folder'></i> 12 Topik</span>
            </div>
                <div class="flex gap-2 mt-4">
                    <button class="flex-1 px-3 py-2 text-sm font-medium text-gray-600 bg-gray-50 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition"><i class='bx bx-edit-alt'></i> Edit</button>
                    <button class="flex-1 px-3 py-2 text-sm font-medium text-red-500 bg-red-50 hover:bg-red-100 rounded-lg transition"><i class='bx bx-trash'></i> Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Class -->
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
        
        <form class="space-y-5" action="#" method="POST">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Mata Kuliah</label>
                <input type="text" name="course_name" placeholder="Contoh: Kalkulus II" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Kelas</label>
                    <input type="text" name="class_name" placeholder="Contoh: Kelas A" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kode (Auto)</label>
                    <input type="text" value="MTH2024-X" disabled class="w-full bg-gray-200 border border-gray-300 rounded-xl px-4 py-3 text-gray-500 font-mono">
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
</script>
@endpush
@endsection