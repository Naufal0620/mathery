@extends('layouts.student')

@section('title', $course->name . ' - Detail Kelas')
@section('header_title', 'Materi Kelas')

@section('content')
<div class="max-w-7xl mx-auto pb-20 md:pb-10 px-4 sm:px-6">
    
    {{-- 1. Header Area --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-200 pb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('student.dashboard') }}" class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-indigo-50 hover:text-indigo-600 transition-all shadow-sm">
                <i class='bx bx-left-arrow-alt text-2xl'></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 leading-tight">{{ $course->name }}</h1>
                <p class="text-sm text-gray-500 flex items-center gap-2 mt-1">
                    <span class="bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded text-xs font-bold font-mono">{{ $course->code }}</span>
                    <span>{{ $course->teacher->full_name ?? 'Dosen Pengampu' }}</span>
                </p>
            </div>
        </div>
    </div>

    @if($topics->isEmpty())
        <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-gray-300">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class='bx bx-folder-open text-3xl text-gray-400'></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Belum Ada Materi</h3>
            <p class="text-gray-500 text-sm">Dosen belum mengupload materi pertemuan untuk kelas ini.</p>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- 2. NAVIGATION (Sidebar on Desktop, Tabs on Mobile) --}}
            <div class="lg:col-span-3 lg:sticky lg:top-24 z-30">
                
                {{-- Label Navigasi --}}
                <div class="hidden lg:block mb-3 px-2">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Daftar Pertemuan</h3>
                </div>

                {{-- Container Navigasi --}}
                <div class="flex lg:flex-col overflow-x-auto lg:overflow-visible gap-2 pb-4 lg:pb-0 hide-scrollbar snap-x">
                    @foreach($topics as $index => $topic)
                        <button 
                            onclick="switchTab('{{ $topic->id }}')"
                            id="nav-btn-{{ $topic->id }}"
                            class="nav-item snap-start flex-shrink-0 w-auto lg:w-full text-left px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 border lg:border-0
                            {{ $loop->first ? 'active bg-indigo-600 text-white shadow-md lg:shadow-none' : 'bg-white lg:bg-transparent text-gray-600 border-gray-200 hover:bg-gray-50 lg:hover:bg-gray-100' }}">
                            <span class="block text-[10px] opacity-70 mb-0.5 {{ $loop->first ? 'text-indigo-200' : 'text-gray-400' }} label-date">
                                Pertemuan {{ $loop->iteration }}
                            </span>
                            <span class="block truncate font-bold">{{ Str::limit($topic->name, 25) }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- 3. MAIN CONTENT AREA --}}
            <div class="lg:col-span-9 min-h-[500px]">
                @foreach($topics as $index => $topic)
                    
                    {{-- Topic Content Container --}}
                    <div id="content-{{ $topic->id }}" class="topic-content {{ !$loop->first ? 'hidden' : '' }} space-y-8 animate-fade-in">
                        
                        {{-- A. Header Pertemuan --}}
                        <div class="bg-white rounded-3xl p-6 md:p-8 border border-gray-200 shadow-sm">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-100 pb-6 mb-6">
                                <div>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-700 text-xs font-bold uppercase tracking-wide mb-3">
                                        <i class='bx bx-bookmark'></i> Topik Bahasan
                                    </span>
                                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 leading-tight">{{ $topic->name }}</h2>
                                </div>
                                <div class="text-right md:text-left shrink-0">
                                    <div class="inline-flex flex-col items-end md:items-start bg-gray-50 px-4 py-2 rounded-xl border border-gray-100">
                                        <span class="text-[10px] text-gray-400 font-bold uppercase">Tanggal</span>
                                        <span class="text-sm font-bold text-gray-700 flex items-center gap-1">
                                            <i class='bx bx-calendar'></i> {{ \Carbon\Carbon::parse($topic->meeting_date)->translatedFormat('d M Y') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Render Konten Editor.js --}}
                            <div class="prose prose-indigo max-w-none text-gray-600 leading-relaxed space-y-6" id="render-{{ $topic->id }}">
                                {{-- Content will be injected via JS --}}
                            </div>
                            
                            {{-- Raw Data for JS --}}
                            <textarea class="hidden json-source" data-target="render-{{ $topic->id }}">{{ $topic->content }}</textarea>

                            {{-- Fallback Empty State --}}
                            @if(!$topic->content)
                                <div class="py-10 text-center bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                                    <p class="text-gray-400 text-sm italic">Materi belum ditambahkan oleh dosen.</p>
                                </div>
                            @endif
                        </div>

                        {{-- B. Diskusi Section --}}
                        <div class="bg-gray-50 rounded-3xl border border-gray-200 p-6 md:p-8">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-indigo-600 shadow-sm">
                                    <i class='bx bx-message-rounded-dots text-xl'></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">Diskusi Kelas</h3>
                                    <p class="text-xs text-gray-500">Tanyakan sesuatu atau berdiskusi tentang materi ini.</p>
                                </div>
                            </div>

                            {{-- Form Komentar --}}
                            <form action="{{ route('student.topic.comment', $topic->id) }}" method="POST" enctype="multipart/form-data" class="mb-8 group">
                                @csrf
                                <div class="bg-white p-1.5 rounded-2xl border border-gray-300 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500 transition-all">
                                    <textarea name="body" rows="2" placeholder="Tulis komentar..." class="w-full text-sm border-none focus:ring-0 resize-none bg-transparent placeholder-gray-400 px-3 py-2 rounded-xl"></textarea>
                                    
                                    <div class="flex items-center justify-between px-2 pb-1 mt-1">
                                        <label class="cursor-pointer p-2 rounded-full hover:bg-gray-100 text-gray-400 hover:text-indigo-600 transition-colors" title="Upload Gambar">
                                            <i class='bx bx-image-add text-xl'></i>
                                            <input type="file" name="image" class="hidden input-image" accept="image/*" onchange="previewImage(this)">
                                        </label>
                                        <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded-xl text-xs font-bold hover:bg-black transition-all shadow-lg hover:shadow-none">
                                            Kirim
                                        </button>
                                    </div>
                                    
                                    {{-- Preview Image --}}
                                    <div class="image-preview hidden px-3 pb-3 relative w-fit">
                                        <img src="" class="h-20 w-auto rounded-lg border border-gray-200 object-cover">
                                        <button type="button" onclick="removePreview(this)" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center hover:bg-red-600 shadow-sm"><i class='bx bx-x'></i></button>
                                    </div>
                                </div>
                            </form>

                            {{-- List Komentar --}}
                            <div class="space-y-6">
                                @forelse($topic->comments as $comment)
                                    <div class="flex gap-3 md:gap-4 relative group/comment">
                                        @if($comment->replies->isNotEmpty())
                                            <div class="absolute left-[19px] top-10 bottom-0 w-0.5 bg-gray-200 group-hover/comment:bg-indigo-200 transition-colors"></div>
                                        @endif

                                        <div class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-indigo-700 font-bold text-sm shadow-sm shrink-0 z-10">
                                            {{ substr($comment->user->full_name, 0, 1) }}
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div class="bg-white p-4 rounded-2xl rounded-tl-none border border-gray-200 shadow-sm">
                                                <div class="flex justify-between items-start mb-1">
                                                    <span class="text-sm font-bold text-gray-900">{{ $comment->user->full_name }}</span>
                                                    <span class="text-[10px] text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                                </div>
                                                @if($comment->body)
                                                    <p class="text-sm text-gray-700 leading-relaxed">{{ $comment->body }}</p>
                                                @endif
                                                @if($comment->image_path)
                                                    <img src="{{ asset('storage/' . $comment->image_path) }}" class="mt-3 rounded-lg max-h-48 w-auto object-cover cursor-zoom-in border border-gray-100" onclick="viewImage(this.src)">
                                                @endif
                                            </div>

                                            <div class="flex items-center gap-4 mt-2 ml-2">
                                                <button onclick="toggleLike({{ $comment->id }})" class="flex items-center gap-1 text-xs font-semibold transition-colors {{ $comment->isLikedBy(Auth::id()) ? 'text-pink-500' : 'text-gray-400 hover:text-pink-500' }}" id="like-btn-{{ $comment->id }}">
                                                    <i class='bx {{ $comment->isLikedBy(Auth::id()) ? 'bxs-heart' : 'bx-heart' }} text-base'></i> 
                                                    <span id="like-count-{{ $comment->id }}">{{ $comment->likes->count() }}</span>
                                                </button>
                                                <button onclick="toggleReply('reply-form-{{ $comment->id }}')" class="flex items-center gap-1 text-xs font-semibold text-gray-400 hover:text-indigo-600 transition-colors">
                                                    Balas
                                                </button>
                                            </div>

                                            {{-- Reply Form --}}
                                            <form id="reply-form-{{ $comment->id }}" action="{{ route('student.topic.comment', $topic->id) }}" method="POST" enctype="multipart/form-data" class="hidden mt-3 animate-fade-in">
                                                @csrf
                                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                <div class="flex gap-2 items-start">
                                                    <div class="flex-1 bg-white rounded-xl border border-gray-300 p-1.5 focus-within:border-indigo-500 transition-colors">
                                                        <textarea name="body" rows="1" placeholder="Balas..." class="w-full text-xs border-none focus:ring-0 resize-none bg-transparent px-2"></textarea>
                                                        <div class="flex justify-between items-center mt-1 px-1">
                                                            <input type="file" name="image" class="text-[10px] text-gray-400 file:mr-2 file:py-0.5 file:px-2 file:rounded-full file:border-0 file:text-[10px] file:bg-gray-100 file:text-gray-600">
                                                            <button type="submit" class="text-[10px] font-bold bg-gray-900 text-white px-3 py-1 rounded-lg hover:bg-black">Kirim</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>

                                            {{-- Replies --}}
                                            @if($comment->replies->isNotEmpty())
                                                <div class="mt-4 space-y-4">
                                                    @foreach($comment->replies as $reply)
                                                        <div class="flex gap-3 relative">
                                                            <div class="absolute -left-5 top-3 w-4 h-px bg-gray-300"></div>
                                                            <div class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center text-[10px] font-bold text-gray-500 shrink-0 border border-gray-200">
                                                                {{ substr($reply->user->full_name, 0, 1) }}
                                                            </div>
                                                            <div class="flex-1">
                                                                <div class="bg-white p-3 rounded-xl rounded-tl-none border border-gray-200">
                                                                    <div class="flex justify-between items-center mb-1">
                                                                        <span class="text-xs font-bold text-gray-800">{{ $reply->user->full_name }}</span>
                                                                        <span class="text-[10px] text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                                                    </div>
                                                                    <p class="text-xs text-gray-600">{{ $reply->body }}</p>
                                                                    @if($reply->image_path)
                                                                        <img src="{{ asset('storage/' . $reply->image_path) }}" class="mt-2 rounded-lg max-h-24 border border-gray-200 cursor-zoom-in" onclick="viewImage(this.src)">
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-6">
                                        <p class="text-sm text-gray-400">Belum ada diskusi.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

{{-- Modal Zoom Image --}}
<div id="imageModal" class="fixed inset-0 z-[60] hidden bg-black/90 backdrop-blur-md flex items-center justify-center p-4 transition-opacity duration-300 opacity-0" onclick="closeImageModal()">
    <img id="zoomedImage" src="" class="max-w-full max-h-[90vh] rounded-lg shadow-2xl transform scale-95 transition-transform duration-300">
</div>

@push('scripts')
<script>
    // --- 1. Tab Switching Logic (Fix) ---
    function switchTab(topicId) {
        // A. Handle Content Visibility
        // Hide all contents first
        document.querySelectorAll('.topic-content').forEach(el => {
            el.classList.add('hidden');
        });
        // Show specific content
        const targetContent = document.getElementById('content-' + topicId);
        if(targetContent) {
            targetContent.classList.remove('hidden');
            // Scroll to top of content on mobile for better UX
            if(window.innerWidth < 1024) {
                targetContent.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        // B. Handle Button Styling
        // Reset all buttons
        document.querySelectorAll('.nav-item').forEach(btn => {
            // Remove Active Classes
            btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-md'); 
            // Add Inactive Classes
            btn.classList.add('bg-white', 'text-gray-600', 'border-gray-200'); 
            
            // Handle Desktop specific styles reset
            if(window.innerWidth >= 1024) {
                btn.classList.remove('lg:bg-indigo-50', 'lg:text-indigo-700', 'lg:border-r-4', 'lg:border-indigo-600');
                btn.classList.add('lg:bg-transparent');
            }

            // Reset labels color
            const label = btn.querySelector('.label-date');
            if(label) {
                label.classList.remove('text-indigo-200');
                label.classList.add('text-gray-400');
            }
        });

        // Activate specific button
        const activeBtn = document.getElementById('nav-btn-' + topicId);
        if(activeBtn) {
            activeBtn.classList.remove('bg-white', 'text-gray-600', 'border-gray-200');
            activeBtn.classList.add('bg-indigo-600', 'text-white', 'shadow-md');
            
            const activeLabel = activeBtn.querySelector('.label-date');
            if(activeLabel) {
                activeLabel.classList.remove('text-gray-400');
                activeLabel.classList.add('text-indigo-200');
            }
        }
    }

    // --- 2. Editor.js Content Parser ---
    document.addEventListener("DOMContentLoaded", function() {
        const sources = document.querySelectorAll('.json-source');
        if(sources.length > 0) {
            sources.forEach(textarea => {
                const targetId = textarea.getAttribute('data-target');
                const targetDiv = document.getElementById(targetId);
                try {
                    if(textarea.value.trim() !== "") {
                        const data = JSON.parse(textarea.value);
                        targetDiv.innerHTML = renderEditorJs(data);
                    }
                } catch(e) { console.error("JSON Error", e); }
            });
        }
    });

    function renderEditorJs(data) {
        if (!data.blocks) return '';
        let html = '';
        data.blocks.forEach(block => {
            switch (block.type) {
                case 'header':
                    const level = block.data.level || 2;
                    const sizes = { 1: 'text-3xl', 2: 'text-xl', 3: 'text-lg', 4: 'text-base' };
                    html += `<h${level} class="font-bold text-gray-900 mt-6 mb-3 ${sizes[level]}">${block.data.text}</h${level}>`;
                    break;
                case 'paragraph':
                    html += `<p class="mb-4 text-gray-600 leading-7 text-sm md:text-base">${block.data.text}</p>`;
                    break;
                case 'list':
                    const tag = block.data.style === 'ordered' ? 'ol' : 'ul';
                    const style = block.data.style === 'ordered' ? 'list-decimal' : 'list-disc';
                    html += `<${tag} class="${style} pl-5 mb-4 space-y-1 text-gray-600 text-sm md:text-base">`;
                    block.data.items.forEach(item => html += `<li class="pl-1">${item}</li>`);
                    html += `</${tag}>`;
                    break;
                case 'image':
                    const url = block.data.file ? block.data.file.url : block.data.url;
                    const caption = block.data.caption ? `<p class="text-center text-xs text-gray-500 mt-2 italic">${block.data.caption}</p>` : '';
                    html += `<div class="my-6"><img src="${url}" class="w-full rounded-2xl border border-gray-100 shadow-sm" alt="Image">${caption}</div>`;
                    break;
                case 'embed':
                    if(block.data.service === 'youtube') {
                        html += `<div class="relative w-full aspect-video rounded-2xl overflow-hidden shadow-sm my-6 bg-black">
                                    <iframe src="${block.data.embed}" class="absolute top-0 left-0 w-full h-full" frameborder="0" allowfullscreen></iframe>
                                 </div>`;
                    } else {
                        html += `<div class="p-4 bg-gray-50 border border-gray-200 rounded-xl my-4 flex items-center gap-3">
                                    <div class="bg-indigo-100 p-2 rounded-lg text-indigo-600"><i class='bx bx-link'></i></div>
                                    <a href="${block.data.source}" target="_blank" class="text-indigo-600 text-sm font-bold hover:underline truncate">Link Referensi: ${block.data.source}</a>
                                 </div>`;
                    }
                    break;
            }
        });
        return html;
    }

    // --- 3. UI Helpers ---
    function toggleReply(id) {
        const form = document.getElementById(id);
        form.classList.toggle('hidden');
        if(!form.classList.contains('hidden')) form.querySelector('textarea').focus();
    }

    function previewImage(input) {
        const container = input.closest('form').querySelector('.image-preview');
        const img = container.querySelector('img');
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = (e) => { img.src = e.target.result; container.classList.remove('hidden'); }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removePreview(btn) {
        const container = btn.closest('.image-preview');
        const input = btn.closest('form').querySelector('.input-image');
        container.classList.add('hidden');
        input.value = '';
    }

    function toggleLike(commentId) {
        const btn = document.getElementById(`like-btn-${commentId}`);
        const count = document.getElementById(`like-count-${commentId}`);
        const icon = btn.querySelector('i');
        const isLiked = icon.classList.contains('bxs-heart');
        
        // Optimistic Update
        let val = parseInt(count.innerText);
        if(isLiked) {
            icon.classList.replace('bxs-heart', 'bx-heart');
            btn.classList.replace('text-pink-500', 'text-gray-400');
            count.innerText = Math.max(0, val - 1);
        } else {
            icon.classList.replace('bx-heart', 'bxs-heart');
            btn.classList.replace('text-gray-400', 'text-pink-500');
            icon.classList.add('scale-125');
            setTimeout(()=>icon.classList.remove('scale-125'), 200);
            count.innerText = val + 1;
        }

        fetch(`{{ url('/student/comment') }}/${commentId}/like`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json'}
        }).then(r => r.json()).then(d => { count.innerText = d.count; });
    }

    // Image Zoom Logic
    function viewImage(src) {
        const modal = document.getElementById('imageModal');
        const img = document.getElementById('zoomedImage');
        img.src = src;
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.remove('opacity-0'); img.classList.remove('scale-95'); img.classList.add('scale-100'); }, 10);
    }
    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        const img = document.getElementById('zoomedImage');
        modal.classList.add('opacity-0'); img.classList.remove('scale-100'); img.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }
</script>

<style>
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .animate-fade-in { animation: fadeIn 0.4s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endpush
@endsection