@extends('layouts.admin')

@section('title', 'Dashboard - Mathery Admin')
@section('header_title', 'Dashboard Overview')

@section('content')
<div class="fade-in">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5 mb-8">
        <div class="p-6 rounded-2xl text-white bg-gradient-to-r from-indigo-700 to-purple-500 shadow-lg shadow-indigo-200">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm opacity-90 font-medium uppercase tracking-wide mb-1">Total Mahasiswa</p>
                    <h3 class="text-3xl font-bold">142</h3>
                </div>
                <div class="w-14 p-2 flex justify-center items-center aspect-square bg-white/20 rounded-lg">
                    <i class='bx bx-education bx-md'></i>
                </div>
            </div>
        </div>
        
        <div class="p-6 rounded-2xl bg-white border border-gray-200 shadow-sm">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500 font-medium uppercase tracking-wide mb-1">Total Projek</p>
                    <h3 class="text-3xl font-bold text-gray-800">24</h3>
                </div>
                <div class="w-14 p-2 flex justify-center items-center aspect-square bg-purple-50 text-purple-600 rounded-lg">
                    <i class='bx bx-folder bx-md'></i>
                </div>
            </div>
        </div>

        <div class="p-6 rounded-2xl bg-white border border-gray-200 shadow-sm">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500 font-medium uppercase tracking-wide mb-1">Materi Upload</p>
                    <h3 class="text-3xl font-bold text-gray-800">89</h3>
                </div>
                <div class="w-14 p-2 flex justify-center items-center aspect-square bg-blue-50 text-blue-600 rounded-lg">
                    <i class='bx bx-file bx-md'></i>
                </div>
            </div>
        </div>

        <div class="p-6 rounded-2xl bg-white border border-gray-200 shadow-sm">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500 font-medium uppercase tracking-wide mb-1">Topik Belajar</p>
                    <h3 class="text-3xl font-bold text-gray-800">12</h3>
                </div>
                <div class="w-14 p-2 flex justify-center items-center aspect-square bg-orange-50 text-orange-600 rounded-lg">
                    <i class='bx bx-book-bookmark bx-md'></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Table (Preview) -->
    <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center px-6 py-5 border-b border-gray-100 gap-2">
            <h3 class="text-lg font-semibold text-gray-800">Aktivitas Terbaru</h3>
            <a href="{{ route('admin.activity') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Lihat Semua Log</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[600px]">
                <thead class="bg-gray-50/50 text-gray-500 text-xs uppercase font-semibold tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Mahasiswa / Kelompok</th>
                        <th class="px-6 py-4">Aktivitas</th>
                        <th class="px-6 py-4">Kelas</th>
                        <th class="px-6 py-4">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    <!-- Data Dummy Statis (Nanti diganti Foreach) -->
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-800">Kelompok 4 (Naufal)</td>
                        <td class="px-6 py-4">
                            <span class="flex items-center gap-2">
                                <i class='bx bx-upload text-green-500'></i> Upload Laporan.pdf
                            </span>
                        </td>
                        <td class="px-6 py-4"><span class="bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-lg text-xs font-bold">Kalkulus A</span></td>
                        <td class="px-6 py-4 text-gray-400">2 menit lalu</td>
                    </tr>
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-800">Siti Aminah</td>
                        <td class="px-6 py-4">
                            <span class="flex items-center gap-2">
                                <i class='bx bx-log-in-circle text-blue-500'></i> Bergabung ke Kelas
                            </span>
                        </td>
                        <td class="px-6 py-4"><span class="bg-purple-50 text-purple-700 px-2.5 py-1 rounded-lg text-xs font-bold">Aljabar B</span></td>
                        <td class="px-6 py-4 text-gray-400">15 menit lalu</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection