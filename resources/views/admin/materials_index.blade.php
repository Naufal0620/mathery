@extends('layouts.admin')

@section('title', 'Manajemen Materi')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Bank Materi Pembelajaran</h1>
            <p class="text-sm text-gray-500">Kelola materi dari dosen dan mahasiswa.</p>
        </div>
        <button onclick="toggleModal('uploadModal')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow flex items-center gap-2 text-sm font-semibold transition">
            <i class='bx bx-upload'></i> Upload Materi Baru
        </button>
    </div>

    {{-- Filter --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6">
        <form action="{{ route('admin.materials.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Pilih Kelas --}}
            <div>
                <label class="text-xs font-bold text-gray-500 uppercase">Filter Kelas</label>
                <select name="class_id" onchange="this.form.submit()" class="w-full mt-1 border-gray-300 rounded-lg text-sm focus:ring-indigo-500">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Pilih Topik (Hanya muncul jika kelas dipilih) --}}
            <div>
                <label class="text-xs font-bold text-gray-500 uppercase">Filter Topik</label>
                <select name="topic_id" onchange="this.form.submit()" class="w-full mt-1 border-gray-300 rounded-lg text-sm focus:ring-indigo-500" {{ $topics->isEmpty() ? 'disabled' : '' }}>
                    <option value="">-- Semua Topik --</option>
                    @foreach($topics as $t)
                        <option value="{{ $t->id }}" {{ request('topic_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                @if(request('class_id') || request('topic_id'))
                    <a href="{{ route('admin.materials.index') }}" class="text-red-500 hover:text-red-700 text-sm font-bold mb-2">
                        <i class='bx bx-reset'></i> Reset Filter
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- List Materi --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase">
                <tr>
                    <th class="px-6 py-3">Judul Materi</th>
                    <th class="px-6 py-3">Tipe</th>
                    <th class="px-6 py-3">Pengunggah</th>
                    <th class="px-6 py-3">Topik / Kelas</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($materials as $m)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-medium text-gray-800">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded bg-blue-50 text-blue-600 text-xl">
                                @if($m->type == 'pdf') <i class='bx bxs-file-pdf'></i>
                                @elseif($m->type == 'video') <i class='bx bxs-video'></i>
                                @elseif($m->type == 'ppt') <i class='bx bxs-slideshow'></i>
                                @else <i class='bx bx-link'></i> @endif
                            </div>
                            <a href="{{ $m->type == 'link' ? $m->file_path : asset('storage/'.$m->file_path) }}" target="_blank" class="hover:text-indigo-600 hover:underline">
                                {{ $m->title }}
                            </a>
                        </div>
                    </td>
                    <td class="px-6 py-4 uppercase text-xs font-bold text-gray-500">{{ $m->type }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold">
                                {{ substr($m->author->full_name, 0, 1) }}
                            </span>
                            <span class="{{ $m->author->role == 'admin' ? 'text-indigo-600 font-bold' : '' }}">
                                {{ $m->author->full_name }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-500">
                        <p class="font-bold">{{ $m->topic->name }}</p>
                        <p>{{ $m->topic->course->name }}</p>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <form action="{{ route('admin.materials.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Hapus materi ini?');">
                            @csrf @method('DELETE')
                            <button class="text-gray-400 hover:text-red-600 transition"><i class='bx bx-trash text-lg'></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-8 text-gray-400">Tidak ada materi ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $materials->links() }}</div>
    </div>
</div>

{{-- MODAL UPLOAD --}}
<div id="uploadModal" class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-50 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800">Upload Materi (Admin)</h3>
            <button onclick="toggleModal('uploadModal')" class="text-gray-400 hover:text-red-500"><i class='bx bx-x text-2xl'></i></button>
        </div>
        <div class="p-6">
            @if(!$selectedCourse)
                <div class="text-center text-gray-500 py-4">
                    <i class='bx bx-info-circle text-4xl mb-2'></i>
                    <p>Silakan <b>Filter Kelas</b> terlebih dahulu di halaman utama untuk memilih target upload.</p>
                </div>
            @else
                <form action="{{ route('admin.materials.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Topik Pertemuan</label>
                        <select name="topic_id" class="w-full border-gray-300 rounded-lg text-sm" required>
                            @foreach($topics as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Judul Materi</label>
                        <input type="text" name="title" class="w-full border-gray-300 rounded-lg text-sm" required placeholder="Contoh: Slide Presentasi Pertemuan 1">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Tipe File</label>
                        {{-- Tambahkan ID dan onchange --}}
                        <select name="type" id="admFileType" onchange="updateAdmInput()" class="w-full border-gray-300 rounded-lg text-sm">
                            <option value="pdf">Dokumen PDF (.pdf)</option>
                            <option value="ppt">PowerPoint (.ppt, .pptx)</option>
                            <option value="video">Video (.mp4, .webm)</option>
                            <option value="link">Link Eksternal</option>
                        </select>
                    </div>

                    <div id="admFileGroup">
                        <label class="block text-xs font-bold text-gray-500 mb-1">
                            File <span id="admFileHint" class="text-[10px] text-gray-400 font-normal">(PDF Max 10MB)</span>
                        </label>
                        {{-- Input File dengan Accept Dinamis --}}
                        <input type="file" name="file" id="admFileInput" accept=".pdf" class="w-full text-sm text-gray-500 file:bg-indigo-50 file:text-indigo-700 file:border-0 file:rounded-full file:px-4 file:py-1 file:mr-2">
                    </div>

                    <div id="admUrlGroup" class="hidden">
                        <label class="block text-xs font-bold text-gray-500 mb-1">URL / Link</label>
                        <input type="url" name="url" placeholder="https://" class="w-full border-gray-300 rounded-lg text-sm">
                    </div>

                    {{-- SCRIPT UPDATE INPUT (Taruh di bawah section content) --}}
                    <script>
                        function updateAdmInput() {
                            const type = document.getElementById('admFileType').value;
                            const fileGroup = document.getElementById('admFileGroup');
                            const urlGroup = document.getElementById('admUrlGroup');
                            const fileInput = document.getElementById('admFileInput');
                            const hint = document.getElementById('admFileHint');

                            // Reset
                            fileGroup.classList.add('hidden');
                            urlGroup.classList.add('hidden');

                            if (type === 'link') {
                                urlGroup.classList.remove('hidden');
                            } else {
                                fileGroup.classList.remove('hidden');
                                
                                // Set Whitelist di Frontend (UX)
                                if (type === 'pdf') {
                                    fileInput.accept = '.pdf';
                                    hint.innerText = '(Format .pdf, Max 10MB)';
                                } else if (type === 'ppt') {
                                    fileInput.accept = '.ppt, .pptx';
                                    hint.innerText = '(Format .ppt / .pptx, Max 10MB)';
                                } else if (type === 'video') {
                                    fileInput.accept = '.mp4, .webm, .ogg';
                                    hint.innerText = '(Format .mp4, Max 20MB)';
                                }
                            }
                        }
                        
                        // Jalankan sekali saat load
                        document.addEventListener('DOMContentLoaded', updateAdmInput);
                    </script>

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded-lg">Upload</button>
                </form>
            @endif
        </div>
    </div>
</div>

<script>
    function toggleModal(id) {
        document.getElementById(id).classList.toggle('hidden');
    }
    function toggleFileInput() {
        const type = document.getElementById('fileType').value;
        if(type === 'link' || type === 'article') {
            document.getElementById('fileInputGroup').classList.add('hidden');
            document.getElementById('urlInputGroup').classList.remove('hidden');
        } else {
            document.getElementById('fileInputGroup').classList.remove('hidden');
            document.getElementById('urlInputGroup').classList.add('hidden');
        }
    }
</script>
@endsection