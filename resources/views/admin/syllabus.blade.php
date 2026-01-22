@extends('layouts.admin')

@section('title', 'Jadwal & Topik - Mathery')
@section('header_title', 'Jadwal & Topik Perkuliahan')

@section('content')
<div class="fade-in">
    {{-- Header & Actions --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        
        {{-- Filter Dropdown --}}
        <div class="w-full md:w-auto">
            <form action="{{ route('admin.syllabus') }}" method="GET" class="flex items-center gap-2">
                <div class="relative w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class='bx bx-filter-alt text-gray-400'></i>
                    </div>
                    <select name="filter_class" onchange="this.form.submit()" class="pr-10 w-full bg-white border border-gray-300 text-gray-700 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition appearance-none cursor-pointer">
                        <option value="">Semua Kelas</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('filter_class') == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i class='bx bx-chevron-down text-gray-400'></i>
                    </div>
                </div>
            </form>
        </div>

        {{-- Button Tambah --}}
        <button onclick="toggleModal('modalAddTopic')" class="w-full md:w-auto bg-gradient-to-r from-indigo-700 to-purple-600 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-indigo-200 hover:shadow-none hover:opacity-90 transition-all flex items-center justify-center gap-2">
            <i class='bx bx-calendar-plus'></i> Tambah Topik Baru
        </button>
    </div>

    {{-- Tabel Topik --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase text-gray-500 font-semibold">
                        <th class="p-4">Tanggal</th>
                        <th class="p-4">Mata Kuliah / Kelas</th>
                        <th class="p-4">Topik Pertemuan</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    @forelse($topics as $topic)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="p-4 whitespace-nowrap font-mono text-indigo-600">
                            {{ \Carbon\Carbon::parse($topic->meeting_date)->format('d M Y') }}
                        </td>
                        <td class="p-4">
                            <span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded-md text-xs font-bold">
                                {{ $topic->course->name ?? 'Kelas Terhapus' }}
                            </span>
                        </td>
                        <td class="p-4">
                            <p class="font-bold text-gray-800">{{ $topic->name }}</p>
                            <p class="text-gray-500 text-xs truncate max-w-xs">{{ $topic->description }}</p>
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Tombol Edit --}}
                                <button 
                                    onclick="openEditModal('{{ $topic->id }}', '{{ $topic->class_id }}', '{{ $topic->name }}', '{{ $topic->meeting_date }}', '{{ $topic->description }}')"
                                    class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-600 rounded-lg hover:bg-orange-50 hover:text-orange-500 transition">
                                    <i class='bx bx-pencil'></i>
                                </button>
                                
                                {{-- Tombol Delete dengan SweetAlert --}}
                                <button type="button" onclick="confirmDelete('{{ $topic->id }}', '{{ $topic->name }}')" class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-600 rounded-lg hover:bg-red-50 hover:text-red-500 transition">
                                    <i class='bx bx-trash'></i>
                                </button>
                                
                                {{-- Form Delete Hidden --}}
                                <form id="delete-form-{{ $topic->id }}" action="{{ route('admin.syllabus.destroy', $topic->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <i class='bx bx-filter-alt text-4xl mb-2 opacity-50'></i>
                                <p>Tidak ada topik ditemukan.</p>
                                @if(request('filter_class'))
                                    <a href="{{ route('admin.syllabus') }}" class="text-indigo-600 hover:underline text-xs mt-1">Reset Filter</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Add Topic (Sama seperti sebelumnya) --}}
<div id="modalAddTopic" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-8 w-full max-w-lg transform scale-100 transition-all max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-gray-800">Tambah Topik</h3>
            <button onclick="toggleModal('modalAddTopic')" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 transition">
                <i class='bx bx-x bx-sm'></i>
            </button>
        </div>
        
        <form class="space-y-5" action="{{ route('admin.syllabus.store') }}" method="POST">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Kelas</label>
                <div class="relative">
                    {{-- Jika sedang difilter, otomatis pilih kelas tersebut di modal tambah --}}
                    <select name="class_id" required class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 appearance-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                        <option value="" disabled {{ !request('filter_class') ? 'selected' : '' }}>-- Pilih Kelas --</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('filter_class') == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                    <i class='bx bx-chevron-down absolute right-4 top-3.5 text-gray-400'></i>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Pertemuan</label>
                <input type="text" name="name" required placeholder="Contoh: Pertemuan 1 - Kontrak Kuliah" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Pertemuan</label>
                <input type="date" name="meeting_date" required class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Singkat</label>
                <textarea rows="3" name="description" placeholder="Apa yang akan dibahas?" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"></textarea>
            </div>
            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modalAddTopic')" class="px-6 py-3 text-gray-600 hover:bg-gray-100 rounded-xl font-medium transition">Batal</button>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-700 to-purple-600 text-white rounded-xl font-medium shadow-lg shadow-indigo-200 hover:shadow-none transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Topic (Sama seperti sebelumnya) --}}
<div id="modalEditTopic" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-8 w-full max-w-lg transform scale-100 transition-all max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-gray-800">Edit Topik</h3>
            <button onclick="toggleModal('modalEditTopic')" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 transition">
                <i class='bx bx-x bx-sm'></i>
            </button>
        </div>
        <form id="formEditTopic" action="#" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Kelas</label>
                <div class="relative">
                    <select id="edit_class_id" name="class_id" required class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 appearance-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->name }}</option>
                        @endforeach
                    </select>
                    <i class='bx bx-chevron-down absolute right-4 top-3.5 text-gray-400'></i>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Pertemuan</label>
                <input type="text" id="edit_name" name="name" required class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Pertemuan</label>
                <input type="date" id="edit_meeting_date" name="meeting_date" required class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Singkat</label>
                <textarea rows="3" id="edit_description" name="description" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"></textarea>
            </div>
            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modalEditTopic')" class="px-6 py-3 text-gray-600 hover:bg-gray-100 rounded-xl font-medium transition">Batal</button>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-medium shadow-lg shadow-orange-200 hover:shadow-none transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Toggle Modal Standard
    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
        } else {
            modal.classList.add('hidden');
        }
    }

    // Open Edit Modal
    function openEditModal(id, classId, name, meetingDate, description) {
        let urlTemplate = "{{ route('admin.syllabus.update', 'ID_PLACEHOLDER') }}";
        document.getElementById('formEditTopic').action = urlTemplate.replace('ID_PLACEHOLDER', id);
        
        document.getElementById('edit_class_id').value = classId;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_meeting_date').value = meetingDate;
        document.getElementById('edit_description').value = description;
        
        toggleModal('modalEditTopic');
    }

    // SweetAlert2 Delete Confirmation
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Topik?',
            text: "Anda akan menghapus pertemuan \"" + name + "\". Data ini tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444', // Red 500
            cancelButtonColor: '#6b7280', // Gray 500
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl px-4 py-2',
                cancelButton: 'rounded-xl px-4 py-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form hidden jika user klik "Ya, Hapus!"
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endpush
@endsection