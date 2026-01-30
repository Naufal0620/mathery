@extends('layouts.student')

@section('content')
<div class="container py-8 max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('student.class.show', $course->id) }}" class="text-gray-500 hover:text-blue-600 text-sm flex items-center gap-1 mb-2">
            <i class="fas fa-arrow-left"></i> Kembali ke Kelas
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Upload Projek Kelas</h1>
        <p class="text-gray-600 text-sm">Bagikan hasil kerja Anda/Kelompok Anda di sini.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 md:p-8">
            <form action="{{ route('student.project.store', $course->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="space-y-6">
                    {{-- Judul --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Projek <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required placeholder="Misal: Sistem Pakar Penyakit Tanaman" 
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Singkat</label>
                        <textarea name="description" rows="4" placeholder="Jelaskan fitur utama dan teknologi yang digunakan..." 
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm"></textarea>
                    </div>

                    {{-- URLs --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Link Demo (Opsional)</label>
                            <div class="flex rounded-md shadow-sm">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                    <i class="fas fa-globe"></i>
                                </span>
                                <input type="url" name="project_url" placeholder="https://..." 
                                    class="flex-1 block w-full rounded-none rounded-r-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Link Repository (Opsional)</label>
                            <div class="flex rounded-md shadow-sm">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                    <i class="fab fa-github"></i>
                                </span>
                                <input type="url" name="repo_url" placeholder="https://github.com/..." 
                                    class="flex-1 block w-full rounded-none rounded-r-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                        </div>
                    </div>

                    {{-- Thumbnail --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Thumbnail / Screenshot</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:bg-gray-50 transition">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-image text-gray-400 text-3xl mb-2"></i>
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                        <span>Upload gambar</span>
                                        <input id="file-upload" name="thumbnail" type="file" class="sr-only" accept="image/*">
                                    </label>
                                    <p class="pl-1">atau drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="pt-4">
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                            <i class="fas fa-upload mr-2"></i> Kumpulkan Projek
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection