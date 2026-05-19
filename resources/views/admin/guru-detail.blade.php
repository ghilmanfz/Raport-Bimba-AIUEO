@extends('layouts.admin')

@section('title', 'Detail Guru - E-Rapor BiMBA')
@section('page-title', 'Detail Guru')

@section('content')
<div class="flex items-center gap-4 mb-8">
  <a href="{{ route('admin.guru') }}" class="p-2 hover:bg-gray-100 rounded-lg">
    <iconify-icon icon="lucide:arrow-left" width="20"></iconify-icon>
  </a>
  <h1 class="text-2xl lg:text-3xl font-bold tracking-tight">{{ $teacher->user->name }}</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <!-- Main Info -->
  <div class="lg:col-span-2 space-y-6">
    <!-- Basic Info -->
    <div class="bg-white rounded-2xl border border-[#dee1e6] p-6 main-shadow">
      <h2 class="text-lg font-semibold text-[#171a1f] mb-4">Informasi Dasar</h2>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <p class="text-sm text-[#565d6d] mb-1">NIP</p>
          <p class="text-base font-medium text-[#171a1f]">{{ $teacher->nip ?? '-' }}</p>
        </div>
        <div>
          <p class="text-sm text-[#565d6d] mb-1">Nama</p>
          <p class="text-base font-medium text-[#171a1f]">{{ $teacher->user->name }}</p>
        </div>
        <div>
          <p class="text-sm text-[#565d6d] mb-1">Email</p>
          <p class="text-base font-medium text-[#171a1f]">{{ $teacher->user->email }}</p>
        </div>
        <div>
          <p class="text-sm text-[#565d6d] mb-1">Status</p>
          <span class="status-pill {{ $teacher->status === 'aktif' ? 'status-active' : ($teacher->status === 'cuti' ? 'status-lulus' : 'status-pindah') }}">{{ ucfirst($teacher->status) }}</span>
        </div>
        @if($teacher->specialization)
        <div class="col-span-2">
          <p class="text-sm text-[#565d6d] mb-1">Bidang Keahlian</p>
          <p class="text-base font-medium text-[#171a1f]">{{ $teacher->specialization }}</p>
        </div>
        @endif
      </div>
    </div>

    <!-- Classroom Assignment -->
    <div class="bg-white rounded-2xl border border-[#dee1e6] p-6 main-shadow">
      <h2 class="text-lg font-semibold text-[#171a1f] mb-4">Kelas yang Diampu</h2>
      @if($teacher->classrooms->count() > 0)
        <div class="space-y-2">
          @foreach($teacher->classrooms as $classroom)
            <div class="flex items-center justify-between px-4 py-3 border border-[#dee1e6] rounded-lg">
              <span class="font-medium text-[#171a1f]">{{ $classroom->name }}</span>
              <span class="text-sm text-[#565d6d]">{{ $teacher->students()->where('classroom_id', $classroom->id)->count() }} murid</span>
            </div>
          @endforeach
        </div>
      @else
        <p class="text-sm text-[#565d6d]">Tidak ada kelas yang diampu.</p>
      @endif
    </div>

    <!-- Students -->
    <div class="bg-white rounded-2xl border border-[#dee1e6] p-6 main-shadow">
      <h2 class="text-lg font-semibold text-[#171a1f] mb-4">Murid Binaan ({{ $teacher->students->count() }})</h2>
      @if($teacher->students->count() > 0)
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead>
              <tr class="border-b border-[#dee1e6]">
                <th class="py-2 font-medium text-[#565d6d]">NIS</th>
                <th class="py-2 font-medium text-[#565d6d]">Nama</th>
                <th class="py-2 font-medium text-[#565d6d]">Tahapan</th>
                <th class="py-2 font-medium text-[#565d6d]">Status</th>
                <th class="py-2 text-right font-medium text-[#565d6d]">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#dee1e6]">
              @foreach($teacher->students as $student)
                <tr>
                  <td class="py-3">{{ $student->nis }}</td>
                  <td class="py-3 font-medium text-[#171a1f]">{{ $student->name }}</td>
                  <td class="py-3">{{ $student->classroom?->name ?? '-' }}</td>
                  <td class="py-3">
                    <span class="status-pill {{ $student->status === 'aktif' ? 'status-active' : ($student->status === 'lulus' ? 'status-lulus' : 'status-pindah') }}">{{ ucfirst($student->status) }}</span>
                  </td>
                  <td class="py-3 text-right">
                    <a href="{{ route('admin.murid.show', $student) }}" class="text-[#3d8af5] hover:underline text-xs font-medium">
                      Lihat Detail
                    </a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <p class="text-sm text-[#565d6d]">Tidak ada murid yang dibimbing.</p>
      @endif
    </div>
  </div>

  <!-- Sidebar -->
  <div class="space-y-6">
    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl border border-[#dee1e6] p-6 main-shadow">
      <h2 class="text-lg font-semibold text-[#171a1f] mb-4">Aksi</h2>
      <div class="space-y-2">
        <a href="{{ route('admin.guru') }}" class="flex items-center gap-2 w-full px-4 py-2 bg-[#3d8af5] text-white rounded-xl font-medium text-sm hover:bg-blue-600">
          <iconify-icon icon="lucide:arrow-left" width="16"></iconify-icon>
          Kembali ke Daftar
        </a>
        <form method="POST" action="{{ route('admin.guru.destroy', $teacher) }}" id="form-delete-guru" class="w-full">
          @csrf @method('DELETE')
          <button type="button" onclick="showDeleteConfirm(this, 'guru')" class="flex items-center gap-2 w-full px-4 py-2 bg-red-600 text-white rounded-xl font-medium text-sm hover:bg-red-700">
            <iconify-icon icon="lucide:trash-2" width="16"></iconify-icon>
            Hapus Guru
          </button>
        </form>
      </div>
    </div>

    <!-- Statistics -->
    <div class="bg-white rounded-2xl border border-[#dee1e6] p-6 main-shadow">
      <h2 class="text-lg font-semibold text-[#171a1f] mb-4">Statistik</h2>
      <div class="space-y-3">
        <div>
          <p class="text-sm text-[#565d6d] mb-1">Total Murid</p>
          <p class="text-2xl font-bold text-[#171a1f]">{{ $teacher->students->count() }}</p>
        </div>
        <div>
          <p class="text-sm text-[#565d6d] mb-1">Murid Aktif</p>
          <p class="text-xl font-bold text-green-600">{{ $teacher->students->where('status', 'aktif')->count() }}</p>
        </div>
        <div>
          <p class="text-sm text-[#565d6d] mb-1">Murid Lulus</p>
          <p class="text-xl font-bold text-blue-600">{{ $teacher->students->where('status', 'lulus')->count() }}</p>
        </div>
        <div>
          <p class="text-sm text-[#565d6d] mb-1">Murid Pindah</p>
          <p class="text-xl font-bold text-orange-600">{{ $teacher->students->where('status', 'pindah')->count() }}</p>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function showDeleteConfirm(button, type) {
  const modal = document.getElementById('modal-delete-confirm');
  const typeLabels = {
    'murid': 'Murid',
    'guru': 'Guru', 
    'wali': 'Wali Murid'
  };
  document.getElementById('delete-confirm-type').textContent = typeLabels[type];
  document.getElementById('delete-confirm-btn').onclick = function() {
    button.parentElement.submit();
  };
  modal.classList.remove('hidden');
  modal.classList.add('flex');
}

function closeDeleteConfirm() {
  const modal = document.getElementById('modal-delete-confirm');
  modal.classList.add('hidden');
  modal.classList.remove('flex');
}
</script>

<!-- Delete Confirmation Modal -->
<div id="modal-delete-confirm" class="hidden fixed inset-0 z-50 bg-black/50 items-center justify-center">
  <div class="bg-white rounded-2xl p-8 w-full max-w-md mx-4 shadow-xl">
    <div class="flex items-center gap-3 mb-6">
      <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
        <iconify-icon icon="lucide:alert-triangle" width="24" class="text-red-600"></iconify-icon>
      </div>
      <div>
        <h3 class="text-lg font-bold text-[#171a1f]">Konfirmasi Hapus</h3>
        <p class="text-sm text-[#565d6d]">Tindakan ini tidak dapat dibatalkan</p>
      </div>
    </div>
    
    <p class="text-sm text-[#565d6d] mb-6">Anda yakin ingin menghapus <span id="delete-confirm-type" class="font-semibold text-[#171a1f]"></span> ini? Data yang dihapus tidak dapat dipulihkan.</p>
    
    <div class="flex gap-3">
      <button type="button" onclick="closeDeleteConfirm()" class="flex-1 py-2.5 border border-[#dee1e6] rounded-xl text-sm font-medium text-[#565d6d] hover:bg-gray-50">Batal</button>
      <button type="button" id="delete-confirm-btn" class="flex-1 py-2.5 bg-red-600 text-white rounded-xl text-sm font-medium hover:bg-red-700">Hapus</button>
    </div>
  </div>
</div>

@endsection
