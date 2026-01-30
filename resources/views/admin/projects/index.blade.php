@extends('layouts.student')

@section('content')
<div class="container px-6 py-8 mx-auto">
    
    {{-- Header & Action --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Galeri Projek Mahasiswa</h1>
            <p class="text-gray-500 mt-1">Lihat karya terbaik dari seluruh kelas.</p>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-8">
        <form action="{{ route('student.projects.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
            <div class="flex-1 w-full">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 block">Filter Kelas</label>
                <div class="relative">
                    <select name="class_id" onchange="this.form.submit()" class="w-full appearance-none bg-gray-50 border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-blue-500">
                        <option value="">Semua Kelas</option>
                        @foreach($allClasses as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }} ({{ $class->code }})
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <i class='bx bx-chevron-down'></i>
                    </div>
                </div>
            </div>
            @if(request('class_id'))
                <a href="{{ route('student.projects.index') }}" class="text-red-500 hover:text-red-700 text-sm font-semibold mt-5">
                    <i class='bx bx-x'></i> Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Project Grid --}}
    @if($projects->isEmpty())
        <div class="text-center py-16 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
            <i class='bx bx-planet text-6xl text-gray-300 mb-4'></i>
            <h3 class="text-xl font-medium text-gray-600">Belum ada projek ditemukan</h3>
            <p class="text-gray-400">Jadilah yang pertama mengupload projek!</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($projects as $project)
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition duration-300 border border-gray-100 overflow-hidden group flex flex-col h-full relative">
                    
                    {{-- Badge Featured --}}
                    @if($project->is_featured)
                        <div class="absolute top-4 right-4 z-10 bg-yellow-400 text-yellow-900 text-[10px] font-black px-3 py-1 rounded-full shadow-sm flex items-center gap-1">
                            <i class='bx bxs-star'></i> FEATURED
                        </div>
                    @endif

                    {{-- Thumbnail --}}
                    <div class="h-48 bg-gray-200 relative overflow-hidden">
                        <img src="{{ asset('storage/' . $project->thumbnail) }}" alt="{{ $project->title }}" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition duration-300"></div>
                    </div>

                    {{-- Content --}}
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="mb-3">
                            <span class="bg-blue-50 text-blue-600 text-[10px] font-bold px-2 py-1 rounded border border-blue-100 uppercase tracking-wide">
                                {{ $project->course->name ?? 'Unknown Class' }}
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-gray-800 mb-2 leading-snug group-hover:text-blue-600 transition">{{ $project->title }}</h3>
                        
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold">
                                {{ substr($project->group->name ?? 'G', 0, 1) }}
                            </div>
                            <p class="text-sm text-gray-500 font-medium">{{ $project->group->name ?? 'Tanpa Kelompok' }}</p>
                        </div>

                        <p class="text-gray-600 text-sm line-clamp-3 mb-6 flex-1">
                            {{ $project->description }}
                        </p>

                        <div class="flex gap-3 mt-auto">
                            @if($project->project_url)
                                <a href="{{ $project->project_url }}" target="_blank" class="flex-1 text-center bg-gray-50 hover:bg-blue-50 text-gray-700 hover:text-blue-600 py-2 rounded-lg text-sm font-semibold border border-gray-200 transition flex items-center justify-center gap-2">
                                    <i class='bx bx-globe'></i> Demo
                                </a>
                            @endif
                            @if($project->repo_url)
                                <a href="{{ $project->repo_url }}" target="_blank" class="flex-1 text-center bg-gray-50 hover:bg-gray-800 hover:text-white text-gray-700 py-2 rounded-lg text-sm font-semibold border border-gray-200 transition flex items-center justify-center gap-2">
                                    <i class='bx bxl-github'></i> Code
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $projects->links() }}
        </div>
    @endif
</div>

{{-- MODAL UPLOAD --}}
<div id="uploadModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all scale-100">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Upload Projek Baru</h3>
            <button onclick="document.getElementById('uploadModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class='bx bx-x text-2xl'></i>
            </button>
        </div>
        
        <div class="p-6">
            <form id="projectUploadForm" method="POST" enctype="multipart/form-data">
                @csrf
                
                {{-- Langkah 1: Pilih Kelas --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Kelas</label>
                    <select id="classSelector" onchange="updateFormAction(this.value)" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm p-2.5 bg-white">
                        <option value="">-- Pilih Kelas Anda --</option>
                        @foreach($myClasses as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Projek akan otomatis dikaitkan dengan Kelompok Anda di kelas ini.</p>
                </div>

                {{-- Konten Form (Default Hidden sampai kelas dipilih) --}}
                <div id="formContent" class="space-y-4 opacity-50 pointer-events-none transition duration-300">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Judul Projek</label>
                        <input type="text" name="title" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 shadow-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 shadow-sm"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Link Demo</label>
                            <input type="url" name="project_url" placeholder="https://" class="w-full rounded-lg border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Link Repo</label>
                            <input type="url" name="repo_url" placeholder="https://github.." class="w-full rounded-lg border-gray-300 shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Thumbnail</label>
                        <input type="file" name="thumbnail" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-md mt-4 transition">
                        Publikasikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function updateFormAction(classId) {
        const form = document.getElementById('projectUploadForm');
        const content = document.getElementById('formContent');
        
        if (classId) {
            // Update action URL form sesuai route student.project.store
            // Asumsi URL pattern: /student/class/{classId}/project/store
            form.action = "/student/class/" + classId + "/project/store";
            
            // Aktifkan form
            content.classList.remove('opacity-50', 'pointer-events-none');
        } else {
            form.action = "";
            content.classList.add('opacity-50', 'pointer-events-none');
        }
    }
</script>
@endsection