@extends('layouts.admin')

@section('title', 'Manajemen Kelompok - ' . $course->name)

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
                   class="border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                   <i class='bx bx-layer mr-2 text-lg'></i> Manajemen Kelompok
                </a>
            </nav>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- KOLOM KIRI: FORM BUAT KELOMPOK --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 sticky top-6 overflow-hidden">
                
                <div class="p-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class='bx bx-folder-plus text-blue-500 text-xl'></i> Buat Kelompok
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Buat kelompok baru berdasarkan topik.</p>
                </div>
                
                <div class="p-5">
                    <form action="{{ route('admin.groups.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="class_id" value="{{ $course->id }}">

                        <div class="space-y-4">
                            {{-- Input Topik --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Topik Pembahasan</label>
                                <div class="relative">
                                    <select name="topic_id" required class="w-full bg-white border border-gray-300 text-gray-700 rounded-lg py-2.5 pl-3 pr-10 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition shadow-sm appearance-none">
                                        <option value="">-- Pilih Topik --</option>
                                        @foreach($topics as $topic)
                                            <option value="{{ $topic->id }}">{{ $topic->name }} ({{ \Carbon\Carbon::parse($topic->meeting_date)->format('d M') }})</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                        <i class='bx bx-chevron-down text-lg'></i>
                                    </div>
                                </div>
                            </div>

                            {{-- Input Nama --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Nama Kelompok</label>
                                <div class="relative">
                                    <input type="text" name="name" required placeholder="Contoh: Kelompok 1" 
                                        class="w-full bg-white border border-gray-300 text-gray-700 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class='bx bx-text text-gray-400 text-lg'></i>
                                    </div>
                                </div>
                            </div>

                            {{-- Input Slot --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Kuota Anggota</label>
                                <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden shadow-sm">
                                    <button type="button" onclick="document.getElementById('slots').stepDown()" class="px-3 py-2 bg-gray-50 hover:bg-gray-100 border-r border-gray-300 text-gray-600 transition">
                                        <i class='bx bx-minus'></i>
                                    </button>
                                    <input type="number" id="slots" name="max_slots" value="5" min="1" required class="w-full text-center border-none focus:ring-0 text-sm py-2 bg-white h-full text-gray-700 font-medium">
                                    <button type="button" onclick="document.getElementById('slots').stepUp()" class="px-3 py-2 bg-gray-50 hover:bg-gray-100 border-l border-gray-300 text-gray-600 transition">
                                        <i class='bx bx-plus'></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-lg shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-2 mt-2">
                                <i class='bx bx-save text-lg'></i> Simpan Kelompok
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: DAFTAR KELOMPOK --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="flex justify-between items-center">
                <h3 class="font-bold text-gray-800 text-lg">Daftar Kelompok Aktif</h3>
                <span class="bg-blue-50 text-blue-600 text-xs font-bold px-3 py-1 rounded-full border border-blue-100">
                    Total: {{ $groups->count() }}
                </span>
            </div>

            @forelse($groups as $group)
                {{-- Card Kelompok (Style mirip "Pending Requests") --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all">
                    {{-- Header Card --}}
                    <div class="bg-gray-50/40 px-5 py-3 border-b border-gray-100 flex justify-between items-center">
                        <div>
                            <h4 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                                {{ $group->name }}
                                @if($group->isFull())
                                    <span class="text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded-full border border-red-200">PENUH</span>
                                @endif
                            </h4>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs text-gray-500 flex items-center gap-1">
                                    <i class='bx bx-tag'></i> {{ $group->topic->name ?? 'Tanpa Topik' }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4">
                            {{-- Progress Bar Slot --}}
                            <div class="text-right hidden sm:block">
                                <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-wide mb-1">
                                    {{ $group->students->count() }} / {{ $group->max_slots }} Anggota
                                </span>
                                <div class="w-24 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full {{ $group->isFull() ? 'bg-red-500' : 'bg-green-500' }}" style="width: {{ ($group->students->count() / $group->max_slots) * 100 }}%"></div>
                                </div>
                            </div>
                            
                            {{-- Tombol Hapus --}}
                            <form action="{{ route('admin.groups.destroy', $group->id) }}" method="POST" onsubmit="return confirm('Hapus kelompok ini? Anggota akan dikeluarkan.');">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition border border-transparent hover:border-red-100" title="Hapus Kelompok">
                                    <i class='bx bx-trash text-lg'></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Body Card (List Siswa) --}}
                    <div class="p-5">
                        @if($group->students->isEmpty())
                            <div class="text-center py-6 border-2 border-dashed border-gray-100 rounded-lg bg-gray-50/30">
                                <p class="text-xs text-gray-400 italic">Belum ada mahasiswa yang bergabung.</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($group->students as $student)
                                    <div class="flex justify-between items-center p-3 bg-white border border-gray-100 rounded-lg shadow-sm hover:border-blue-200 transition group">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-bold border border-indigo-100">
                                                {{ substr($student->full_name, 0, 1) }}
                                            </div>
                                            <div class="overflow-hidden">
                                                <p class="text-sm font-semibold text-gray-700 truncate max-w-[120px]">{{ $student->full_name }}</p>
                                                <p class="text-[10px] text-gray-400">{{ $student->username }}</p>
                                            </div>
                                        </div>

                                        {{-- Indikator Request Leave --}}
                                        @if($student->pivot->is_requesting_group_leave)
                                            <div class="flex items-center gap-1 bg-orange-50 px-2 py-1 rounded-lg border border-orange-100">
                                                <span class="text-[10px] text-orange-600 font-bold uppercase mr-1">Out?</span>
                                                <form action="{{ route('admin.groups.leave.approve', [$course->id, $student->id]) }}" method="POST" class="inline">
                                                    @csrf @method('PUT')
                                                    <button class="text-green-600 hover:bg-green-100 p-0.5 rounded transition" title="Setujui"><i class='bx bx-check text-lg'></i></button>
                                                </form>
                                                <form action="{{ route('admin.groups.leave.reject', [$course->id, $student->id]) }}" method="POST" class="inline">
                                                    @csrf @method('PUT')
                                                    <button class="text-red-500 hover:bg-red-100 p-0.5 rounded transition" title="Tolak"><i class='bx bx-x text-lg'></i></button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-[10px] text-green-600 bg-green-50 px-2 py-0.5 rounded-full border border-green-100 font-medium">Aktif</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-12 bg-white rounded-xl border border-gray-200 shadow-sm text-center">
                    <div class="w-14 h-14 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                        <i class='bx bx-layer text-3xl text-gray-300'></i>
                    </div>
                    <p class="text-gray-600 font-medium">Belum ada kelompok</p>
                    <p class="text-xs text-gray-400 mt-1 max-w-xs">Gunakan form di sebelah kiri untuk membuat kelompok baru.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection