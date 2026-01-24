@extends('layouts.student')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 font-bold text-xl">Kelas Saya</h2>

    @if($myClasses->isEmpty())
        <div class="alert alert-info">
            Anda belum bergabung dengan kelas manapun. 
            <a href="{{ route('student.dashboard') }}" class="underline">Cari kelas di Dashboard</a>.
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($myClasses as $class)
                <a href="{{ route('student.class.show', $class->id) }}" class="block no-underline hover:shadow-lg transition">
                    <div class="card bg-white border rounded-lg overflow-hidden h-full">
                        <div class="p-4 bg-blue-600 text-white">
                            <h5 class="font-bold text-lg">{{ $class->name }}</h5>
                            <span class="text-sm opacity-90">{{ $class->code }}</span>
                        </div>
                        <div class="p-4">
                            <p class="text-gray-600 mb-2">
                                <i class="fas fa-chalkboard-teacher mr-1"></i> 
                                {{ $class->teacher->full_name }}
                            </p>
                            <p class="text-sm text-gray-500 line-clamp-2">
                                {{ $class->description ?? 'Tidak ada deskripsi.' }}
                            </p>
                        </div>
                        <div class="px-4 py-3 bg-gray-50 text-right">
                            <span class="text-blue-600 text-sm font-semibold">Lihat Detail &rarr;</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection