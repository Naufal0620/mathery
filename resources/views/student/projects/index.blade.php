@extends('layouts.student')

@section('title', 'Galeri Projek')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class='bx bx-rocket text-blue-600'></i> Galeri Projek
            </h1>
            <p class="text-gray-500 text-sm mt-1">Lihat karya terbaik dari teman-teman Anda.</p>
        </div>
        
        {{-- Tombol Upload --}}
        @if($myClasses->isNotEmpty())
            <button onclick="toggleModal('uploadModal')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-2 font-semibold text-sm">
                <i class='bx bx-cloud-upload text-xl'></i> Upload Projek
            </button>
        @endif
    </div>

    {{-- FILTER BAR --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-8">
        <form action="{{ route('student.projects.index') }}" method="GET" class="flex items-end gap-4">
            <div class="w-full md:w-1/3">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 block">Filter Kelas</label>
                <div class="relative">
                    <select name="class_id" onchange="this.form.submit()" class="w-full appearance-none bg-gray-50 border border-gray-300 text-gray-700 py-2.5 px-4 pr-8 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 border-r-[12px] border-transparent outline-none">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($allCourses as $c)
                            <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500"><i class='bx bx-chevron-down text-lg'></i></div>
                </div>
            </div>
            @if(request('class_id'))
                <a href="{{ route('student.projects.index') }}" class="text-red-500 hover:text-red-700 text-sm font-bold mb-2.5 flex items-center gap-1">
                    <i class='bx bx-reset'></i> Reset
                </a>
            @endif
        </form>
    </div>

    {{-- GRID PROJEK --}}
    @if($projects->isEmpty())
        <div class="text-center py-20 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
            <i class='bx bx-planet text-5xl text-gray-300 mb-3'></i>
            <p class="text-gray-500 font-medium">Belum ada projek yang ditemukan.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($projects as $project)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-200 overflow-hidden group flex flex-col h-full relative">
                    
                    @if($project->is_featured)
                        <div class="absolute top-3 right-3 z-10 bg-yellow-400 text-yellow-900 text-[10px] font-black px-2 py-1 rounded shadow-sm flex items-center gap-1">
                            <i class='bx bxs-star'></i> FEATURED
                        </div>
                    @endif

                    {{-- Thumbnail --}}
                    <div class="h-44 bg-gray-100 relative overflow-hidden group-hover:brightness-95 transition">
                        @if($project->thumbnail && $project->thumbnail != 'project_default.jpg')
                            <img src="{{ asset('storage/' . $project->thumbnail) }}" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700">
                        @else
                             <div class="w-full h-full flex items-center justify-center text-gray-300">
                                <i class='bx bx-image text-5xl'></i>
                            </div>
                        @endif
                        <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-black/70 to-transparent p-3 pt-8">
                            <span class="text-white text-[10px] font-bold uppercase tracking-wider bg-blue-600/90 px-2 py-0.5 rounded shadow-sm">
                                {{ $project->course->code }}
                            </span>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="font-bold text-gray-800 text-lg leading-tight mb-2 line-clamp-2 group-hover:text-blue-600 transition">{{ $project->title }}</h3>
                        
                        <div class="flex items-center gap-2 mb-3 pb-3 border-b border-gray-50">
                            <div class="w-7 h-7 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-bold border border-indigo-100">
                                {{ substr($project->group->name ?? 'G', 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-gray-700 truncate">{{ $project->group->name ?? 'Tanpa Kelompok' }}</p>
                                <p class="text-[10px] text-gray-400 truncate">Upload: {{ $project->student->full_name }}</p>
                            </div>
                        </div>

                        <p class="text-gray-600 text-sm line-clamp-3 mb-4 flex-1">{{ $project->description }}</p>

                        <div class="flex gap-2 mt-auto">
                            @if($project->project_url)
                                <a href="{{ $project->project_url }}" target="_blank" class="flex-1 text-center bg-white hover:bg-blue-50 text-gray-600 hover:text-blue-600 py-2 rounded-lg text-xs font-bold border border-gray-200 transition flex items-center justify-center gap-1">
                                    <i class='bx bx-globe text-base'></i> Demo
                                </a>
                            @endif
                            @if($project->repo_url)
                                <a href="{{ $project->repo_url }}" target="_blank" class="flex-1 text-center bg-white hover:bg-gray-800 hover:text-white text-gray-600 py-2 rounded-lg text-xs font-bold border border-gray-200 transition flex items-center justify-center gap-1">
                                    <i class='bx bxl-github text-base'></i> Code
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-8">{{ $projects->links() }}</div>
    @endif
</div>

{{-- MODAL UPLOAD --}}
<div id="uploadModal" class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-60 flex items-center justify-center p-4 backdrop-blur-sm transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform scale-100 transition-all">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <div>
                <h3 class="font-bold text-gray-800">Upload Projek Kelompok</h3>
                <p class="text-xs text-gray-500">Pastikan Anda sudah memiliki kelompok.</p>
            </div>
            <button onclick="toggleModal('uploadModal')" class="text-gray-400 hover:text-red-500 transition"><i class='bx bx-x text-2xl'></i></button>
        </div>
        
        <div class="p-6">
            <form action="{{ route('student.projects.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                {{-- Pilih Kelas --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pilih Kelas</label>
                    <select name="class_id" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">-- Pilih Kelas Anda --</option>
                        @foreach($myClasses as $class)
                            <option value="{{ $class->id }}">
                                {{ $class->name }} ({{ $class->pivot->group_id ? 'Ada Kelompok' : 'Belum Ada Kelompok' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Judul Projek</label>
                    <input type="text" name="title" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 text-sm" placeholder="Nama Aplikasi / Projek...">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Deskripsi Singkat</label>
                    <textarea name="description" rows="3" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 text-sm" placeholder="Jelaskan fitur utama..."></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Link Demo (URL)</label>
                        <input type="url" name="project_url" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 text-sm" placeholder="https://...">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Link Repo (URL)</label>
                        <input type="url" name="repo_url" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 text-sm" placeholder="https://github...">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Thumbnail (Gambar)</label>
                    <input type="file" name="thumbnail" accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-md transition flex items-center justify-center gap-2 mt-2">
                    <i class='bx bx-send'></i> Publikasikan Projek
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleModal(id) {
        const el = document.getElementById(id);
        if(el.classList.contains('hidden')) {
            el.classList.remove('hidden');
        } else {
            el.classList.add('hidden');
        }
    }
</script>
@endsection