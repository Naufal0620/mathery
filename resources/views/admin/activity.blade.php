@extends('layouts.admin')

@section('title', 'Riwayat Aktivitas - Mathery')
@section('header_title', 'Riwayat Aktivitas Sistem')

@section('content')
<div class="fade-in">
    <!-- Filter Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <div class="relative w-full sm:w-64">
            <input type="text" placeholder="Cari aktivitas..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            <i class='bx bx-search absolute left-3 top-2.5 text-gray-400'></i>
        </div>
        <div class="flex gap-2">
            <select class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option>Semua Kelas</option>
                <option>Kalkulus A</option>
                <option>Aljabar B</option>
            </select>
            <button class="bg-white border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">
                <i class='bx bx-filter-alt'></i> Filter
            </button>
        </div>
    </div>

    <!-- Full Table -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Log Aktivitas Sistem</h3>
            <button class="text-sm text-gray-500 hover:text-indigo-600"><i class='bx bx-export mr-1'></i> Export CSV</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[700px]">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-semibold tracking-wider">
                    <tr>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Aksi</th>
                        <th class="px-6 py-4">Detail</th>
                        <th class="px-6 py-4">Kelas</th>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    <!-- Row 1 -->
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name=Naufal&background=random" class="w-8 h-8 rounded-full">
                                <div>
                                    <p class="font-bold text-gray-800">Naufal</p>
                                    <p class="text-xs text-gray-500">Mahasiswa</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4"><span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">Upload</span></td>
                        <td class="px-6 py-4 text-gray-600">Mengupload file <span class="font-medium text-gray-800">Laporan_Akhir.pdf</span></td>
                        <td class="px-6 py-4"><span class="bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded text-xs">Kalkulus A</span></td>
                        <td class="px-6 py-4 text-gray-500">2 menit lalu</td>
                        <td class="px-6 py-4 text-xs text-gray-400 font-mono">192.168.1.10</td>
                    </tr>
                    <!-- Row 2 -->
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name=Siti&background=random" class="w-8 h-8 rounded-full">
                                <div>
                                    <p class="font-bold text-gray-800">Siti Aminah</p>
                                    <p class="text-xs text-gray-500">Mahasiswa</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4"><span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-bold">Join</span></td>
                        <td class="px-6 py-4 text-gray-600">Bergabung menggunakan kode kelas</td>
                        <td class="px-6 py-4"><span class="bg-purple-50 text-purple-700 px-2 py-0.5 rounded text-xs">Aljabar B</span></td>
                        <td class="px-6 py-4 text-gray-500">15 menit lalu</td>
                        <td class="px-6 py-4 text-xs text-gray-400 font-mono">10.0.0.52</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100 flex justify-between items-center">
            <p class="text-xs text-gray-500">Menampilkan 1-4 dari 124 log</p>
            <div class="flex gap-2">
                <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50 text-xs">Prev</button>
                <button class="px-3 py-1 bg-indigo-600 text-white rounded text-xs">1</button>
                <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50 text-xs">2</button>
                <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50 text-xs">Next</button>
            </div>
        </div>
    </div>
</div>
@endsection