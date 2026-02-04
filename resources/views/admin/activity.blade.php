@extends('layouts.admin')

@section('title', 'Riwayat Aktivitas - Mathery')
@section('header_title', 'Log Aktivitas')

@section('content')
<div class="fade-in space-y-6">
    
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-gray-800 mb-1">Jejak Aktivitas</h2>
            <p class="text-sm text-gray-500">Memantau kegiatan terbaru yang terjadi di seluruh platform.</p>
        </div>
        
        <div class="flex gap-4">
            <div class="text-right">
                <span class="block text-2xl font-bold text-indigo-600">{{ $activities->total() }}</span>
                <span class="text-xs text-gray-400 uppercase font-medium">Log Tersimpan</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 md:p-8 relative min-h-[400px]">
        
        @if($activities->isNotEmpty())
            <div class="absolute left-8 md:left-12 top-8 bottom-8 w-0.5 bg-gray-100"></div>

            <div class="space-y-8 relative">
                @php 
                    $lastDate = null; 
                @endphp

                @foreach($activities as $log)
                    @php
                        $currentDate = $log['created_at']->format('Y-m-d');
                        $isToday = $log['created_at']->isToday();
                        $isYesterday = $log['created_at']->isYesterday();
                        
                        // Label Tanggal
                        if ($isToday) $dateLabel = "Hari Ini";
                        elseif ($isYesterday) $dateLabel = "Kemarin";
                        else $dateLabel = $log['created_at']->isoFormat('dddd, D MMMM Y');
                    @endphp

                    {{-- Group Header: Muncul setiap ganti tanggal --}}
                    @if($lastDate !== $currentDate)
                        <div class="relative pl-12 md:pl-16">
                            <span class="inline-block py-1 px-3 rounded-full bg-gray-100 text-gray-600 text-xs font-bold border border-gray-200 shadow-sm">
                                {{ $dateLabel }}
                            </span>
                        </div>
                        @php $lastDate = $currentDate; @endphp
                    @endif

                    {{-- Activity Item --}}
                    <div class="relative pl-12 md:pl-16 group">
                        
                        <div class="absolute left-0 top-1 w-10 h-10 rounded-full {{ $log['color'] }} flex items-center justify-center border-4 border-white shadow-sm z-10">
                            <i class='bx {{ $log['icon'] }} text-lg'></i>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-gray-50/50 rounded-xl border border-gray-100 hover:bg-white hover:border-indigo-100 hover:shadow-md transition-all duration-300">
                            
                            <div class="mb-2 sm:mb-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-bold text-gray-800">{{ $log['user_name'] }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase {{ $log['user_role'] == 'Teacher' ? 'bg-indigo-100 text-indigo-700' : 'bg-green-100 text-green-700' }}">
                                        {{ $log['user_role'] }}
                                    </span>
                                    <span class="text-xs text-gray-400">• {{ $log['created_at']->format('H:i') }} WIB</span>
                                </div>
                                
                                <p class="text-sm text-gray-600">
                                    <span class="font-semibold text-gray-700">{{ $log['action'] }}:</span> 
                                    {{ $log['title'] }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $log['description'] }}</p>
                            </div>

                            <div>
                                <a href="{{ $log['link'] }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-600 hover:text-indigo-600 hover:border-indigo-200 transition-colors shadow-sm">
                                    Lihat Detail <i class='bx bx-right-arrow-alt'></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100">
                {{ $activities->links() }}
            </div>

        @else
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class='bx bx-time-five text-3xl text-gray-400'></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Belum Ada Aktivitas</h3>
                <p class="text-gray-500">Aktivitas sistem akan muncul di sini.</p>
            </div>
        @endif
    </div>
</div>
@endsection