@extends('layouts.admin')

@section('title', 'Manajemen Data Guru - E-Rapor BiMBA')
@section('page-title', 'Manajemen Data Guru')

@section('content')
<!-- Page Title & Actions -->
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-8">
  <div>
    <h1 class="text-2xl lg:text-3xl font-bold tracking-tight">Manajemen Data Guru/Motivator</h1>
    <p class="text-[#565d6d] mt-1 font-roboto">Kelola informasi motivator dan data akun pengguna.</p>
  </div>
  <div class="flex flex-wrap items-center gap-3">
    <a href="{{ route('admin.guru.export') }}" class="flex items-center gap-2 px-4 py-2 bg-white border border-[#dee1e6] rounded-xl text-sm font-medium text-[#171a1f] hover:bg-gray-50">
      <iconify-icon icon="lucide:download" width="16"></iconify-icon>
      Ekspor
    </a>
    <button onclick="document.getElementById('modal-tambah-guru').classList.remove('hidden')" class="flex items-center gap-2 px-4 py-2 bg-[#F97316] text-white rounded-xl text-sm font-medium hover:bg-orange-600">
      <iconify-icon icon="lucide:plus" width="16"></iconify-icon>
      Tambah Guru
    </button>
  </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
  <div class="bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow flex items-center justify-between">
    <div>
      <p class="text-sm font-medium text-[#565d6d]">Total Guru</p>
      <h3 class="text-3xl font-bold mt-1 text-[#171a1f]">{{ $totalGuru }}</h3>
    </div>
    <div class="w-14 h-14 bg-[#FFF7ED] rounded-2xl flex items-center justify-center">
      <iconify-icon icon="lucide:users" width="24" class="text-[#F97316]"></iconify-icon>
    </div>
  </div>
  <div class="bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow flex items-center justify-between">
    <div>
      <p class="text-sm font-medium text-[#565d6d]">Guru Aktif</p>
      <h3 class="text-3xl font-bold mt-1 text-[#171a1f]">{{ $guruAktif }}</h3>
    </div>
    <div class="w-14 h-14 bg-[#FFEDD5] rounded-2xl flex items-center justify-center">
      <iconify-icon icon="lucide:user-check" width="24" class="text-[#C2410C]"></iconify-icon>
    </div>
  </div>
  <div class="bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow flex items-center justify-between">
    <div>
      <p class="text-sm font-medium text-[#565d6d]">Guru Cuti</p>
      <h3 class="text-3xl font-bold mt-1 text-[#171a1f]">{{ $guruCuti }}</h3>
    </div>
    <div class="w-14 h-14 bg-[#FEF3C7] rounded-2xl flex items-center justify-center">
      <iconify-icon icon="lucide:pause-circle" width="24" class="text-[#92400E]"></iconify-icon>
    </div>
  </div>
  <div class="bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow flex items-center justify-between">
    <div>
      <p class="text-sm font-medium text-[#565d6d]">Guru Nonaktif</p>
      <h3 class="text-3xl font-bold mt-1 text-[#171a1f]">{{ $guruNonaktif }}</h3>
    </div>
    <div class="w-14 h-14 bg-[#FEE2E2] rounded-2xl flex items-center justify-center">
      <iconify-icon icon="lucide:user-x" width="24" class="text-[#DC2626]"></iconify-icon>
    </div>
  </div>
</div>

<!-- Table Section -->
<div class="bg-white rounded-2xl border border-[#dee1e6] overflow-hidden main-shadow mb-8">
  <div class="p-4 border-b border-[#dee1e6] flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex flex-col sm:flex-row items-center gap-3">
      <form method="GET" action="{{ route('admin.guru') }}" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
        <div class="relative w-full sm:w-80">
          <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-[#565d6d]">
            <iconify-icon icon="lucide:search" width="16"></iconify-icon>
          </div>
          <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan nama atau NIP..." class="w-full pl-10 pr-4 py-2 bg-[#fafafb] border border-transparent rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20">
        </div>
        <select name="status" class="px-4 py-2 bg-[#fafafb] border border-transparent rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#F97316]/20">
          <option value="">Semua Status</option>
          <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
          <option value="cuti" {{ request('status') === 'cuti' ? 'selected' : '' }}>Cuti</option>
          <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
        </select>
      </form>
    </div>
    <p class="text-sm text-[#565d6d] font-roboto">Menampilkan <span class="font-bold">{{ $teachers->count() }}</span> dari <span class="font-bold">{{ $totalGuru }}</span> guru</p>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead class="bg-[#fafafb] border-b border-[#dee1e6]">
        <tr class="text-[#565d6d] text-sm font-semibold">
          <th class="px-6 py-4">NIP</th>
          <th class="px-6 py-4">Nama Guru</th>
          <th class="px-6 py-4">Email</th>
          <th class="px-6 py-4">Murid Dibimbing</th>
          <th class="px-6 py-4">Status</th>
          <th class="px-6 py-4 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#dee1e6]">
        @forelse($teachers as $teacher)
        <tr class="hover:bg-gray-50/50 {{ $loop->even ? 'bg-[#fafafb]/30' : '' }}">
          <td class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">{{ $teacher->nip ?? '-' }}</td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-[#F97316] flex items-center justify-center text-white font-bold text-sm">{{ strtoupper(substr($teacher->user->name, 0, 1)) }}</div>
              <span class="text-sm font-semibold text-[#171a1f]">{{ $teacher->user->name }}</span>
            </div>
          </td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">{{ $teacher->user->email }}</td>
          <td class="px-6 py-4">
            <div class="space-y-1">
              <div class="inline-flex items-center gap-2">
                <span class="text-sm font-semibold text-[#171a1f]">{{ $teacher->students->count() }}</span>
                <span class="text-xs text-[#565d6d]">murid</span>
              </div>
              @if($teacher->students->count())
                <p class="text-xs text-[#565d6d]">{{ $teacher->students->take(3)->pluck('name')->join(', ') }}{{ $teacher->students->count() > 3 ? ' ...' : '' }}</p>
              @endif
            </div>
          </td>
          <td class="px-6 py-4">
            <span class="status-pill {{ $teacher->status === 'aktif' ? 'status-active' : ($teacher->status === 'cuti' ? 'status-cuti' : 'status-nonaktif') }}">{{ ucfirst($teacher->status) }}</span>
          </td>
          <td class="px-6 py-4 text-right">
            <div class="flex items-center justify-end gap-1">
              <a href="{{ route('admin.guru.show', $teacher) }}" class="p-2 text-[#7c3aed] hover:bg-purple-50 rounded-lg" title="Lihat Detail">
                <iconify-icon icon="lucide:eye" width="14"></iconify-icon>
              </a>
              <button onclick="openEditGuru({{ $teacher->id }}, '{{ addslashes($teacher->user->name) }}', '{{ $teacher->user->email }}', '{{ $teacher->nip }}', '{{ $teacher->status }}')" class="p-2 text-[#F97316] hover:bg-orange-50 rounded-lg" title="Edit">
                <iconify-icon icon="lucide:pencil" width="14"></iconify-icon>
              </button>
              <form method="POST" action="{{ route('admin.guru.destroy', $teacher) }}" id="form-del-guru-{{ $teacher->id }}" class="inline">
                @csrf @method('DELETE')
                <button type="button" onclick="showDeleteModal('form-del-guru-{{ $teacher->id }}', 'Guru', '{{ addslashes($teacher->user->name) }}')" class="p-2 text-[#D92626] hover:bg-red-50 rounded-lg" title="Hapus">
                  <iconify-icon icon="lucide:trash-2" width="14"></iconify-icon>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="px-6 py-8 text-center text-sm text-[#565d6d]">Belum ada data guru.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div class="p-4 border-t border-[#dee1e6] flex flex-col sm:flex-row items-center justify-between gap-4">
    <p class="text-sm text-[#565d6d] font-roboto">Menampilkan <span class="font-bold">{{ $teachers->firstItem() ?? 0 }}-{{ $teachers->lastItem() ?? 0 }}</span> dari <span class="font-bold">{{ $teachers->total() }}</span> Motivator</p>
    {{ $teachers->links() }}
  </div>
</div>

<!-- Bottom Panels -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
  <div class="xl:col-span-2 bg-gradient-to-br from-[#F97316] to-[#EA580C] p-6 lg:p-8 rounded-2xl main-shadow flex flex-col sm:flex-row gap-6">
    <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex-shrink-0 flex items-center justify-center">
      <iconify-icon icon="lucide:lightbulb" width="22" class="text-[#F97316]"></iconify-icon>
    </div>
    <div>
      <h4 class="text-lg font-bold text-white mb-2" style="color: white;">Tips Manajemen Motivator</h4>
      <p class="text-white/80 text-sm leading-relaxed">
        Pastikan beban kelas setiap guru tidak melebihi 5 kelas per minggu untuk menjaga kualitas pengajaran BiMBA AIUEO yang optimal bagi anak-anak.
      </p>
    </div>
  </div>
  <div class="bg-white p-6 rounded-2xl border border-[#dee1e6] main-shadow flex flex-col items-center text-center">
    <div class="w-12 h-12 bg-[#F97316]/10 rounded-full flex items-center justify-center mb-4">
      <iconify-icon icon="lucide:help-circle" width="22" class="text-[#C2410C]"></iconify-icon>
    </div>
    <h4 class="text-base font-bold text-[#171a1f] mb-1">Butuh Bantuan?</h4>
    <p class="text-xs text-[#565d6d] mb-6">Hubungi tim IT Pusat untuk bantuan teknis.</p>
    @php
      $supportWa = preg_replace('/\D+/', '', \App\Models\Setting::get('support_whatsapp', ''));
      $supportWaUrl = $supportWa ? 'https://wa.me/' . $supportWa . '?text=Halo%20Admin%20BiMBA%2C%20saya%20butuh%20bantuan%20terkait%20sistem%20E-Rapor.' : null;
    @endphp
    @if($supportWaUrl)
    <a href="{{ $supportWaUrl }}" target="_blank" rel="noopener" class="w-full py-2 border border-[#F97316] text-[#C2410C] text-sm font-medium rounded-xl hover:bg-[#F97316]/5 block text-center">
      <iconify-icon icon="lucide:message-circle" width="14" class="inline mr-1"></iconify-icon>
      Pusat Bantuan
    </a>
    @else
    <span class="w-full py-2 border border-[#dee1e6] text-[#9ca3af] text-sm font-medium rounded-xl block text-center cursor-not-allowed">
      <iconify-icon icon="lucide:message-circle" width="14" class="inline mr-1"></iconify-icon>
      Pusat Bantuan
    </span>
    @endif
  </div>
</div>

<!-- Success Message -->
@if(session('success'))
<div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg text-sm font-medium z-50" x-data x-init="setTimeout(() => $el.remove(), 3000)">
  {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-xl shadow-lg text-sm font-medium z-50" x-data x-init="setTimeout(() => $el.remove(), 5000)">
  @foreach($errors->all() as $error)
    <p>{{ $error }}</p>
  @endforeach
</div>
@endif

<!-- Modal Tambah Guru -->
<div id="modal-tambah-guru" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
  <div class="bg-white rounded-2xl p-8 w-full max-w-lg mx-4 shadow-xl max-h-[90vh] overflow-y-auto">
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-xl font-bold text-[#171a1f]">Tambah Guru Baru</h3>
      <button onclick="document.getElementById('modal-tambah-guru').classList.add('hidden')" class="text-[#565d6d] hover:text-[#171a1f]">
        <iconify-icon icon="lucide:x" width="20"></iconify-icon>
      </button>
    </div>
    <form method="POST" action="{{ route('admin.guru.store') }}" class="space-y-4">
      @csrf
      <div>
        <label class="block text-sm font-medium text-[#565d6d] mb-1">Nama Lengkap</label>
        <input type="text" name="name" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none" placeholder="Nama Guru">
        <p class="text-xs text-[#565d6d] mt-1">NIP akan dibuat otomatis</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-[#565d6d] mb-1">Email</label>
        <input type="email" name="email" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none" placeholder="guru@bimba.id">
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Password</label>
          <input type="text" name="password" class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none" placeholder="Default: password123">
        </div>
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Status</label>
          <select name="status" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none">
            <option value="aktif">Aktif</option>
            <option value="cuti">Cuti</option>
            <option value="nonaktif">Nonaktif</option>
          </select>
        </div>
      </div>
      <div class="rounded-xl border border-[#dee1e6] bg-[#fafafb] p-4 text-sm text-[#565d6d]">
        Guru tidak perlu dipilihkan kelas di menu ini. Murid yang dibimbing ditentukan dari menu <strong>Admin → Murid</strong> melalui pilihan <strong>Guru Pembimbing</strong>.
      </div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="document.getElementById('modal-tambah-guru').classList.add('hidden')" class="flex-1 py-2.5 border border-[#dee1e6] rounded-xl text-sm font-medium text-[#565d6d] hover:bg-gray-50">Batal</button>
        <button type="submit" class="flex-1 py-2.5 bg-[#F97316] text-white rounded-xl text-sm font-medium hover:bg-orange-600">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Guru -->
<div id="modal-edit-guru" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
  <div class="bg-white rounded-2xl p-8 w-full max-w-lg mx-4 shadow-xl max-h-[90vh] overflow-y-auto">
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-xl font-bold text-[#171a1f]">Edit Data Guru</h3>
      <button onclick="document.getElementById('modal-edit-guru').classList.add('hidden')" class="text-[#565d6d] hover:text-[#171a1f]">
        <iconify-icon icon="lucide:x" width="20"></iconify-icon>
      </button>
    </div>
    <form id="form-edit-guru" method="POST" class="space-y-4">
      @csrf @method('PUT')
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Nama Lengkap</label>
          <input type="text" name="name" id="edit-name" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none">
        </div>
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">NIP</label>
          <input type="text" id="edit-nip" readonly class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm bg-gray-50 text-[#565d6d] cursor-not-allowed">
          <p class="text-xs text-[#565d6d] mt-1">NIP tidak dapat diubah</p>
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-[#565d6d] mb-1">Email</label>
        <input type="email" name="email" id="edit-email" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none">
      </div>
      <div>
        <label class="block text-sm font-medium text-[#565d6d] mb-1">Status</label>
        <select name="status" id="edit-status" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none">
          <option value="aktif">Aktif</option>
          <option value="cuti">Cuti</option>
          <option value="nonaktif">Nonaktif</option>
        </select>
      </div>
      <div class="rounded-xl border border-[#dee1e6] bg-[#fafafb] p-4 text-sm text-[#565d6d]">
        Guru tidak terikat langsung ke kelas. Data murid dibimbing tetap diatur dari menu <strong>Admin → Murid</strong>.
      </div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="document.getElementById('modal-edit-guru').classList.add('hidden')" class="flex-1 py-2.5 border border-[#dee1e6] rounded-xl text-sm font-medium text-[#565d6d] hover:bg-gray-50">Batal</button>
        <button type="submit" class="flex-1 py-2.5 bg-[#F97316] text-white rounded-xl text-sm font-medium hover:bg-orange-600">Perbarui</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEditGuru(id, name, email, nip, status) {
  document.getElementById('form-edit-guru').action = '/admin/guru/' + id;
  document.getElementById('edit-name').value = name;
  document.getElementById('edit-email').value = email;
  document.getElementById('edit-nip').value = nip;
  document.getElementById('edit-status').value = status;
  document.getElementById('modal-edit-guru').classList.remove('hidden');
}
</script>

<style>
  .status-pill {
    @apply inline-block px-3 py-1.5 text-xs font-medium rounded-lg;
  }
  .status-pill.status-active {
    @apply bg-[#DCFAE6] text-[#166534];
  }
  .status-pill.status-cuti {
    @apply bg-[#FEF3C7] text-[#92400E];
  }
  .status-pill.status-nonaktif {
    @apply bg-[#FEE2E2] text-[#991B1B];
  }
</style>

<script>
let _deleteFormId = null;
function showDeleteModal(formId, type, name) {
  _deleteFormId = formId;
  document.getElementById('del-modal-type').textContent = type;
  document.getElementById('del-modal-name').textContent = name;
  const m = document.getElementById('modal-delete-list');
  m.classList.remove('hidden'); m.classList.add('flex');
}
function closeDeleteModal() {
  document.getElementById('modal-delete-list').classList.add('hidden');
  document.getElementById('modal-delete-list').classList.remove('flex');
  _deleteFormId = null;
}
function doDelete() {
  if (_deleteFormId) document.getElementById(_deleteFormId).submit();
}
</script>

<!-- Delete Confirmation Modal -->
<div id="modal-delete-list" class="hidden fixed inset-0 z-50 bg-black/50 items-center justify-center">
  <div class="bg-white rounded-2xl p-8 w-full max-w-md mx-4 shadow-xl">
    <div class="flex items-center gap-3 mb-4">
      <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
        <iconify-icon icon="lucide:alert-triangle" width="24" class="text-red-600"></iconify-icon>
      </div>
      <div>
        <h3 class="text-lg font-bold text-[#171a1f]">Konfirmasi Hapus</h3>
        <p class="text-sm text-[#565d6d]">Tindakan ini tidak dapat dibatalkan</p>
      </div>
    </div>
    <p class="text-sm text-[#565d6d] mb-6">Anda yakin ingin menghapus <span class="font-semibold text-[#171a1f]" id="del-modal-type"></span> <span class="font-semibold text-[#171a1f]" id="del-modal-name"></span>? Data tidak dapat dipulihkan.</p>
    <div class="flex gap-3">
      <button type="button" onclick="closeDeleteModal()" class="flex-1 py-2.5 border border-[#dee1e6] rounded-xl text-sm font-medium text-[#565d6d] hover:bg-gray-50">Batal</button>
      <button type="button" onclick="doDelete()" class="flex-1 py-2.5 bg-red-600 text-white rounded-xl text-sm font-medium hover:bg-red-700">OK, Hapus</button>
    </div>
  </div>
</div>

@endsection
