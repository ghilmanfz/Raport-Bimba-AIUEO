@extends('layouts.admin')

@section('title', 'Manajemen Wali Murid - E-Rapor BiMBA')
@section('page-title', 'Manajemen Wali Murid')

@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
  <div>
    <h1 class="text-2xl lg:text-3xl font-bold tracking-tight">Manajemen Wali Murid</h1>
    <p class="text-[#565d6d] mt-1 font-roboto">Data wali murid terhubung dengan siswa. Status wali dihitung otomatis dari status siswa.</p>
  </div>
  <a href="{{ route('admin.wali.export') }}" class="flex items-center gap-2 px-4 py-2 bg-white border border-[#3d8af5]/30 rounded-xl text-[#3d8af5] font-medium text-sm hover:bg-blue-50">
    <iconify-icon icon="lucide:download" width="16"></iconify-icon>
    Export CSV
  </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
  <div class="bg-white border border-[#dee1e6] rounded-xl p-6 main-shadow">
    <p class="text-sm font-medium text-[#565d6d]">Total Wali Murid</p>
    <h3 class="text-3xl font-bold mt-2 text-[#171a1f]">{{ $totalWali }}</h3>
  </div>
  <div class="bg-white border border-[#dee1e6] rounded-xl p-6 main-shadow">
    <p class="text-sm font-medium text-[#565d6d]">Rata-rata Anak/Wali</p>
    <h3 class="text-3xl font-bold mt-2 text-[#171a1f]">{{ $totalWali > 0 ? round(\App\Models\Student::count() / $totalWali, 1) : 0 }}</h3>
  </div>
</div>

<div class="bg-white rounded-2xl border border-[#dee1e6] overflow-hidden main-shadow">
  <div class="p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-[#dee1e6]">
    <h2 class="text-xl font-semibold tracking-tight">Daftar Wali Murid</h2>
    <form method="GET" action="{{ route('admin.wali') }}" class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ayah/ibu/anak" class="px-4 py-2 border border-[#dee1e6] rounded-xl text-sm">
      <select name="status" class="px-4 py-2 border border-[#dee1e6] rounded-xl text-sm">
        <option value="">Semua Status</option>
        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
        <option value="lulus" {{ request('status') === 'lulus' ? 'selected' : '' }}>Lulus</option>
        <option value="pindah" {{ request('status') === 'pindah' ? 'selected' : '' }}>Pindah</option>
      </select>
      <button type="submit" class="px-4 py-2 bg-[#3d8af5] text-white rounded-xl text-sm">Filter</button>
    </form>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-[#f3f4f6]/30 text-[#565d6d] text-sm font-semibold border-b border-[#dee1e6]">
          <th class="px-6 py-4">Nama Murid</th>
          <th class="px-6 py-4">Nama Ayah</th>
          <th class="px-6 py-4">Nama Ibu</th>
          <th class="px-6 py-4">Alamat</th>
          <th class="px-6 py-4">Status Wali</th>
          <th class="px-6 py-4 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#dee1e6]">
        @forelse($wali as $parent)
        @php
          $studentStatuses = $parent->students->pluck('status')->unique()->toArray();
          $waliStatus = 'Tidak Ada Anak';
          if (!empty($studentStatuses)) {
            if (in_array('aktif', $studentStatuses)) {
              $waliStatus = 'Aktif';
            } elseif (count($studentStatuses) === 1 && $studentStatuses[0] === 'lulus') {
              $waliStatus = 'Lulus';
            } elseif (count($studentStatuses) === 1 && $studentStatuses[0] === 'pindah') {
              $waliStatus = 'Pindah';
            } else {
              $waliStatus = 'Aktif';
            }
          }
        @endphp
        <tr class="hover:bg-gray-50/50">
          <td class="px-6 py-4 text-sm text-[#565d6d]">{{ $parent->students->pluck('name')->join(', ') ?: '-' }}</td>
          <td class="px-6 py-4 text-sm font-medium text-[#171a1f]">{{ $parent->father_name ?: '-' }}</td>
          <td class="px-6 py-4 text-sm font-medium text-[#171a1f]">{{ $parent->mother_name ?: '-' }}</td>
          <td class="px-6 py-4 text-sm text-[#565d6d]">{{ $parent->address ?: '-' }}</td>
          <td class="px-6 py-4 text-sm">
            <span class="status-pill {{ $waliStatus === 'Aktif' ? 'status-active' : ($waliStatus === 'Lulus' ? 'status-lulus' : ($waliStatus === 'Pindah' ? 'status-pindah' : 'status-neutral')) }}">{{ $waliStatus }}</span>
          </td>
          <td class="px-6 py-4 text-right">
            <div class="flex items-center justify-end gap-1">
              <a href="{{ route('admin.wali.show', $parent) }}" class="p-2 text-[#7c3aed] hover:bg-purple-50 rounded-lg" title="Lihat Detail">
                <iconify-icon icon="lucide:eye" width="14"></iconify-icon>
              </a>
              <button
                onclick="openEditWali({{ $parent->id }}, '{{ addslashes($parent->father_name ?? '') }}', '{{ addslashes($parent->mother_name ?? '') }}', '{{ addslashes($parent->father_phone ?? '') }}', '{{ addslashes($parent->mother_phone ?? '') }}', '{{ addslashes($parent->address ?? '') }}', '{{ $parent->email }}')"
                class="p-2 text-[#3d8af5] hover:bg-blue-50 rounded-lg" title="Edit"
              >
                <iconify-icon icon="lucide:pencil" width="14"></iconify-icon>
              </button>
              <form method="POST" action="{{ route('admin.wali.destroy', $parent) }}" id="form-del-wali-{{ $parent->id }}" class="inline">
                @csrf @method('DELETE')
                <button type="button" onclick="showDeleteModal('form-del-wali-{{ $parent->id }}', 'Wali Murid', '{{ addslashes($parent->father_name ?? $parent->name) }}')" class="p-2 text-[#D92626] hover:bg-red-50 rounded-lg" title="Hapus">
                  <iconify-icon icon="lucide:trash-2" width="14"></iconify-icon>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="px-6 py-8 text-center text-sm text-[#565d6d]">Belum ada data wali murid.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="p-6 border-t border-[#dee1e6]">
    {{ $wali->links() }}
  </div>
</div>

<div id="modal-edit-wali" class="hidden fixed inset-0 z-50 bg-black/50 items-center justify-center">
  <div class="bg-white rounded-2xl p-8 w-full max-w-lg mx-4 shadow-xl max-h-[90vh] overflow-y-auto">
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-xl font-bold text-[#171a1f]">Edit Data Wali Murid</h3>
      <button onclick="closeWaliModal()" class="text-[#565d6d] hover:text-[#171a1f]">
        <iconify-icon icon="lucide:x" width="20"></iconify-icon>
      </button>
    </div>
    <form id="form-edit-wali" method="POST" class="space-y-4">
      @csrf @method('PUT')
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Nama Ayah</label>
          <input type="text" name="father_name" id="edit-father-name" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Nama Ibu</label>
          <input type="text" name="mother_name" id="edit-mother-name" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm">
        </div>
      </div>
      
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Kontak Ayah (Opsional)</label>
          <input type="text" name="father_phone" id="edit-father-phone" class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Kontak Ibu (Opsional)</label>
          <input type="text" name="mother_phone" id="edit-mother-phone" class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm">
        </div>
      </div>
      
      <div>
        <label class="block text-sm font-medium text-[#565d6d] mb-1">Alamat</label>
        <textarea name="address" id="edit-address" rows="3" class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm"></textarea>
      </div>

      <hr class="border-[#dee1e6] my-4">
      <h4 class="text-sm font-semibold text-[#171a1f]">Keamanan Akun</h4>

      <div>
        <label class="block text-sm font-medium text-[#565d6d] mb-1">Password Baru (Opsional)</label>
        <input type="password" name="password" id="edit-password" class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm" placeholder="Biarkan kosong jika tidak ingin mengubah">
        <p class="mt-1 text-xs text-[#565d6d]">Minimal 8 karakter. Kosongkan jika tidak ingin mengubah password.</p>
      </div>

      <div class="flex gap-3 pt-2">
        <button type="button" onclick="closeWaliModal()" class="flex-1 py-2.5 border border-[#dee1e6] rounded-xl text-sm font-medium text-[#565d6d] hover:bg-gray-50">Batal</button>
        <button type="submit" class="flex-1 py-2.5 bg-[#3d8af5] text-white rounded-xl text-sm font-medium hover:bg-blue-600">Simpan</button>
      </div>
    </form>
  </div>
</div>

@if(session('success'))
<div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg text-sm font-medium z-50" x-data x-init="setTimeout(() => $el.remove(), 3000)">
  {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-xl shadow-lg text-sm font-medium z-50" x-data x-init="setTimeout(() => $el.remove(), 4000)">
  {{ session('error') }}
</div>
@endif

<script>
function openEditWali(id, fatherName, motherName, fatherPhone, motherPhone, address, email) {
  document.getElementById('form-edit-wali').action = '/admin/wali/' + id;
  document.getElementById('edit-father-name').value = fatherName || '';
  document.getElementById('edit-mother-name').value = motherName || '';
  document.getElementById('edit-father-phone').value = fatherPhone || '';
  document.getElementById('edit-mother-phone').value = motherPhone || '';
  document.getElementById('edit-address').value = address || '';
  document.getElementById('edit-password').value = '';
  openWaliModal();
}

function openWaliModal() {
  const modal = document.getElementById('modal-edit-wali');
  modal.classList.remove('hidden');
  modal.classList.add('flex');
}

function closeWaliModal() {
  const modal = document.getElementById('modal-edit-wali');
  modal.classList.add('hidden');
  modal.classList.remove('flex');
}

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
      <button type="button" onclick="doDelete()" class="flex-1 py-2.5 bg-red-600 text-white rounded-xl text-sm font-medium hover:bg-red-700">Hapus</button>
    </div>
  </div>
</div>

@endsection
