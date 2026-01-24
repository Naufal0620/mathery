@extends('layouts.student')

@section('content')
<div class="container py-4">
    <div class="mb-6 bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-600">
        <h1 class="text-2xl font-bold text-gray-800">{{ $course->name }}</h1>
        <p class="text-gray-600 mt-1">
            <span class="font-semibold">{{ $course->code }}</span> &bull; Dosen: {{ $course->teacher->full_name }}
        </p>
        <p class="mt-3 text-gray-700">{{ $course->description }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 space-y-4">
            <h3 class="text-xl font-bold border-b pb-2 mb-3">Materi & Pertemuan</h3>
            
            @forelse($course->topics as $topic)
                <div class="bg-white p-4 rounded-lg border hover:border-blue-300 transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <h5 class="font-bold text-lg text-blue-900">{{ $topic->name }}</h5>
                            <p class="text-sm text-gray-500 mb-2">
                                <i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($topic->meeting_date)->translatedFormat('d F Y') }}
                            </p>
                            <p class="text-gray-700">{{ $topic->description }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 italic">Belum ada topik pertemuan yang dibuat dosen.</p>
            @endforelse
        </div>

        <div class="space-y-4">
            <h3 class="text-xl font-bold border-b pb-2 mb-3">Kelompok Belajar</h3>
            
            {{-- Status Saya --}}
            @if($membership->group_id)
                @php 
                    $myGroup = $course->groups->where('id', $membership->group_id)->first(); 
                @endphp
                <div class="bg-green-50 border border-green-200 p-4 rounded mb-4">
                    <p class="font-semibold text-green-800">Anda anggota: {{ $myGroup->name ?? 'Kelompok' }}</p>
                    
                    @if($membership->is_requesting_group_leave)
                         <button class="w-full mt-2 py-1 bg-gray-400 text-white text-sm rounded cursor-not-allowed">Menunggu Persetujuan Keluar</button>
                    @else
                        <form action="{{ route('student.group.leave', $course->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full mt-2 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded">Ajukan Keluar</button>
                        </form>
                    @endif
                </div>
            @endif

            {{-- Daftar Semua Kelompok --}}
            @forelse($course->groups as $group)
                <div class="bg-white border rounded p-3 {{ $membership->group_id == $group->id ? 'ring-2 ring-green-400' : '' }}">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-bold">{{ $group->name }}</span>
                        <span class="text-xs bg-gray-200 px-2 py-1 rounded">
                            {{ $group->students->count() }}/{{ $group->max_slots }}
                        </span>
                    </div>
                    
                    {{-- Logic Tombol Gabung --}}
                    @if(!$membership->group_id && !$group->isFull())
                        <form action="{{ route('student.group.join', $group->id) }}" method="POST">
                            @csrf
                            <button class="w-full py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">Gabung</button>
                        </form>
                    @elseif(!$membership->group_id && $group->isFull())
                         <button disabled class="w-full py-1 bg-gray-300 text-gray-500 text-xs rounded">Penuh</button>
                    @endif

                    {{-- List Anggota (Optional: Dropdown/Accordion) --}}
                    <div class="mt-2 text-xs text-gray-500">
                        @foreach($group->students as $member)
                            <div>• {{ $member->full_name }}</div>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Belum ada kelompok dibentuk.</p>
            @endforelse

        </div>
    </div>
</div>
@endsection