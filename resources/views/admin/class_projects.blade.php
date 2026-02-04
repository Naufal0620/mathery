@extends('layouts.admin')

@section('title', 'Galeri Projek - Mathery')
@section('header_title', 'Galeri Projek Mahasiswa')

@section('content')
<div class="fade-in space-y-6">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div class="w-full md:w-auto">
            <h2 class="text-xl font-bold text-gray-800 mb-1">Hasil Karya Mahasiswa</h2>
            <p class="text-sm text-gray-500 mb-4">Lihat dan kelola pengumpulan tugas akhir atau projek kelas.</p>
            
            <form action="{{ route('admin.projects.index') }}" method="GET" class="relative max-w-xs">
                <i class='bx bx-filter-alt absolute left-3 top-1/2 -translate-y-1/2 text-gray-400'></i>
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
        
        @if(request('filter_class') && $projects->isNotEmpty())
        <div class="hidden md:flex gap-4">
            <div class="px-4 py-2 bg-indigo-50 rounded-xl border border-indigo-100 text-center">
                <span class="block text-xl font-bold text-indigo-600">{{ $projects->count() }}</span>
                <span class="text-xs text-indigo-400 font-medium uppercase">Total Karya</span>
            </div>
        </div>
        @endif
    </div>

    <div class="min-h-[300px]">
        @if($projects->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                @foreach($projects as $project)
                
                {{-- VALIDASI THUMBNAIL: Cek DB & Fisik File --}}
                @php
                    $hasThumbnail = !empty($project->thumbnail) && 
                                    $project->thumbnail !== 'project_default.jpg' && 
                                    \Illuminate\Support\Facades\Storage::disk('public')->exists($project->thumbnail);
                @endphp

                <div class="group bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col overflow-hidden h-full relative">
                    
                    @if($project->is_featured)
                    <div class="absolute top-3 right-3 z-20 bg-yellow-400 text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-md flex items-center gap-1">
                        <i class='bx bxs-star'></i> FEATURED
                    </div>
                    @endif

                    <div class="h-40 bg-gray-100 relative overflow-hidden group-hover:opacity-90 transition-opacity">
                        @if($hasThumbnail)
                            <img src="{{ asset('storage/' . $project->thumbnail) }}" alt="{{ $project->title }}" class="w-full h-full object-cover object-center transform group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-indigo-50 to-purple-50 flex items-center justify-center">
                                <div class="text-center">
                                    <i class='bx bx-code-block text-5xl text-indigo-200 mb-1'></i>
                                    <span class="block text-xs font-semibold text-indigo-300">
                                        {{ !empty($project->thumbnail) && $project->thumbnail !== 'project_default.jpg' ? 'Image Missing' : 'No Thumbnail' }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="p-5 flex-1 flex flex-col">
                        <div class="mb-4">
                            <h3 class="text-lg font-bold text-gray-800 leading-tight mb-2 line-clamp-1 group-hover:text-indigo-600 transition-colors" title="{{ $project->title }}">
                                {{ $project->title }}
                            </h3>
                            
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-600 border border-indigo-200">
                                    <i class='bx bx-user'></i>
                                </div>
                                <div class="flex flex-col">
                                    @if($project->group)
                                        <span class="text-xs font-bold text-gray-700">{{ $project->group->name }}</span>
                                    @else
                                        <span class="text-xs font-bold text-gray-700">{{ $project->student->full_name }}</span>
                                    @endif
                                </div>
                            </div>

                            <p class="text-sm text-gray-500 line-clamp-2">
                                {{ $project->description ?? 'Tidak ada deskripsi.' }}
                            </p>
                        </div>

                        <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between gap-2">
                            <button onclick="showDetail(
                                '{{ addslashes($project->title) }}', 
                                '{{ addslashes($project->description ?? 'Tidak ada deskripsi.') }}', 
                                '{{ $project->student->full_name }}', 
                                '{{ $project->group ? $project->group->name : 'Individu' }}',
                                '{{ $project->repo_url }}', 
                                '{{ $project->project_url }}',
                                '{{ $project->created_at->format('d M Y') }}'
                            )" class="flex-1 px-3 py-2 bg-gray-50 text-gray-600 text-xs font-bold rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition-colors flex items-center justify-center gap-1">
                                <i class='bx bx-info-circle text-base'></i> Detail
                            </button>

                            <form action="{{ route('admin.projects.feature', $project->id) }}" method="POST">
                                @csrf @method('PUT')
                                <button type="submit" class="px-3 py-2 {{ $project->is_featured ? 'bg-yellow-50 text-yellow-500 border-yellow-200' : 'bg-gray-50 text-gray-400 hover:text-yellow-500' }} border border-transparent text-xs font-bold rounded-lg transition-colors flex items-center justify-center" title="{{ $project->is_featured ? 'Hapus Unggulan' : 'Jadikan Unggulan' }}">
                                    <i class='{{ $project->is_featured ? 'bxf' : 'bx' }} bx-star text-base'></i>
                                </button>
                            </form>

                            <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST" onsubmit="return deleteConfirm(event)">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-3 py-2 bg-gray-50 text-gray-600 text-xs font-bold rounded-lg hover:bg-red-50 hover:text-red-600 transition-colors flex items-center justify-center" title="Hapus">
                                    <i class='bx bx-trash text-base'></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        @elseif(request('filter_class'))
            <div class="text-center py-20 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 mt-4">
                <div class="bg-indigo-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-folder-minus text-3xl text-indigo-500'></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Belum ada Projek</h3>
                <p class="text-gray-500">Mahasiswa di kelas ini belum mengumpulkan tugas/projek apapun.</p>
            </div>

        @else
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="bg-white p-6 rounded-full shadow-xl shadow-indigo-100 mb-6 animate-bounce-slow">
                    <i class='bx bx-select-none text-5xl text-indigo-600'></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Pilih Kelas Terlebih Dahulu</h3>
                <p class="text-gray-500 max-w-md mx-auto">
                    Silakan pilih kelas melalui dropdown di atas untuk melihat galeri projek mahasiswa.
                </p>
            </div>
        @endif
    </div>

    <div id="detailProjectModal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto" onclick="closeModal('detailProjectModal')">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 w-full max-w-lg p-0 fade-in" onclick="event.stopPropagation()">
                    
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                        <h3 class="text-xl font-bold text-white mb-1" id="modalTitle">Judul Projek</h3>
                        <p class="text-indigo-100 text-sm flex items-center gap-2">
                            <i class='bx bx-time'></i> Dikumpulkan: <span id="modalDate"></span>
                        </p>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="flex items-start gap-4 p-3 bg-gray-50 rounded-xl border border-gray-100">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 shrink-0">
                                <i class='bx bx-user text-xl'></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Author</p>
                                <p class="text-sm font-bold text-gray-800" id="modalStudent">Nama</p>
                                <p class="text-xs text-gray-500" id="modalGroup">Kelompok</p>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-2">Deskripsi Projek</p>
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-sm text-gray-600 leading-relaxed max-h-40 overflow-y-auto custom-scrollbar" id="modalDesc"></div>
                        </div>

                        <div class="pt-4 border-t border-gray-100 flex flex-col gap-2">
                            <a href="#" id="modalRepo" target="_blank" class="w-full py-2.5 bg-gray-800 text-white font-bold rounded-xl shadow hover:bg-gray-900 transition-all flex items-center justify-center gap-2">
                                <i class='bx bxl-github text-xl'></i> Buka Repository
                            </a>
                            <a href="#" id="modalDemo" target="_blank" class="hidden w-full py-2.5 bg-blue-600 text-white font-bold rounded-xl shadow hover:bg-blue-700 transition-all items-center justify-center gap-2">
                                <i class='bx bx-globe text-xl'></i> Lihat Live Demo
                            </a>
                        </div>
                    </div>

                    <button onclick="closeModal('detailProjectModal')" class="absolute top-4 right-4 text-white/70 hover:text-white transition-colors">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    function showDetail(title, desc, student, group, repoUrl, projectUrl, date) {
        document.getElementById('modalTitle').innerText = title;
        document.getElementById('modalDesc').innerText = desc; 
        document.getElementById('modalStudent').innerText = student;
        document.getElementById('modalGroup').innerText = group;
        document.getElementById('modalDate').innerText = date;
        
        // Setup Repo Button
        const repoBtn = document.getElementById('modalRepo');
        if(repoUrl && repoUrl !== 'null') {
            repoBtn.href = repoUrl;
            repoBtn.classList.remove('hidden');
            repoBtn.classList.add('flex');
        } else {
            repoBtn.classList.add('hidden');
            repoBtn.classList.remove('flex');
        }

        // Setup Demo Button
        const demoBtn = document.getElementById('modalDemo');
        if(projectUrl && projectUrl !== 'null') {
            demoBtn.href = projectUrl;
            demoBtn.classList.remove('hidden');
            demoBtn.classList.add('flex');
        } else {
            demoBtn.classList.add('hidden');
            demoBtn.classList.remove('flex');
        }
        
        openModal('detailProjectModal');
    }

    function deleteConfirm(event) {
        event.preventDefault();
        var form = event.target;
        Swal.fire({
            title: 'Hapus Projek?',
            text: "Data projek akan dihapus secara permanen.",
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