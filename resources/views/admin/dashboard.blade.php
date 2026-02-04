@extends('layouts.admin')

@section('title', 'Dashboard - Mathery Admin')
@section('header_title', 'Dashboard Overview')

@section('content')
<div class="fade-in space-y-6">
    
    <div class="relative bg-white rounded-2xl p-6 shadow-sm border border-gray-100 overflow-hidden">
        <div class="relative z-10">
            <h2 class="text-2xl font-bold text-gray-800 mb-1">Selamat Datang, {{ Auth::user()->full_name ?? 'Admin' }}! 👋</h2>
            <p class="text-gray-500 text-sm">Berikut adalah ringkasan aktivitas akademik di platform Mathery hari ini.</p>
        </div>
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 rounded-full bg-gradient-to-br from-indigo-50 to-purple-50 opacity-50 blur-2xl"></div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">
        <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Mahasiswa</p>
                    <h3 class="text-3xl font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">{{ $stats['students'] }}</h3>
                </div>
                <div class="w-12 h-12 flex justify-center items-center bg-indigo-50 text-indigo-600 rounded-xl group-hover:scale-110 transition-transform">
                    <i class='bx bx-user-voice bx-sm'></i>
                </div>
            </div>
        </div>
        <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Projek</p>
                    <h3 class="text-3xl font-bold text-gray-800 group-hover:text-purple-600 transition-colors">{{ $stats['projects'] }}</h3>
                </div>
                <div class="w-12 h-12 flex justify-center items-center bg-purple-50 text-purple-600 rounded-xl group-hover:scale-110 transition-transform">
                    <i class='bx bx-folder-open bx-sm'></i>
                </div>
            </div>
        </div>
        <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Materi Ajar</p>
                    <h3 class="text-3xl font-bold text-gray-800 group-hover:text-blue-600 transition-colors">{{ $stats['materials'] }}</h3>
                </div>
                <div class="w-12 h-12 flex justify-center items-center bg-blue-50 text-blue-600 rounded-xl group-hover:scale-110 transition-transform">
                    <i class='bx bx-book-content bx-sm'></i>
                </div>
            </div>
        </div>
        <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Topik Pertemuan</p>
                    <h3 class="text-3xl font-bold text-gray-800 group-hover:text-orange-600 transition-colors">{{ $stats['topics'] }}</h3>
                </div>
                <div class="w-12 h-12 flex justify-center items-center bg-orange-50 text-orange-600 rounded-xl group-hover:scale-110 transition-transform">
                    <i class='bx bx-calendar-event bx-sm'></i>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-3 rounded-2xl bg-white border border-gray-200 shadow-sm flex flex-col h-full">
        <div class="flex flex-row justify-between items-center px-6 py-5 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">Aktivitas Terbaru</h3>
            <a href="{{ route('admin.activity') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1 transition-colors">
                Lihat Semua <i class='bx bx-right-arrow-alt'></i>
            </a>
        </div>
        
        <div class="p-6 flex-1 overflow-y-auto max-h-[500px] custom-scrollbar">
            @if($recentActivities->isNotEmpty())
                <div class="relative space-y-6">
                    <div class="absolute left-5 top-2 bottom-2 w-0.5 bg-gray-100"></div>

                    @foreach($recentActivities as $activity)
                    <div class="relative pl-12 group">
                        <div class="absolute left-0 top-1 w-10 h-10 rounded-full {{ $activity['color'] }} flex items-center justify-center border-4 border-white shadow-sm z-10">
                            <i class='bx {{ $activity['icon'] }} text-lg'></i>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-bold text-gray-800">{{ $activity['user_name'] }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase {{ $activity['user_role'] == 'Teacher' ? 'bg-indigo-100 text-indigo-700' : 'bg-green-100 text-green-700' }}">
                                        {{ $activity['user_role'] }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">{{ $activity['action'] }}:</span> {{ $activity['title'] }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $activity['description'] }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-xs text-gray-400 block">{{ $activity['created_at']->diffForHumans() }}</span>
                                <a href="{{ $activity['link'] }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold mt-1 inline-block">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-full py-10 text-gray-400">
                    <i class='bx bx-sleepy bx-md text-gray-300 mb-2'></i>
                    <p>Belum ada aktivitas tercatat hari ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection