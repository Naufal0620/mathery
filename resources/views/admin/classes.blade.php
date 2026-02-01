@extends('layouts.admin')

@section('title', 'Manajemen Kelas - Mathery')
@section('header_title', 'Manajemen Kelas')

@section('content')
<div class="fade-in">
    
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-8">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Daftar Kelas Aktif</h2>
            <p class="text-sm text-gray-500">Kelola kelas, mahasiswa, dan topik pembelajaran.</p>
        </div>
        <button onclick="openModal('createClassModal')" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-xl shadow-lg shadow-indigo-200 hover:shadow-indigo-300 hover:scale-105 transition-all flex items-center gap-2">
            <i class='bx bx-plus-circle text-xl'></i> Buat Kelas Baru
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($courses as $course)
        <div class="group bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col">
            
            <div class="h-24 bg-gradient-to-r from-indigo-500 to-purple-600 p-5 relative">
                <div class="absolute top-4 right-4">
                    <div class="relative" x-data="{ open: false }">
                        <button onclick="toggleDropdown('dropdown-{{ $course->id }}')" class="text-white/80 hover:text-white transition-colors">
                            <i class='bx bx-dots-vertical-rounded text-2xl'></i>
                        </button>
                        <div id="dropdown-{{ $course->id }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl z-20 border border-gray-100 py-1 text-left origin-top-right transform transition-all">
                            <a href="{{ route('admin.classes.members', $course->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                <i class='bx bx-user mr-2'></i> Anggota Kelas
                            </a>
                            <a href="{{ route('admin.syllabus', ['filter_class' => $course->id]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                <i class='bx bx-book-content mr-2'></i> Lihat Silabus
                            </a>
                            <button onclick="editClass({{ $course->id }}, '{{ explode(' - ', $course->name)[0] }}', '{{ explode(' - ', $course->name)[1] ?? '' }}', '{{ $course->description }}')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600">
                                <i class='bx bx-edit mr-2'></i> Edit Info
                            </button>
                            <div class="border-t border-gray-100 my-1"></div>
                            <form action="{{ route('admin.classes.destroy', $course->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kelas ini? Semua data terkait akan hilang.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <i class='bx bx-trash mr-2'></i> Hapus Kelas
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <h3 class="text-white font-bold text-lg truncate pr-6">{{ $course->name }}</h3>
                <p class="text-indigo-100 text-xs font-medium uppercase tracking-wider opacity-90">{{ $course->code }}</p>
            </div>

            <div class="p-5 flex-1 flex flex-col">
                <p class="text-gray-500 text-sm mb-4 line-clamp-2 min-h-[40px]">
                    {{ $course->description ?? 'Tidak ada deskripsi untuk kelas ini.' }}
                </p>
                
                <div class="grid grid-cols-2 gap-3 mt-auto">
                    <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100">
                        <span class="block text-xl font-bold text-indigo-600">{{ $course->students_count }}</span>
                        <span class="text-xs text-gray-400 font-medium uppercase">Mahasiswa</span>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100">
                        <span class="block text-xl font-bold text-purple-600">{{ $course->topics_count }}</span>
                        <span class="text-xs text-gray-400 font-medium uppercase">Topik</span>
                    </div>
                </div>
            </div>

            <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                <a href="{{ route('admin.classes.members', $course->id) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                    Kelola Anggota <i class='bx bx-right-arrow-alt'></i>
                </a>
                <span class="text-xs text-gray-400" title="Dibuat pada">{{ $course->created_at->format('d M Y') }}</span>
            </div>
        </div>
        @empty
        <div class="col-span-full flex flex-col items-center justify-center py-12 text-center text-gray-400">
            <div class="bg-gray-100 p-4 rounded-full mb-3">
                <i class='bx bx-chalkboard bx-lg text-gray-300'></i>
            </div>
            <p class="text-lg font-medium text-gray-500">Belum ada kelas yang dibuat.</p>
            <p class="text-sm mb-4">Silakan buat kelas baru untuk memulai.</p>
            <button onclick="openModal('createClassModal')" class="text-indigo-600 font-medium hover:underline">Buat Kelas Sekarang</button>
        </div>
        @endforelse
    </div>

    <div id="createClassModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeModal('createClassModal')"></div>
        
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-2xl shadow-2xl p-6 fade-in">
            <div class="flex justify-between items-center mb-5">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-800">Buat Kelas Baru</h3>
                <button onclick="closeModal('createClassModal')" class="text-gray-400 hover:text-red-500 transition-colors">
                    <i class='bx bx-x bx-md'></i>
                </button>
            </div>

            <form id="classForm" action="{{ route('admin.classes.store') }}" method="POST">
                @csrf
                <div id="methodField"></div> <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Mata Kuliah</label>
                        <div class="relative">
                            <i class='bx bx-book absolute left-3 top-3 text-gray-400'></i>
                            <input type="text" name="course_name" id="course_name" placeholder="Contoh: Kalkulus Dasar" required
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas / Kode Seksi</label>
                        <div class="relative">
                            <i class='bx bx-building absolute left-3 top-3 text-gray-400'></i>
                            <input type="text" name="class_name" id="class_name" placeholder="Contoh: Kelas A / Reguler B" required
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                        <textarea name="description" id="description" rows="3" placeholder="Deskripsi singkat tentang kelas ini..."
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all text-sm"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="button" onclick="closeModal('createClassModal')" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-colors">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all">Simpan Kelas</button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function toggleDropdown(id) {
        // Tutup semua dropdown lain dulu
        document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
            if (el.id !== id) el.classList.add('hidden');
        });
        
        const dropdown = document.getElementById(id);
        dropdown.classList.toggle('hidden');
    }

    // Klik di luar dropdown untuk menutup
    window.onclick = function(event) {
        if (!event.target.closest('.relative')) {
            document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
                el.classList.add('hidden');
            });
        }
    }

    function openModal(modalID) {
        document.getElementById(modalID).classList.remove('hidden');
        
        // Reset form ke mode Create
        document.getElementById('modalTitle').innerText = 'Buat Kelas Baru';
        document.getElementById('classForm').action = "{{ route('admin.classes.store') }}";
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('course_name').value = '';
        document.getElementById('class_name').value = '';
        document.getElementById('description').value = '';
    }

    function closeModal(modalID) {
        document.getElementById(modalID).classList.add('hidden');
    }

    function editClass(id, courseName, className, description) {
        openModal('createClassModal');
        
        // Setup mode Edit
        document.getElementById('modalTitle').innerText = 'Edit Kelas';
        let url = "{{ route('admin.classes.update', ':id') }}";
        url = url.replace(':id', id);
        
        document.getElementById('classForm').action = url;
        document.getElementById('methodField').innerHTML = '@method("PUT")';
        
        document.getElementById('course_name').value = courseName;
        document.getElementById('class_name').value = className;
        document.getElementById('description').value = description;
    }
</script>
@endpush
@endsection