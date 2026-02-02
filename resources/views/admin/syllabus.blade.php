@extends('layouts.admin')

@section('title', 'Silabus & Jadwal - Mathery')
@section('header_title', 'Manajemen Silabus')

@section('content')
<div class="fade-in space-y-6">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div class="w-full md:w-auto">
            <h2 class="text-xl font-bold text-gray-800 mb-1">Jadwal Pembelajaran</h2>
            <p class="text-sm text-gray-500 mb-4">Pilih kelas untuk melihat dan mengatur topik pertemuan.</p>
            
            <form action="{{ route('admin.syllabus') }}" method="GET" class="relative max-w-xs">
                <i class='bx bx-filter absolute left-3 top-1/2 -translate-y-1/2 text-gray-400'></i>
                <select name="filter_class" onchange="this.form.submit()" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer hover:bg-gray-100">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('filter_class') == $course->id ? 'selected' : '' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        @if(request('filter_class'))
        <button onclick="openModal('createTopicModal')" class="w-full md:w-auto px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
            <i class='bx bx-plus-circle text-xl'></i> Tambah Pertemuan
        </button>
        @endif
    </div>

    <div class="relative min-h-[300px]">
        
        @if($topics->isNotEmpty())
            <div class="absolute left-8 md:left-1/2 top-0 bottom-0 w-0.5 bg-gray-200 transform -translate-x-1/2 hidden md:block"></div>
            
            <div class="space-y-8 pb-10">
                @foreach($topics as $index => $topic)
                    @php
                        $isPast = \Carbon\Carbon::parse($topic->meeting_date)->isPast();
                        $alignment = $index % 2 == 0 ? 'left' : 'right';
                    @endphp

                    <div class="relative flex items-center justify-between md:justify-center">
                        <div class="hidden md:block w-5/12 {{ $alignment == 'left' ? 'order-1' : 'order-3' }}"></div>

                        <div class="absolute left-8 md:left-1/2 transform -translate-x-1/2 w-8 h-8 rounded-full border-4 border-white shadow-md flex items-center justify-center z-10 
                            {{ $isPast ? 'bg-gray-400' : 'bg-gradient-to-r from-indigo-500 to-purple-600' }} order-2">
                            <span class="text-white text-[10px] font-bold">{{ $loop->iteration }}</span>
                        </div>

                        <div class="w-full pl-20 md:pl-0 md:w-5/12 {{ $alignment == 'left' ? 'order-3 md:text-left' : 'order-1 md:text-right' }}">
                            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-all group relative {{ $isPast ? 'opacity-70 grayscale-[50%]' : '' }}">
                                <div class="text-xs font-semibold uppercase tracking-wider mb-1 {{ $isPast ? 'text-gray-400' : 'text-indigo-600' }}">
                                    {{ \Carbon\Carbon::parse($topic->meeting_date)->isoFormat('dddd, D MMMM Y') }}
                                </div>
                                <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-indigo-700 transition-colors">
                                    {{ $topic->name }}
                                </h3>
                                <p class="text-sm text-gray-500 line-clamp-2 mb-4">
                                    {{ $topic->description ?? 'Tidak ada deskripsi.' }}
                                </p>
                                <div class="flex items-center gap-2 {{ $alignment == 'right' ? 'md:justify-end' : '' }}">
                                    <button onclick="editTopic({{ $topic->id }}, '{{ $topic->name }}', '{{ $topic->meeting_date }}', '{{ $topic->description }}')" 
                                        class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-50 rounded-lg border border-gray-200 hover:bg-orange-50 hover:text-orange-600 hover:border-orange-200 transition-all">
                                        <i class='bx bx-edit'></i> Edit
                                    </button>
                                    <form action="{{ route('admin.syllabus.destroy', $topic->id) }}" method="POST" onsubmit="return deleteConfirm(event)">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-50 rounded-lg border border-gray-200 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-all">
                                            <i class='bx bx-trash'></i> Hapus
                                        </button>
                                    </form>
                                </div>
                                <div class="md:hidden absolute left-[-2.05rem] top-8 w-6 h-0.5 bg-gray-200"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        @elseif(request('filter_class'))
            <div class="text-center py-20 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 mt-4">
                <div class="bg-indigo-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-calendar-plus text-3xl text-indigo-500'></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Belum ada Jadwal</h3>
                <p class="text-gray-500 mb-6">Kelas ini belum memiliki topik pertemuan.</p>
                <button onclick="openModal('createTopicModal')" class="text-indigo-600 font-medium hover:underline">
                    Buat Topik Pertama
                </button>
            </div>

        @else
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="bg-white p-6 rounded-full shadow-xl shadow-indigo-100 mb-6 animate-bounce-slow">
                    <i class='bx bx-select-none text-5xl text-indigo-600'></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Pilih Kelas Terlebih Dahulu</h3>
                <p class="text-gray-500 max-w-md mx-auto">
                    Silakan pilih kelas melalui dropdown di atas untuk mulai mengelola jadwal pertemuan dan silabus.
                </p>
                <div class="mt-8 text-gray-300 animate-pulse">
                    <i class='bx bx-up-arrow-alt text-4xl'></i>
                </div>
            </div>
        @endif
    </div>

    <div id="createTopicModal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
    
        {{-- REVISI: Tambahkan onclick di sini --}}
        <div class="fixed inset-0 z-10 overflow-y-auto" onclick="closeModal('createTopicModal')">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                
                {{-- REVISI: Tambahkan stopPropagation agar klik di dalam kotak tidak menutup modal --}}
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 w-full max-w-md p-6 fade-in" 
                     onclick="event.stopPropagation()">
                    
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Tambah Topik Pertemuan</h3>
                    
                    <form action="{{ route('admin.syllabus.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="class_id" value="{{ request('filter_class') }}">
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Pertemuan</label>
                                <input type="text" name="name" required placeholder="Contoh: Pengantar Kalkulus" 
                                    class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none text-sm">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pelaksanaan</label>
                                <input type="date" name="meeting_date" required 
                                    class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi / Agenda</label>
                                <textarea name="description" rows="3" placeholder="Apa yang akan dipelajari..." 
                                    class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none text-sm"></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" onclick="closeModal('createTopicModal')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 text-sm">Batal</button>
                            <button type="submit" class="px-4 py-2 text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 text-sm">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editTopicModal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
    
        {{-- REVISI: Tambahkan onclick di sini --}}
        <div class="fixed inset-0 z-10 overflow-y-auto" onclick="closeModal('editTopicModal')">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                
                {{-- REVISI: Tambahkan stopPropagation di sini --}}
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 w-full max-w-md p-6 fade-in"
                     onclick="event.stopPropagation()">
                    
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Edit Topik</h3>
                    
                    <form id="editTopicForm" method="POST">
                        @csrf @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Pertemuan</label>
                                <input type="text" name="name" id="edit_name" required 
                                    class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none text-sm">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pelaksanaan</label>
                                <input type="date" name="meeting_date" id="edit_date" required 
                                    class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi / Agenda</label>
                                <textarea name="description" id="edit_description" rows="3" 
                                    class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none text-sm"></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" onclick="closeModal('editTopicModal')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 text-sm">Batal</button>
                            <button type="submit" class="px-4 py-2 text-white bg-orange-500 rounded-xl hover:bg-orange-600 text-sm">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    function editTopic(id, name, date, description) {
        openModal('editTopicModal');
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_date').value = date;
        document.getElementById('edit_description').value = description;
        let url = "{{ route('admin.syllabus.update', ':id') }}";
        document.getElementById('editTopicForm').action = url.replace(':id', id);
    }

    function deleteConfirm(event) {
        event.preventDefault();
        var form = event.target;
        Swal.fire({
            title: 'Hapus Topik?',
            text: "Data materi dan tugas terkait mungkin akan terpengaruh.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    }
</script>
@endpush
@endsection