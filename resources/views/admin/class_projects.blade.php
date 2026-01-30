@extends('layouts.admin')

@section('title', 'Projek Mahasiswa - ' . $course->name)

@section('content')
<div class="container-fluid px-4">
    
    {{-- HEADER & TABS --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $course->name }}</h1>
                <p class="text-gray-500 text-sm mb-4">Kode Kelas: {{ $course->code }}</p>
            </div>
            <a href="{{ route('admin.classes') }}" class="text-gray-500 hover:text-blue-600 text-sm font-medium transition flex items-center gap-1">
                <i class='bx bx-arrow-back text-lg'></i> Kembali
            </a>
        </div>

        {{-- Tab Navigation --}}
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <a href="{{ route('admin.classes.members', $course->id) }}" 
                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                   <i class='bx bx-group mr-2 text-lg'></i> Anggota Kelas
                </a>

                <a href="{{ route('admin.classes.groups', $course->id) }}" 
                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                   <i class='bx bx-layer mr-2 text-lg'></i> Manajemen Kelompok
                </a>

                {{-- Tab Aktif --}}
                <a href="{{ route('admin.classes.projects', $course->id) }}" 
                   class="border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                   <i class='bx bx-rocket mr-2 text-lg'></i> Projek Mahasiswa
                </a>
            </nav>
        </div>
    </div>

    {{-- KONTEN PROJEK --}}
    @if($projects->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 bg-white rounded-xl border border-dashed border-gray-300">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 text-gray-400">
                <i class='bx bx-folder-open text-4xl'></i>
            </div>
            <h3 class="text-gray-600 font-bold text-lg">Belum Ada Projek</h3>
            <p class="text-sm text-gray-400">Mahasiswa belum mengunggah projek apapun di kelas ini.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($projects as $project)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden group hover:shadow-md transition flex flex-col h-full relative">
                    
                    {{-- Badge Featured --}}
                    @if($project->is_featured)
                        <div class="absolute top-3 right-3 z-10 bg-yellow-400 text-yellow-900 text-[10px] font-bold px-2 py-1 rounded shadow-sm flex items-center gap-1">
                            <i class='bx bxs-star'></i> FEATURED
                        </div>
                    @endif

                    {{-- Thumbnail --}}
                    <div class="h-40 bg-gray-100 overflow-hidden relative">
                        @if($project->thumbnail && $project->thumbnail != 'project_default.jpg')
                            <img src="{{ asset('storage/' . $project->thumbnail) }}" alt="{{ $project->title }}" class="w-full h-full object-cover transition transform group-hover:scale-105 duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300 bg-gray-50">
                                <i class='bx bx-image text-5xl'></i>
                            </div>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="font-bold text-gray-800 text-lg leading-tight line-clamp-2">{{ $project->title }}</h3>
                        </div>
                        
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">
                                {{ substr($project->student->full_name, 0, 1) }}
                            </div>
                            <span class="text-xs text-gray-500 font-medium">{{ $project->student->full_name }}</span>
                        </div>

                        <p class="text-sm text-gray-600 line-clamp-3 mb-4 flex-1">
                            {{ $project->description ?? 'Tidak ada deskripsi.' }}
                        </p>

                        {{-- Links --}}
                        <div class="flex gap-2 mb-4">
                            @if($project->project_url)
                                <a href="{{ $project->project_url }}" target="_blank" class="flex-1 text-center py-1.5 border border-gray-200 rounded text-xs font-medium text-gray-600 hover:bg-gray-50 transition">
                                    <i class='bx bx-globe'></i> Demo
                                </a>
                            @endif
                            @if($project->repo_url)
                                <a href="{{ $project->repo_url }}" target="_blank" class="flex-1 text-center py-1.5 border border-gray-200 rounded text-xs font-medium text-gray-600 hover:bg-gray-50 transition">
                                    <i class='bx bxl-github'></i> Repo
                                </a>
                            @endif
                        </div>

                        {{-- Action Buttons (Admin) --}}
                        <div class="pt-3 border-t border-gray-100 flex justify-between items-center">
                            {{-- Toggle Featured --}}
                            <form action="{{ route('admin.projects.toggle-featured', $project->id) }}" method="POST">
                                @csrf @method('PUT')
                                <button type="submit" class="flex items-center gap-1 text-xs font-bold {{ $project->is_featured ? 'text-yellow-500 hover:text-yellow-600' : 'text-gray-400 hover:text-yellow-500' }} transition">
                                    <i class='bx {{ $project->is_featured ? 'bxs-star' : 'bx-star' }} text-lg'></i>
                                    {{ $project->is_featured ? 'Unfeature' : 'Feature' }}
                                </button>
                            </form>

                            {{-- Delete --}}
                            <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST" onsubmit="return confirm('Hapus projek ini secara permanen?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-500 transition" title="Hapus Projek">
                                    <i class='bx bx-trash text-lg'></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection