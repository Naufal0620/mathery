@extends('layouts.admin')

@section('title', 'Bank Materi - Mathery')
@section('header_title', 'Manajemen Materi')

@section('content')
<div class="fade-in space-y-6">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div class="w-full md:w-auto">
            <h2 class="text-xl font-bold text-gray-800 mb-1">Bank Materi Pembelajaran</h2>
            <p class="text-sm text-gray-500 mb-4">Upload dan kelola bahan ajar untuk setiap kelas.</p>
            
            <form action="{{ route('admin.materials.index') }}" method="GET" class="relative max-w-xs">
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
        <button onclick="openModal('createMaterialModal')" class="w-full md:w-auto px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
            <i class='bx bx-plus-circle text-xl'></i> Upload Materi
        </button>
        @endif
    </div>

    <div class="min-h-[300px]">
        @if($materials->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($materials as $material)
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all group flex flex-col h-full">
                    
                    <div class="p-5 flex items-start justify-between">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center 
                            @if($material->type == 'link') bg-blue-50 text-blue-600 
                            @elseif($material->type == 'video') bg-red-50 text-red-600 
                            @elseif($material->type == 'pdf') bg-red-50 text-red-600 
                            @else bg-orange-50 text-orange-600 @endif">
                            
                            @if($material->type == 'link') <i class='bx bx-link text-2xl'></i>
                            @elseif($material->type == 'video') <i class='bx bx-video text-2xl'></i>
                            @elseif($material->type == 'ppt') <i class='bx bx-slideshow text-2xl'></i>
                            @elseif($material->type == 'pdf') <i class='bx bx-file-detail text-2xl'></i>
                            @else <i class='bx bx-file text-2xl'></i>
                            @endif
                        </div>
                        
                        <div class="relative" x-data="{ open: false }">
                             <button onclick="editMaterial({{ $material->id }}, '{{ $material->title }}', '{{ $material->topic_id }}', '{{ $material->description }}', '{{ $material->type }}', '{{ $material->url }}')" 
                                class="text-gray-400 hover:text-indigo-600 p-1 rounded-lg transition-colors" title="Edit">
                                <i class='bx bx-edit-alt text-xl'></i>
                            </button>
                            <form action="{{ route('admin.materials.destroy', $material->id) }}" method="POST" class="inline-block" onsubmit="return deleteConfirm(event)">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-600 p-1 rounded-lg transition-colors" title="Hapus">
                                    <i class='bx bx-trash text-xl'></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="px-5 pb-5 flex-1 flex flex-col">
                        <div class="mb-auto">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1 block">
                                {{ Str::limit($material->topic->name ?? 'Topik Dihapus', 25) }}
                            </span>
                            <h3 class="text-base font-bold text-gray-800 leading-snug mb-2 group-hover:text-indigo-600 transition-colors">
                                {{ $material->title }}
                            </h3>
                            <p class="text-xs text-gray-500 line-clamp-2">
                                {{ $material->description ?? 'Tidak ada deskripsi.' }}
                            </p>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center text-[10px] font-bold text-gray-600">
                                    {{ substr($material->author->full_name ?? 'A', 0, 1) }}
                                </div>
                                <span class="text-xs text-gray-400">{{ $material->created_at->format('d M') }}</span>
                            </div>
                            
                            @if($material->type == 'link')
                                <a href="{{ $material->url }}" target="_blank" class="text-xs font-bold text-blue-600 hover:underline flex items-center gap-1">
                                    Buka Link <i class='bx bx-link-external'></i>
                                </a>
                            @else
                                <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:underline flex items-center gap-1">
                                    Download <i class='bx bx-download'></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        @elseif(request('filter_class'))
            <div class="text-center py-20 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 mt-4">
                <div class="bg-indigo-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-folder-open text-3xl text-indigo-500'></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Belum ada Materi</h3>
                <p class="text-gray-500 mb-6">Mulai upload materi pelajaran untuk kelas ini.</p>
                <button onclick="openModal('createMaterialModal')" class="text-indigo-600 font-medium hover:underline">
                    Upload Sekarang
                </button>
            </div>
            
            @if($topics->isEmpty())
                <div class="mt-4 p-4 bg-orange-50 border border-orange-100 rounded-xl flex items-center gap-3 text-orange-700 text-sm max-w-xl mx-auto">
                    <i class='bx bx-info-circle text-xl'></i>
                    <p>Perhatian: Kelas ini belum memiliki Topik. Buat topik dulu di menu <a href="{{ route('admin.syllabus', ['filter_class' => request('filter_class')]) }}" class="font-bold underline">Silabus</a> sebelum upload materi.</p>
                </div>
            @endif

        @else
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="bg-white p-6 rounded-full shadow-xl shadow-indigo-100 mb-6 animate-bounce-slow">
                    <i class='bx bx-select-none text-5xl text-indigo-600'></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Pilih Kelas Terlebih Dahulu</h3>
                <p class="text-gray-500 max-w-md mx-auto">
                    Silakan pilih kelas melalui dropdown di atas untuk melihat dan mengelola materi pembelajaran.
                </p>
            </div>
        @endif
    </div>

    <div id="createMaterialModal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto" onclick="closeModal('createMaterialModal')">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 w-full max-w-lg p-6 fade-in" onclick="event.stopPropagation()">
                    
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Upload Materi Baru</h3>
                    
                    <form action="{{ route('admin.materials.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Materi</label>
                                <input type="text" name="title" required placeholder="Contoh: Modul Bab 1" 
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Topik Pertemuan</label>
                                <select name="topic_id" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none text-sm bg-white">
                                    <option value="" disabled selected>-- Pilih Topik --</option>
                                    @foreach($topics as $topic)
                                        <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div x-data="{ type: 'file' }">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Materi</label>
                                <div class="flex gap-4 mb-3">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="type" value="file" x-model="type" class="text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-sm text-gray-600">Dokumen (PDF/PPT)</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="type" value="video" x-model="type" class="text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-sm text-gray-600">Video</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="type" value="link" x-model="type" class="text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-sm text-gray-600">Link Eksternal</span>
                                    </label>
                                </div>

                                <div x-show="type === 'file' || type === 'video'" class="transition-all">
                                    <input type="file" name="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-xl p-1">
                                    <p class="text-xs text-gray-400 mt-1">*Maksimal 20MB.</p>
                                </div>

                                <div x-show="type === 'link'" class="transition-all" style="display: none;">
                                    <input type="url" name="url" placeholder="https://..." class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none text-sm">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                                <textarea name="description" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none text-sm"></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" onclick="closeModal('createMaterialModal')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 text-sm">Batal</button>
                            <button type="submit" class="px-4 py-2 text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 text-sm">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editMaterialModal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto" onclick="closeModal('editMaterialModal')">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 w-full max-w-lg p-6 fade-in" onclick="event.stopPropagation()">
                    
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Edit Materi</h3>
                    
                    <form id="editMaterialForm" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Materi</label>
                                <input type="text" name="title" id="edit_title" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Topik Pertemuan</label>
                                <select name="topic_id" id="edit_topic_id" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm bg-white">
                                    @foreach($topics as $topic)
                                        <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div x-data="{ changeMode: false, type: 'file' }" x-init="type = document.getElementById('edit_type_hidden').value">
                                <input type="hidden" id="edit_type_hidden"> <div class="flex items-center justify-between mb-2">
                                    <label class="block text-sm font-medium text-gray-700">File / Link</label>
                                    <button type="button" @click="changeMode = !changeMode" class="text-xs text-indigo-600 underline">
                                        <span x-text="changeMode ? 'Batal Ubah' : 'Ganti File/Link'"></span>
                                    </button>
                                </div>

                                <div x-show="changeMode" class="p-3 bg-gray-50 rounded-xl border border-gray-200">
                                    <div class="flex gap-4 mb-3">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="type" value="file" x-model="type" class="text-indigo-600"> <span class="text-xs">File</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="type" value="link" x-model="type" class="text-indigo-600"> <span class="text-xs">Link</span>
                                        </label>
                                    </div>
                                    
                                    <div x-show="type === 'file' || type === 'video'">
                                        <input type="file" name="file" class="block w-full text-xs text-gray-500 file:py-1 file:px-3 file:rounded-full file:bg-white file:border-0 file:text-indigo-700 border border-gray-300 rounded-lg p-1">
                                    </div>
                                    <div x-show="type === 'link'">
                                        <input type="url" name="url" id="edit_url" placeholder="https://..." class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                <textarea name="description" id="edit_description" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm"></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" onclick="closeModal('editMaterialModal')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 text-sm">Batal</button>
                            <button type="submit" class="px-4 py-2 text-white bg-orange-500 rounded-xl hover:bg-orange-600 text-sm">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    function editMaterial(id, title, topicId, description, type, url) {
        openModal('editMaterialModal');
        document.getElementById('edit_title').value = title;
        document.getElementById('edit_topic_id').value = topicId;
        document.getElementById('edit_description').value = description;
        
        // Setup Alpine Data via hidden inputs/attributes logic
        document.getElementById('edit_type_hidden').value = type;
        if(type === 'link') document.getElementById('edit_url').value = url;
        
        let actionUrl = "{{ route('admin.materials.update', ':id') }}";
        document.getElementById('editMaterialForm').action = actionUrl.replace(':id', id);
    }

    function deleteConfirm(event) {
        event.preventDefault();
        var form = event.target;
        Swal.fire({
            title: 'Hapus Materi?',
            text: "File yang dihapus tidak dapat dikembalikan.",
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