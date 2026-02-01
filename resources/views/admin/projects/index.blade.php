@extends('layouts.admin')

@section('title', 'Galeri Projek Mahasiswa')

@section('content')
<div class="container-fluid px-4 py-6">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Galeri Projek</h1>
            <p class="text-sm text-gray-500">Kelola dan kurasi projek hasil karya mahasiswa.</p>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-8">
        <form action="{{ route('admin.projects.index') }}" method="GET" class="flex items-end gap-4">
            <div class="w-full md:w-1/3">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 block">Filter Kelas</label>
                <div class="relative">
                    <select name="class_id" onchange="this.form.submit()" class="w-full appearance-none bg-gray-50 border border-gray-300 text-gray-700 py-2.5 px-4 pr-8 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 border-r-[12px] border-transparent outline-none">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500"><i class='bx bx-chevron-down text-lg'></i></div>
                </div>
            </div>
            @if(request('class_id'))
                <a href="{{ route('admin.projects.index') }}" class="text-red-500 hover:text-red-700 text-sm font-bold mb-2.5 flex items-center gap-1">
                    <i class='bx bx-reset'></i> Reset
                </a>
            @endif
        </form>
    </div>

    {{-- GRID PROJEK --}}
    @if($projects->isEmpty())
        <div class="text-center py-20 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
            <i class='bx bx-images text-4xl text-gray-300 mb-2'></i>
            <p class="text-gray-500">Belum ada projek yang diupload.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($projects as $project)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-200 overflow-hidden flex flex-col h-full relative group">
                    
                    {{-- Badge Featured --}}
                    @if($project->is_featured)
                        <div class="absolute top-3 right-3 z-10 bg-yellow-400 text-yellow-900 text-[10px] font-black px-2 py-1 rounded shadow-sm flex items-center gap-1">
                            <i class='bx bxs-star'></i> FEATURED
                        </div>
                    @endif

                    {{-- Thumbnail --}}
                    <div class="h-40 bg-gray-100 overflow-hidden relative">
                        @if($project->thumbnail && $project->thumbnail != 'project_default.jpg')
                            <img src="{{ asset('storage/' . $project->thumbnail) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300 bg-gray-50">
                                <i class='bx bx-image text-4xl'></i>
                            </div>
                        @endif
                        <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-black/60 to-transparent p-3">
                            <p class="text-white text-xs font-bold truncate">{{ $project->course->name }}</p>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="p-4 flex-1 flex flex-col">
                        <h3 class="font-bold text-gray-800 text-base leading-tight mb-1 line-clamp-2">{{ $project->title }}</h3>
                        
                        <div class="flex items-center gap-2 mb-3 mt-1">
                            <span class="text-xs bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded border border-indigo-100 font-semibold truncate max-w-[150px]">
                                <i class='bx bx-group'></i> {{ $project->group->name ?? 'No Group' }}
                            </span>
                        </div>

                        <p class="text-gray-500 text-sm line-clamp-2 mb-4 flex-1">{{ $project->description }}</p>

                        {{-- ACTIONS --}}
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100 mt-auto">
                            {{-- Toggle Feature --}}
                            <form action="{{ route('admin.projects.feature', $project->id) }}" method="POST">
                                @csrf @method('PUT')
                                <button type="submit" class="flex items-center gap-1 text-xs font-bold {{ $project->is_featured ? 'text-yellow-600 hover:text-yellow-700' : 'text-gray-400 hover:text-yellow-500' }} transition">
                                    <i class='bx {{ $project->is_featured ? 'bxs-star' : 'bx-star' }} text-base'></i>
                                    {{ $project->is_featured ? 'Unfeature' : 'Feature' }}
                                </button>
                            </form>

                            {{-- Delete --}}
                            <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST" onsubmit="return confirm('Hapus projek ini secara permanen?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-600 transition" title="Hapus">
                                    <i class='bx bx-trash text-lg'></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $projects->links() }}</div>
    @endif
</div>
@endsection