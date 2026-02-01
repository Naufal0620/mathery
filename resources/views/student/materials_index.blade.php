@extends('layouts.student')

@section('title', 'Materi Kelas')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Header & Upload Action --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class='bx bx-book-reader text-blue-600'></i> Materi Pembelajaran
            </h1>
            <p class="text-sm text-gray-500">Akses materi per pertemuan.</p>
        </div>

        {{-- Logika Tombol Upload (Terkunci ke Topik Kelompok) --}}
        @if($selectedClass && $myAssignedTopic)
            <button onclick="toggleModal('uploadModal')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow-md flex items-center gap-2 font-semibold transition hover:-translate-y-0.5">
                <i class='bx bx-cloud-upload text-xl'></i> 
                <div class="text-left leading-tight">
                    <span class="block text-xs font-normal opacity-80">Tugas Kelompok:</span>
                    <span class="text-sm">Upload Materi {{ Str::limit($myAssignedTopic->name, 15) }}</span>
                </div>
            </button>
        @elseif($selectedClass)
            <button disabled class="bg-gray-100 text-gray-400 px-5 py-2 rounded-lg flex items-center gap-2 font-semibold cursor-not-allowed border border-gray-200">
                <i class='bx bx-lock-alt'></i> Upload Terkunci
            </button>
        @endif
    </div>

    {{-- Filter Kelas (Wajib Pilih) --}}
    @if($myClasses->isNotEmpty())
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-8">
            <form action="{{ route('student.materials.index') }}" method="GET">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 block">Kelas Saat Ini</label>
                <div class="relative w-full md:w-1/3">
                    <select name="class_id" onchange="this.form.submit()" class="w-full appearance-none bg-gray-50 border border-gray-300 text-gray-800 font-semibold py-2.5 px-4 pr-8 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm transition">
                        @foreach($myClasses as $c)
                            <option value="{{ $c->id }}" {{ ($selectedClass && $selectedClass->id == $c->id) ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->code }})
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-600"><i class='bx bx-chevron-down text-xl'></i></div>
                </div>
            </form>
        </div>
    @else
        <div class="text-center py-10 bg-red-50 rounded-xl border border-red-200 text-red-600">
            <p>Anda belum bergabung dengan kelas manapun.</p>
        </div>
    @endif

    {{-- CONTENT: Loop Topik --}}
    @if($selectedClass)
        <div class="space-y-8">
            @forelse($topics as $topic)
                <div class="border-l-4 border-blue-500 pl-4 md:pl-6 relative">
                    {{-- Judul Pertemuan --}}
                    <div class="mb-4">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            {{ $topic->name }}
                            @if($topic->meeting_date)
                                <span class="text-xs font-normal text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full border border-gray-200">
                                    <i class='bx bx-calendar'></i> {{ \Carbon\Carbon::parse($topic->meeting_date)->translatedFormat('d M Y') }}
                                </span>
                            @endif
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">{{ $topic->description ?? 'Tidak ada deskripsi pertemuan.' }}</p>
                    </div>

                    {{-- Grid Materi dalam Pertemuan Ini --}}
                    @if($topic->materials->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($topic->materials as $m)
                                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-blue-300 transition flex items-start gap-3 group">
                                    {{-- Icon File --}}
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-xl shrink-0 
                                        {{ $m->type == 'pdf' ? 'bg-red-50 text-red-500' : ($m->type == 'ppt' ? 'bg-orange-50 text-orange-500' : ($m->type == 'video' ? 'bg-blue-50 text-blue-500' : 'bg-gray-100 text-gray-600')) }}">
                                        @if($m->type == 'pdf') <i class='bx bxs-file-pdf'></i>
                                        @elseif($m->type == 'ppt') <i class='bx bxs-slideshow'></i>
                                        @elseif($m->type == 'video') <i class='bx bxs-video'></i>
                                        @else <i class='bx bx-link'></i> @endif
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <a href="{{ $m->type == 'link' ? $m->file_path : asset('storage/'.$m->file_path) }}" target="_blank" class="block font-semibold text-gray-800 text-sm leading-snug mb-1 truncate hover:text-blue-600 transition" title="{{ $m->title }}">
                                            {{ $m->title }}
                                        </a>
                                        
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-1.5">
                                                <div class="w-4 h-4 rounded-full bg-gray-200 flex items-center justify-center text-[8px] font-bold text-gray-600">
                                                    {{ substr($m->author->full_name, 0, 1) }}
                                                </div>
                                                <span class="text-xs text-gray-500 truncate max-w-[100px]">
                                                    {{ $m->author->role == 'admin' ? 'Dosen' : $m->author->full_name }}
                                                </span>
                                            </div>
                                            <a href="{{ $m->type == 'link' ? $m->file_path : asset('storage/'.$m->file_path) }}" target="_blank" class="text-gray-400 hover:text-blue-600">
                                                <i class='bx bx-download text-lg'></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-lg p-4 border border-dashed border-gray-200 text-center">
                            <p class="text-xs text-gray-400 italic">Belum ada materi untuk pertemuan ini.</p>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-16 bg-white rounded-xl border border-gray-200">
                    <i class='bx bx-layer text-5xl text-gray-200 mb-3'></i>
                    <p class="text-gray-500">Belum ada topik/pertemuan yang dibuat untuk kelas ini.</p>
                </div>
            @endforelse
        </div>
    @endif
</div>

{{-- MODAL UPLOAD --}}
@if($selectedClass && $myAssignedTopic)
<div id="uploadModal" class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-60 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform scale-100 transition-all">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <div>
                <h3 class="font-bold text-gray-800">Upload Materi Kelompok</h3>
                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-semibold border border-green-200">
                    Target: {{ $myAssignedTopic->name }}
                </span>
            </div>
            <button onclick="toggleModal('uploadModal')" class="text-gray-400 hover:text-red-500"><i class='bx bx-x text-2xl'></i></button>
        </div>
        
        <div class="p-6">
            <form action="{{ route('student.materials.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Judul Materi</label>
                    <input type="text" name="title" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 text-sm" required placeholder="Contoh: Slide Presentasi...">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Tipe File</label>
                    <select name="type" id="stuFileType" onchange="updateStuInput()" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500">
                        <option value="pdf">Dokumen PDF</option>
                        <option value="ppt">PowerPoint (PPT)</option>
                        <option value="link">Link Eksternal</option>
                    </select>
                </div>

                <div id="stuFileGroup">
                    <label class="block text-xs font-bold text-gray-500 mb-1">
                        File <span id="stuFileHint" class="text-[10px] text-gray-400 font-normal">(Max 5MB)</span>
                    </label>
                    <input type="file" name="file" id="stuFileInput" accept=".pdf" class="w-full text-sm text-gray-500 file:bg-blue-50 file:text-blue-700 file:border-0 file:rounded-full file:px-4 file:py-1 hover:file:bg-blue-100 transition">
                </div>
                <div id="stuUrlGroup" class="hidden">
                    <label class="block text-xs font-bold text-gray-500 mb-1">URL</label>
                    <input type="url" name="url" placeholder="https://" class="w-full border-gray-300 rounded-lg text-sm">
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-lg shadow-md transition flex items-center justify-center gap-2">
                    <i class='bx bx-save'></i> Publikasikan
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function updateStuInput() {
        const type = document.getElementById('stuFileType').value;
        const fileGroup = document.getElementById('stuFileGroup');
        const urlGroup = document.getElementById('stuUrlGroup');
        const fileInput = document.getElementById('stuFileInput');
        const hint = document.getElementById('stuFileHint');

        fileGroup.classList.add('hidden');
        urlGroup.classList.add('hidden');

        if (type === 'link') {
            urlGroup.classList.remove('hidden');
        } else {
            fileGroup.classList.remove('hidden');
            if (type === 'pdf') {
                fileInput.accept = '.pdf';
                hint.innerText = '(Hanya .pdf, Max 5MB)';
            } else if (type === 'ppt') {
                fileInput.accept = '.ppt, .pptx';
                hint.innerText = '(Hanya .ppt / .pptx, Max 5MB)';
            }
        }
    }
    // Init state
    document.addEventListener('DOMContentLoaded', updateStuInput);
</script>
@endif

<script>
    function toggleModal(id) {
        document.getElementById(id).classList.toggle('hidden');
    }
</script>
@endsection