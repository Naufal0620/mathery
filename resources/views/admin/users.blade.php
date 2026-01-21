@extends('layouts.admin')

@section('title', 'Data Mahasiswa - Mathery')
@section('header_title', 'Data Mahasiswa')

@section('content')
<div class="fade-in">
    <div class="bg-white rounded-2xl border border-gray-200 p-8 flex flex-col items-center justify-center text-center min-h-[300px]">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
            <i class='bx bx-education bx-md text-gray-400'></i>
        </div>
        <h3 class="text-lg font-bold text-gray-800">Modul Data Mahasiswa</h3>
        <p class="text-gray-500 max-w-md mt-2">Halaman ini akan menampilkan daftar seluruh mahasiswa, status enrollment, dan manajemen akun pengguna.</p>
        <button class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
            Tambah Mahasiswa
        </button>
    </div>
</div>
@endsection