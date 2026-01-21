@extends('layouts.admin')

@section('title', 'Silabus & Topik - Mathery')
@section('header_title', 'Silabus & Topik')

@section('content')
<div class="fade-in">
    <div class="bg-white p-5 rounded-2xl border border-gray-200 mb-6 flex flex-col sm:flex-row items-start sm:items-center gap-4 shadow-sm">
        <div class="flex items-center gap-2 text-gray-600 w-full sm:w-auto">
            <i class='bx bx-filter-alt'></i>
            <span class="font-medium text-sm">Filter Kelas:</span>
        </div>
        <select class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none w-full sm:w-64">
            <option>Kalkulus II - Kelas A</option>
            <option>Aljabar Linear - Kelas B</option>
        </select>
        <button class="text-indigo-600 text-sm font-medium hover:underline sm:ml-auto flex items-center gap-1">
            <i class='bx bx-copy'></i> Salin Silabus
        </button>
    </div>

    <div class="flex justify-between items-center mb-4">
        <h3 class="font-bold text-lg text-gray-800">Daftar Pertemuan</h3>
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-xl text-sm font-medium hover:bg-gray-50 shadow-sm">
            <i class='bx-fw bx-plus mr-1 font-semibold'></i> <span class="hidden sm:inline">Tambah Topik</span>
        </button>
    </div>

    <div class="space-y-4">
        <!-- Item 1 -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between shadow-sm hover:shadow-md transition gap-4">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center font-bold shrink-0">1</div>
                <div>
                    <h4 class="font-bold text-gray-800 text-sm sm:text-base">Pendahuluan & Kontrak Kuliah</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Minggu 1 • Belum ada kelompok</p>
                </div>
            </div>
            <button class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-indigo-600 ml-auto sm:ml-0">
                <i class='bx bx-pencil'></i>
            </button>
        </div>

        <!-- Item 2 -->
        <div class="bg-white rounded-xl border border-indigo-200 p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between shadow-sm hover:shadow-md transition relative overflow-hidden gap-4">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-indigo-500"></div>
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold shrink-0">2</div>
                <div>
                    <h4 class="font-bold text-gray-800 text-sm sm:text-base">Limit Fungsi Aljabar</h4>
                    <div class="flex flex-wrap items-center gap-2 mt-0.5">
                        <p class="text-xs text-gray-500">Minggu 2</p>
                        <span class="text-xs bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded font-medium">Kelompok 1</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3 ml-auto sm:ml-0">
                <span class="flex items-center gap-1 text-xs bg-orange-50 text-orange-600 px-2 py-1 rounded-md font-medium">
                    <i class='bx bx-file'></i> 3 File
                </span>
                <button class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-indigo-600">
                    <i class='bx bx-pencil'></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection