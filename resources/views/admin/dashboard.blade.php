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
                    <h3 class="text-3xl font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">
                        {{ $stats['students'] }}
                    </h3>
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
                    <h3 class="text-3xl font-bold text-gray-800 group-hover:text-purple-600 transition-colors">
                        {{ $stats['projects'] }}
                    </h3>
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
                    <h3 class="text-3xl font-bold text-gray-800 group-hover:text-blue-600 transition-colors">
                        {{ $stats['materials'] }}
                    </h3>
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
                    <h3 class="text-3xl font-bold text-gray-800 group-hover:text-orange-600 transition-colors">
                        {{ $stats['topics'] }}
                    </h3>
                </div>
                <div class="w-12 h-12 flex justify-center items-center bg-orange-50 text-orange-600 rounded-xl group-hover:scale-110 transition-transform">
                    <i class='bx bx-calendar-event bx-sm'></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-3 rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden flex flex-col">
            <div class="flex flex-row justify-between items-center px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Aktivitas Terbaru</h3>
                <a href="{{ route('admin.activity') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1 transition-colors">
                    Lihat Semua <i class='bx bx-right-arrow-alt'></i>
                </a>
            </div>
            
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left min-w-[500px]">
                    <thead class="bg-gray-50/50 text-gray-500 text-xs uppercase font-semibold tracking-wider border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Pengguna</th>
                            <th class="px-6 py-4">Aktivitas</th>
                            <th class="px-6 py-4">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($recentActivities as $activity)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-bold text-xs">
                                        {{ substr($activity['actor'], 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-gray-800">{{ $activity['actor'] }}</span>
                                        <span class="text-xs text-gray-500">{{ $activity['class'] }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="flex items-center gap-2 text-gray-700">
                                    <i class='bx {{ $activity['icon'] }} {{ $activity['color'] }} text-lg'></i> 
                                    {{ $activity['action'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-400 text-xs whitespace-nowrap">
                                {{ $activity['time']->diffForHumans() }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class='bx bx-sleepy bx-md text-gray-300'></i>
                                    <p>Belum ada aktivitas tercatat hari ini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection