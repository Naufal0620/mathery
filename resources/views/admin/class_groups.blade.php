@extends('layouts.admin')

@section('title', 'Kelola Kelompok - ' . $course->name)
@section('header_title', 'Manajemen Kelompok')

@section('content')
<div class="fade-in space-y-6">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('admin.classes') }}" class="hover:text-indigo-600 transition-colors">Manajemen Kelas</a>
                <i class='bx bx-chevron-right'></i>
                <span>Kelompok</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar Kelompok</h2>
            <p class="text-sm text-gray-500">Kelola pembagian kelompok untuk {{ $course->name }}</p>
        </div>
        
        <div class="flex flex-wrap gap-3">
            @if($leavingStudents->isNotEmpty())
            <button onclick="showLeaveRequests()" class="px-4 py-2 bg-red-100 text-red-600 text-sm font-bold rounded-xl border border-red-200 hover:bg-red-200 transition-all flex items-center gap-2 animate-pulse-slow">
                <i class='bx bx-log-out-circle'></i>
                <span>{{ $leavingStudents->count() }} Permintaan Keluar</span>
            </button>
            @endif

            <a href="{{ route('admin.classes.members', $course->id) }}" class="px-4 py-2 bg-white text-indigo-600 text-sm font-medium rounded-xl border border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700 transition-all flex items-center gap-2">
                <i class='bx bx-user text-lg'></i> Kelola Anggota
            </a>
            
            <button onclick="openModal('createGroupModal')" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:-translate-y-0.5 transition-all flex items-center gap-2">
                <i class='bx bx-plus-circle text-xl'></i> Buat Kelompok
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($groups as $group)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm flex flex-col h-full group hover:shadow-md transition-shadow overflow-hidden">
            
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-start">
                <div>
                    <h3 class="font-bold text-gray-800 text-lg group-name">{{ $group->name }}</h3>
                    <div class="flex items-center gap-1 mt-1 text-xs text-indigo-600 font-medium bg-indigo-50 px-2 py-0.5 rounded w-fit">
                        <i class='bx bx-book-bookmark'></i>
                        {{ $group->topic->name ?? 'Topik Dihapus' }}
                    </div>
                </div>
                
                <div class="flex items-center gap-1">
                    <button onclick="editGroup({{ $group->id }}, '{{ $group->name }}', {{ $group->max_slots }})" class="text-gray-400 hover:text-orange-500 p-1.5 rounded-lg hover:bg-orange-50 transition-colors" title="Edit Info">
                        <i class='bx bx-edit text-xl'></i>
                    </button>
                    {{-- REVISI: SweetAlert2 untuk Hapus Kelompok --}}
                    <form action="{{ route('admin.groups.destroy', $group->id) }}" method="POST" onsubmit="return deleteGroupConfirm(event)">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-gray-400 hover:text-red-500 p-1.5 rounded-lg hover:bg-red-50 transition-colors" title="Hapus Kelompok">
                            <i class='bx bx-trash text-xl'></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="p-4 flex-1 flex flex-col">
                <div class="flex justify-between items-center mb-3 px-1">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        Anggota <span class="{{ $group->students->count() >= $group->max_slots ? 'text-red-500' : 'text-green-500' }}">({{ $group->students->count() }}/{{ $group->max_slots }})</span>
                    </p>
                    @if($group->students->count() < $group->max_slots)
                    <button onclick="addMember({{ $group->id }}, '{{ $group->name }}')" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold flex items-center gap-1 px-2 py-1 rounded hover:bg-indigo-50 transition-colors">
                        <i class='bx bx-plus'></i> Tambah
                    </button>
                    @endif
                </div>
                
                <div class="space-y-1 overflow-y-auto max-h-[200px] overflow-x-hidden custom-scrollbar pr-1">
                    @forelse($group->students as $member)
                    <div class="flex justify-between items-center group/member p-2 rounded-lg transition-colors even:bg-gray-50 hover:!bg-indigo-50">
                        <div class="flex items-center gap-3 flex-1 min-w-0"> 
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($member->full_name) }}&background=random" class="w-7 h-7 rounded-full bg-gray-200 shrink-0">
                            <span class="text-sm text-gray-700 font-medium truncate" title="{{ $member->full_name }}">
                                {{ $member->full_name }}
                            </span>
                        </div>
                        {{-- REVISI: SweetAlert2 untuk Keluarkan Anggota --}}
                        <form action="{{ route('admin.groups.members.destroy', [$group->id, $member->id]) }}" method="POST" onsubmit="return removeMemberConfirm(event, '{{ $member->full_name }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-300 hover:text-red-500 opacity-0 group-hover/member:opacity-100 transition-all p-1 shrink-0 ml-2" title="Keluarkan">
                                <i class='bx bx-x-circle text-lg'></i>
                            </button>
                        </form>
                    </div>
                    @empty
                    <div class="text-center py-6 border-2 border-dashed border-gray-100 rounded-xl bg-gray-50/30 mx-1">
                        <span class="text-xs text-gray-400">Belum ada anggota</span>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center text-gray-400 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
            <i class='bx bx-group bx-lg mb-2 text-gray-300'></i>
            <p>Belum ada kelompok yang dibuat.</p>
        </div>
        @endforelse
    </div>

    <div id="createGroupModal" class="fixed inset-0 z-50 hidden">
        {{-- ... Isi Modal Create Group ... --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('createGroupModal')"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-2xl shadow-2xl p-6 fade-in">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Buat Kelompok Baru</h3>
            <form action="{{ route('admin.groups.store') }}" method="POST">
                @csrf
                <input type="hidden" name="class_id" value="{{ $course->id }}">
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Topik</label>
                        <select name="topic_id" required class="w-full mt-1 p-2 rounded-xl border border-gray-300 text-sm">
                            @foreach($topics as $topic)
                                <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Nama Kelompok</label>
                        <input type="text" name="name" required class="w-full mt-1 p-2 rounded-xl border border-gray-300 text-sm" placeholder="Kelompok 1">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Kapasitas</label>
                        <input type="number" name="max_slots" value="5" min="1" required class="w-full mt-1 p-2 rounded-xl border border-gray-300 text-sm">
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeModal('createGroupModal')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200">Batal</button>
                    <button type="submit" class="px-4 py-2 text-white bg-indigo-600 rounded-xl hover:bg-indigo-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    
    <div id="editGroupModal" class="fixed inset-0 z-50 hidden">
        {{-- ... Isi Modal Edit Group ... --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('editGroupModal')"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-2xl shadow-2xl p-6 fade-in">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Edit Kelompok</h3>
            <form id="editGroupForm" method="POST">
                @csrf @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Nama Kelompok</label>
                        <input type="text" name="name" id="edit_name" required class="w-full mt-1 p-2 rounded-xl border border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Kapasitas</label>
                        <input type="number" name="max_slots" id="edit_slots" min="1" required class="w-full mt-1 p-2 rounded-xl border border-gray-300 text-sm">
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeModal('editGroupModal')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200">Batal</button>
                    <button type="submit" class="px-4 py-2 text-white bg-orange-500 rounded-xl hover:bg-orange-600">Update</button>
                </div>
            </form>
        </div>
    </div>

    <div id="addMemberModal" class="fixed inset-0 z-50 hidden">
        {{-- ... Isi Modal Add Member ... --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('addMemberModal')"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-sm bg-white rounded-2xl shadow-2xl p-6 fade-in">
            <h3 class="text-lg font-bold text-gray-800 mb-1">Tambah Anggota</h3>
            <p class="text-xs text-gray-500 mb-4">Menambahkan ke: <span id="addMemberGroupName" class="font-bold text-indigo-600"></span></p>
            <form id="addMemberForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pilih Mahasiswa</label>
                    <select name="student_id" required class="w-full mt-1 p-2.5 rounded-xl border border-gray-300 text-sm bg-white">
                        <option value="" disabled selected>-- Pilih Mahasiswa Tanpa Kelompok --</option>
                        @foreach($ungroupedStudents as $student)
                            <option value="{{ $student->id }}">{{ $student->full_name }}</option>
                        @endforeach
                    </select>
                    @if($ungroupedStudents->isEmpty())
                        <p class="text-xs text-red-500 mt-2">Tidak ada mahasiswa yang tersedia.</p>
                    @endif
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeModal('addMemberModal')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 text-sm">Tambahkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
    function editGroup(id, name, slots) {
        openModal('editGroupModal');
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_slots').value = slots;
        let url = "{{ route('admin.groups.update', ':id') }}"; 
        document.getElementById('editGroupForm').action = url.replace(':id', id);
    }
    function addMember(groupId, groupName) {
        openModal('addMemberModal');
        document.getElementById('addMemberGroupName').innerText = groupName;
        let url = "{{ route('admin.groups.members.store', ':id') }}";
        document.getElementById('addMemberForm').action = url.replace(':id', groupId);
    }

    // REVISI: Fungsi Konfirmasi Hapus Kelompok
    function deleteGroupConfirm(event) {
        event.preventDefault();
        var form = event.target;
        Swal.fire({
            title: 'Hapus Kelompok?',
            text: "Semua anggota akan dikeluarkan otomatis.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    }

    // REVISI: Fungsi Konfirmasi Keluarkan Member
    function removeMemberConfirm(event, name) {
        event.preventDefault();
        var form = event.target;
        Swal.fire({
            title: 'Keluarkan Anggota?',
            text: name + " akan dihapus dari kelompok ini.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Keluarkan!',
            cancelButtonText: 'Batal',
            width: '400px'
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    }

    // SweetAlert2 for Leave Requests (Tetap sama)
    function showLeaveRequests() {
        let htmlContent = `
            <div class="flex flex-col gap-3 text-left">
                @foreach($leavingStudents as $student)
                <div class="flex flex-col sm:flex-row justify-between items-center p-3 bg-red-50 rounded-xl border border-red-100 gap-3">
                    <div class="text-sm">
                        <p class="font-bold text-gray-800">{{ $student->full_name }}</p>
                        <p class="text-xs text-red-500">
                            Ingin keluar dari: <span class="font-bold">{{ $student->groups->where('class_id', $course->id)->first()->name ?? '-' }}</span>
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <form action="{{ route('admin.groups.leave.approve', [$course->id, $student->id]) }}" method="POST">
                            @csrf @method('PUT')
                            <button type="submit" class="px-3 py-1.5 bg-red-600 text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-colors">Izinkan</button>
                        </form>
                        <form action="{{ route('admin.groups.leave.reject', [$course->id, $student->id]) }}" method="POST">
                            @csrf @method('PUT')
                            <button type="submit" class="px-3 py-1.5 bg-gray-200 text-gray-600 text-xs font-bold rounded-lg hover:bg-gray-300 transition-colors">Tolak</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        `;
        Swal.fire({
            title: 'Permintaan Keluar Kelompok',
            html: htmlContent,
            showConfirmButton: false,
            showCloseButton: true,
            width: '550px',
            customClass: { popup: 'rounded-2xl' }
        });
    }
</script>
@endpush
@endsection