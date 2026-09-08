@extends('layouts.admin')

@section('title', 'Detail Murid - E-Rapor BiMBA')
@section('page-title', 'Detail Murid')

@section('content')
<div class="flex items-center gap-4 mb-8">
  <a href="{{ route('admin.murid') }}" class="p-2 hover:bg-gray-100 rounded-lg">
    <iconify-icon icon="lucide:arrow-left" width="20"></iconify-icon>
  </a>
  <h1 class="text-2xl lg:text-3xl font-bold tracking-tight">{{ $student->name }}</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <!-- Main Info -->
  <div class="lg:col-span-2 space-y-6">
    <!-- Basic Info -->
    <div class="bg-white rounded-2xl border border-[#dee1e6] p-6 main-shadow">
      <h2 class="text-lg font-semibold text-[#171a1f] mb-4">Informasi Dasar</h2>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <p class="text-sm text-[#565d6d] mb-1">NIS</p>
          <p class="text-base font-medium text-[#171a1f]">{{ $student->nis }}</p>
        </div>
        <div>
          <p class="text-sm text-[#565d6d] mb-1">Nama Murid</p>
          <p class="text-base font-medium text-[#171a1f]">{{ $student->name }}</p>
        </div>
        <div>
          <p class="text-sm text-[#565d6d] mb-1">Jenis Kelamin</p>
          <p class="text-base font-medium text-[#171a1f]">{{ $student->gender === 'L' ? 'Laki-laki' : ($student->gender === 'P' ? 'Perempuan' : '-') }}</p>
        </div>
        <div>
          <p class="text-sm text-[#565d6d] mb-1">Tanggal Lahir</p>
          <p class="text-base font-medium text-[#171a1f]">{{ $student->birth_date?->translatedFormat('d F Y') ?? '-' }}</p>
        </div>
        <div>
          <p class="text-sm text-[#565d6d] mb-1">Tahapan</p>
          <p class="text-base font-medium text-[#171a1f]">{{ $student->classroom?->name ?? '-' }}</p>
        </div>
        <div>
          <p class="text-sm text-[#565d6d] mb-1">Status</p>
          <span class="status-pill {{ $student->status === 'aktif' ? 'status-active' : ($student->status === 'lulus' ? 'status-lulus' : ($student->status === 'cuti' ? 'status-cuti' : 'status-keluar')) }}">{{ ucfirst($student->status) }}</span>
        </div>
        <div>
          <p class="text-sm text-[#565d6d] mb-1">Tgl. Bergabung</p>
          <p class="text-base font-medium text-[#171a1f]">{{ $student->join_date->format('d/m/Y') }}</p>
        </div>
        <div>
          <p class="text-sm text-[#565d6d] mb-1">Guru Pembimbing</p>
          <p class="text-base font-medium text-[#171a1f]">{{ $student->teacher?->user->name ?? '-' }}</p>
        </div>
      </div>
    </div>

    <!-- Guardian Info -->
    <div class="bg-white rounded-2xl border border-[#dee1e6] p-6 main-shadow">
      <h2 class="text-lg font-semibold text-[#171a1f] mb-4">Data Wali Murid</h2>
      @if($student->parent)
        <div class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-[#565d6d] mb-1">Nama Ayah</p>
              <p class="text-base font-medium text-[#171a1f]">{{ $student->parent->father_name ?? '-' }}</p>
            </div>
            <div>
              <p class="text-sm text-[#565d6d] mb-1">Nama Ibu</p>
              <p class="text-base font-medium text-[#171a1f]">{{ $student->parent->mother_name ?? '-' }}</p>
            </div>
            <div>
              <p class="text-sm text-[#565d6d] mb-1">Kontak Ayah</p>
              <p class="text-base font-medium text-[#171a1f]">{{ $student->parent->father_phone ?? '-' }}</p>
            </div>
            <div>
              <p class="text-sm text-[#565d6d] mb-1">Kontak Ibu</p>
              <p class="text-base font-medium text-[#171a1f]">{{ $student->parent->mother_phone ?? '-' }}</p>
            </div>
          </div>
          <div>
            <p class="text-sm text-[#565d6d] mb-1">Alamat</p>
            <p class="text-base text-[#171a1f]">{{ $student->parent->address ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm text-[#565d6d] mb-1">Email</p>
            <p class="text-base text-[#171a1f]">{{ $student->parent->email ?? '-' }}</p>
          </div>
          <a href="{{ route('admin.wali.show', $student->parent) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 border border-[#3d8af5] rounded-xl text-[#3d8af5] font-medium text-sm hover:bg-blue-100">
            <iconify-icon icon="lucide:arrow-right" width="16"></iconify-icon>
            Lihat Detail Wali
          </a>
        </div>
      @else
        <p class="text-sm text-[#565d6d]">Tidak ada wali murid yang terhubung.</p>
      @endif
    </div>

    <!-- Attendance History -->
    <div class="bg-white rounded-2xl border border-[#dee1e6] p-6 main-shadow">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <div>
          <h2 class="text-lg font-semibold text-[#171a1f]">Riwayat Kehadiran</h2>
          <p class="text-xs text-[#565d6d]">Ringkasan bulan {{ now()->locale('id')->translatedFormat('F Y') }}</p>
        </div>
        <a href="{{ route('admin.absensi.report', ['month' => now()->format('Y-m'), 'student_id' => $student->id, 'student_status' => 'semua']) }}" class="inline-flex items-center gap-2 px-3 py-2 bg-orange-50 border border-orange-200 text-[#C2410C] rounded-xl text-xs font-semibold hover:bg-orange-100">
          <iconify-icon icon="lucide:bar-chart-3" width="15"></iconify-icon> Laporan Lengkap
        </a>
      </div>
      <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-5">
        <div class="rounded-xl bg-green-50 p-3"><p class="text-xs text-green-700">Hadir</p><p class="text-xl font-bold text-green-700">{{ $attendanceSummary['hadir'] }}</p></div>
        <div class="rounded-xl bg-blue-50 p-3"><p class="text-xs text-blue-700">Sakit</p><p class="text-xl font-bold text-blue-700">{{ $attendanceSummary['sakit'] }}</p></div>
        <div class="rounded-xl bg-amber-50 p-3"><p class="text-xs text-amber-700">Izin</p><p class="text-xl font-bold text-amber-700">{{ $attendanceSummary['izin'] }}</p></div>
        <div class="rounded-xl bg-red-50 p-3"><p class="text-xs text-red-700">Alpa</p><p class="text-xl font-bold text-red-700">{{ $attendanceSummary['alpa'] }}</p></div>
        <div class="rounded-xl bg-purple-50 p-3"><p class="text-xs text-purple-700">Hadir/Tercatat</p><p class="text-xl font-bold text-purple-700">{{ $attendanceSummary['percentage'] === null ? '—' : $attendanceSummary['percentage'].'%' }}</p></div>
      </div>
      <div class="space-y-2">
        @forelse($attendanceHistory as $attendance)
          @php $badge = match($attendance->status) { 'hadir' => 'bg-green-100 text-green-700', 'sakit' => 'bg-blue-100 text-blue-700', 'izin' => 'bg-amber-100 text-amber-700', default => 'bg-red-100 text-red-700' }; @endphp
          <div class="border border-[#edf0f3] rounded-xl px-4 py-3 flex items-center justify-between gap-4">
            <div><p class="text-sm font-semibold">{{ $attendance->attendance_date->locale('id')->translatedFormat('d F Y') }}</p><p class="text-xs text-[#565d6d]">{{ $attendance->notes ?: 'Tanpa catatan' }}</p></div>
            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $badge }}">{{ $attendance->status_code }} · {{ $attendance->status_label }}</span>
          </div>
        @empty
          <p class="text-sm text-[#565d6d] py-4">Belum ada riwayat kehadiran.</p>
        @endforelse
      </div>
    </div>

    <!-- Student Progress -->
    <div class="bg-white rounded-2xl border border-[#dee1e6] p-6 main-shadow">
      <h2 class="text-lg font-semibold text-[#171a1f] mb-4">Perkembangan Murid</h2>
      @if($studentProgress->count() > 0)
        <div class="space-y-3">
          @foreach($studentProgress as $progress)
            <div class="border border-[#dee1e6] rounded-lg p-3">
              <div class="flex justify-between items-start">
                <div>
                  <p class="text-sm font-medium text-[#171a1f]">{{ $progress->description ?: 'Catatan Perkembangan' }}</p>
                  <p class="text-xs text-[#565d6d] mt-1">{{ $progress->created_at->format('d/m/Y H:i') }}</p>
                </div>
              </div>
              @if($progress->notes)
                <p class="text-sm text-[#565d6d] mt-2">{{ $progress->notes }}</p>
              @endif
            </div>
          @endforeach
        </div>
      @else
        <p class="text-sm text-[#565d6d]">Belum ada catatan perkembangan.</p>
      @endif
    </div>
  </div>

  <!-- Sidebar -->
  <div class="space-y-6">
    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl border border-[#dee1e6] p-6 main-shadow">
      <h2 class="text-lg font-semibold text-[#171a1f] mb-4">Aksi</h2>
      <div class="space-y-2">
        <a href="{{ route('admin.murid') }}" class="flex items-center gap-2 w-full px-4 py-2 bg-[#3d8af5] text-white rounded-xl font-medium text-sm hover:bg-blue-600">
          <iconify-icon icon="lucide:arrow-left" width="16"></iconify-icon>
          Kembali ke Daftar
        </a>
        <form method="POST" action="{{ route('admin.murid.destroy', $student) }}" id="form-delete-murid" class="w-full">
          @csrf @method('DELETE')
          <button type="button" onclick="showDeleteConfirm(this, 'murid')" class="flex items-center gap-2 w-full px-4 py-2 bg-red-600 text-white rounded-xl font-medium text-sm hover:bg-red-700">
            <iconify-icon icon="lucide:trash-2" width="16"></iconify-icon>
            Hapus Murid
          </button>
        </form>
      </div>
    </div>

    <!-- Teacher Info -->
    @if($student->teacher)
      <div class="bg-white rounded-2xl border border-[#dee1e6] p-6 main-shadow">
        <h2 class="text-lg font-semibold text-[#171a1f] mb-4">Guru Pembimbing</h2>
        <div class="space-y-3">
          <div>
            <p class="text-sm text-[#565d6d] mb-1">Nama</p>
            <p class="text-base font-medium text-[#171a1f]">{{ $student->teacher->user->name }}</p>
          </div>
          <div>
            <p class="text-sm text-[#565d6d] mb-1">NIP</p>
            <p class="text-base font-medium text-[#171a1f]">{{ $student->teacher->nip ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm text-[#565d6d] mb-1">Status</p>
            <p class="text-base font-medium text-[#171a1f]">{{ ucfirst($student->teacher->status) }}</p>
          </div>
          <a href="{{ route('admin.guru.show', $student->teacher) }}" class="flex items-center gap-2 px-4 py-2 bg-blue-50 border border-[#3d8af5] rounded-xl text-[#3d8af5] font-medium text-sm hover:bg-blue-100">
            <iconify-icon icon="lucide:arrow-right" width="16"></iconify-icon>
            Lihat Detail Guru
          </a>
        </div>
      </div>
    @endif
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
      <button type="button" id="delete-confirm-btn" class="flex-1 py-2.5 bg-red-600 text-white rounded-xl text-sm font-medium hover:bg-red-700">OK, Hapus</button>
    </div>
  </div>
</div>

@endsection
