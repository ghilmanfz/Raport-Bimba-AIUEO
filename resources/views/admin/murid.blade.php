@extends('layouts.admin')

@section('title', 'Manajemen Data Murid - E-Rapor BiMBA')
@section('page-title', 'Manajemen Data Murid')

@section('content')
<!-- Page Title & Actions -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
  <div>
    <h1 class="text-2xl lg:text-3xl font-bold tracking-tight">Manajemen Data Murid</h1>
    <p class="text-[#565d6d] mt-1 font-roboto">Kelola informasi murid, kelas, dan status pembelajaran.</p>
  </div>
  <div class="flex flex-wrap items-center gap-3">
    <a href="{{ route('admin.murid.export') }}" class="flex items-center gap-2 px-4 py-2 bg-white border border-[#F97316]/30 rounded-xl text-[#F97316] font-medium text-sm hover:bg-blue-50">
      <iconify-icon icon="lucide:download" width="16"></iconify-icon>
      Export CSV
    </a>
    <button onclick="document.getElementById('modal-tambah-murid').classList.remove('hidden')" class="flex items-center gap-2 px-4 py-2 bg-[#F97316] text-white rounded-xl font-medium text-sm hover:bg-orange-600">
      <iconify-icon icon="lucide:plus" width="16"></iconify-icon>
      Tambah Murid
    </button>
  </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
  <div class="bg-[#FFF7ED] border border-[#F97316]/20 rounded-xl p-6 main-shadow">
    <div class="flex justify-between items-start">
      <div>
        <p class="text-sm font-medium text-[#565d6d]">Total Murid</p>
        <h3 class="text-3xl font-bold mt-2 text-[#171a1f]">{{ $totalMurid }}</h3>
      </div>
      <div class="w-12 h-12 bg-[#F97316]/20 rounded-2xl flex items-center justify-center">
        <iconify-icon icon="lucide:users" width="24" class="text-[#F97316]"></iconify-icon>
      </div>
    </div>
    <p class="mt-4 text-xs text-[#565d6d] font-roboto">Terdaftar tahun ajaran {{ date('Y') }}/{{ date('Y') + 1 }}</p>
  </div>
  <div class="bg-[#EDFDF1] border border-[#F97316]/20 rounded-xl p-6 main-shadow">
    <div class="flex justify-between items-start">
      <div>
        <p class="text-sm font-medium text-[#565d6d]">Murid Aktif</p>
        <h3 class="text-3xl font-bold mt-2 text-[#171a1f]">{{ $muridAktif }}</h3>
      </div>
      <div class="w-12 h-12 bg-[#F97316]/20 rounded-2xl flex items-center justify-center">
        <iconify-icon icon="lucide:user-check" width="24" class="text-[#C2410C]"></iconify-icon>
      </div>
    </div>
    <p class="mt-4 text-xs text-[#565d6d] font-roboto">Aktif dalam kegiatan belajar</p>
  </div>
  <div class="bg-white border border-[#dee1e6] rounded-xl p-6 main-shadow">
    <div class="flex justify-between items-start">
      <div>
        <p class="text-sm font-medium text-[#565d6d]">Status Cuti</p>
        <h3 class="text-3xl font-bold mt-2 text-[#171a1f]">{{ $muridCuti }}</h3>
      </div>
      <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center justify-center">
        <iconify-icon icon="lucide:pause-circle" width="24" class="text-[#565d6d]"></iconify-icon>
      </div>
    </div>
    <p class="mt-4 text-xs text-[#565d6d] font-roboto">Sedang menangguhkan kelas</p>
  </div>
</div>

<!-- Table Section -->
<div class="bg-white rounded-2xl border border-[#dee1e6] overflow-hidden main-shadow">
  <div class="p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-[#dee1e6]">
    <h2 class="text-xl font-semibold tracking-tight">Daftar Murid Terdaftar</h2>
    <div class="flex flex-wrap items-center gap-3">
      <form method="GET" action="{{ route('admin.murid') }}" class="relative w-full sm:w-64">
        <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-[#565d6d]">
          <iconify-icon icon="lucide:search" width="16"></iconify-icon>
        </div>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama atau ID..." class="w-full pl-10 pr-4 py-2 bg-white border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#F97316]/20">
      </form>
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-[#f3f4f6]/30 text-[#565d6d] text-sm font-semibold border-b border-[#dee1e6]">
          <th class="px-6 py-4">ID</th>
          <th class="px-6 py-4">Nama Murid</th>
          <th class="px-6 py-4">Level</th>
          <th class="px-6 py-4">Wali Murid</th>
          <th class="px-6 py-4">Tgl. Bergabung</th>
          <th class="px-6 py-4">Status</th>
          <th class="px-6 py-4 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#dee1e6]">
        @forelse($students as $student)
        <tr class="hover:bg-gray-50/50">
          <td class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">{{ $student->nis }}</td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full bg-[#F97316] flex items-center justify-center text-white font-bold text-xs">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
              <span class="text-sm font-medium text-[#171a1f]">{{ $student->name }}</span>
            </div>
          </td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">{{ $student->classroom?->name ?? '-' }}</td>
          <td class="px-6 py-4">
            @if($student->parent)
              <div class="font-roboto">
                <p class="text-sm font-medium text-[#171a1f]">{{ $student->parent->name }}</p>
                <p class="text-xs text-[#565d6d]">{{ $student->parent->email }} &middot; {{ $student->parent->students_count }} anak</p>
              </div>
            @else
              <span class="text-sm text-[#9095a0] font-roboto">Belum terhubung</span>
            @endif
          </td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">{{ $student->join_date->translatedFormat('d M Y') }}</td>
          <td class="px-6 py-4"><span class="status-pill {{ $student->status === 'aktif' ? 'status-active' : 'status-cuti' }}">{{ ucfirst($student->status) }}</span></td>
          <td class="px-6 py-4 text-right">
            <div class="flex items-center justify-end gap-1">
              <button onclick="openEditMurid({{ $student->id }}, '{{ addslashes($student->name) }}', '{{ $student->nis }}', '{{ $student->classroom_id }}', '{{ $student->join_date->format('Y-m-d') }}', '{{ $student->status }}')" class="p-2 text-[#F97316] hover:bg-blue-50 rounded-lg"><iconify-icon icon="lucide:pencil" width="14"></iconify-icon></button>
              <form method="POST" action="{{ route('admin.murid.destroy', $student) }}" onsubmit="return confirm('Yakin hapus data murid ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="p-2 text-[#F97316] hover:bg-red-50 rounded-lg"><iconify-icon icon="lucide:trash-2" width="14"></iconify-icon></button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="px-6 py-8 text-center text-sm text-[#565d6d]">Belum ada data murid.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div class="p-6 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-[#dee1e6]">
    <p class="text-sm text-[#565d6d] font-roboto">Menampilkan <span class="font-medium">{{ $students->firstItem() ?? 0 }}-{{ $students->lastItem() ?? 0 }}</span> dari <span class="font-medium">{{ $students->total() }}</span> murid</p>
    {{ $students->links() }}
  </div>
</div>

<!-- Success Message -->
@if(session('success'))
<div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg text-sm font-medium z-50" x-data x-init="setTimeout(() => $el.remove(), 3000)">
  {{ session('success') }}
</div>
@endif

<!-- Modal Tambah Murid -->
@php
  $parentMode = old('parent_mode', 'none');
@endphp
<div id="modal-tambah-murid" class="{{ $errors->any() ? '' : 'hidden' }} fixed inset-0 z-50 flex items-center justify-center bg-black/50">
  <div class="bg-white rounded-2xl p-8 w-full max-w-2xl mx-4 shadow-xl max-h-[90vh] overflow-y-auto">
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-xl font-bold text-[#171a1f]">Tambah Murid Baru</h3>
      <button onclick="document.getElementById('modal-tambah-murid').classList.add('hidden')" class="text-[#565d6d] hover:text-[#171a1f]">
        <iconify-icon icon="lucide:x" width="20"></iconify-icon>
      </button>
    </div>
    <form method="POST" action="{{ route('admin.murid.store') }}" class="space-y-4">
      @csrf
      @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
          <p class="font-semibold">Data belum lengkap</p>
          <ul class="mt-1 list-disc pl-4 space-y-1">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
      <div>
        <label class="block text-sm font-medium text-[#565d6d] mb-1">Nama Murid</label>
        <input type="text" name="name" required value="{{ old('name') }}" class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none" placeholder="Nama Lengkap">
        <p class="text-xs text-[#565d6d] mt-1">NIS akan dibuat otomatis</p>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Level</label>
          <select name="classroom_id" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none">
            @foreach($classrooms as $classroom)
              <option value="{{ $classroom->id }}" @selected(old('classroom_id') == $classroom->id)>{{ $classroom->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Tgl. Bergabung</label>
          <input type="date" name="join_date" required value="{{ old('join_date', date('Y-m-d')) }}" class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none">
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-[#565d6d] mb-1">Status</label>
        <select name="status" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none">
          <option value="aktif" @selected(old('status', 'aktif') === 'aktif')>Aktif</option>
          <option value="cuti" @selected(old('status') === 'cuti')>Cuti</option>
          <option value="nonaktif" @selected(old('status') === 'nonaktif')>Nonaktif</option>
        </select>
      </div>
      <hr class="border-[#dee1e6]">

      <div class="space-y-3">
        <div>
          <p class="text-sm font-semibold text-[#171a1f]">Akun Wali Murid</p>
          <p class="text-xs text-[#565d6d] mt-1 font-roboto">Satu akun wali bisa dipakai untuk beberapa murid sekaligus.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
          <label class="cursor-pointer">
            <input type="radio" name="parent_mode" value="none" class="sr-only peer" onchange="setParentMode(this.value)" @checked($parentMode === 'none')>
            <span class="h-full flex items-start gap-2 rounded-xl border border-[#dee1e6] bg-white px-3 py-3 text-sm text-[#565d6d] peer-checked:border-[#F97316] peer-checked:bg-[#FFF7ED] peer-checked:text-[#C2410C]">
              <iconify-icon icon="lucide:user-x" width="16" class="mt-0.5"></iconify-icon>
              <span class="font-medium">Tanpa wali dulu</span>
            </span>
          </label>
          <label class="cursor-pointer">
            <input type="radio" name="parent_mode" value="existing" class="sr-only peer" onchange="setParentMode(this.value)" @checked($parentMode === 'existing')>
            <span class="h-full flex items-start gap-2 rounded-xl border border-[#dee1e6] bg-white px-3 py-3 text-sm text-[#565d6d] peer-checked:border-[#F97316] peer-checked:bg-[#FFF7ED] peer-checked:text-[#C2410C]">
              <iconify-icon icon="lucide:users" width="16" class="mt-0.5"></iconify-icon>
              <span class="font-medium">Pilih wali yang sudah ada</span>
            </span>
          </label>
          <label class="cursor-pointer">
            <input type="radio" name="parent_mode" value="new" class="sr-only peer" onchange="setParentMode(this.value)" @checked($parentMode === 'new')>
            <span class="h-full flex items-start gap-2 rounded-xl border border-[#dee1e6] bg-white px-3 py-3 text-sm text-[#565d6d] peer-checked:border-[#F97316] peer-checked:bg-[#FFF7ED] peer-checked:text-[#C2410C]">
              <iconify-icon icon="lucide:user-plus" width="16" class="mt-0.5"></iconify-icon>
              <span class="font-medium">Buat akun wali baru</span>
            </span>
          </label>
        </div>

        <div data-parent-panel="none" class="{{ $parentMode === 'none' ? '' : 'hidden' }} rounded-xl border border-[#dee1e6] bg-[#f9fafb] px-4 py-3">
          <p class="text-sm font-medium text-[#171a1f]">Murid disimpan tanpa akun wali.</p>
          <p class="text-xs text-[#565d6d] mt-1 font-roboto">Gunakan opsi ini jika data wali belum tersedia saat murid didaftarkan.</p>
        </div>

        @php
          $selectedParentId = old('existing_parent_id');
          $selectedWali = $selectedParentId ? $waliUsers->firstWhere('id', (int) $selectedParentId) : null;
        @endphp
        <div data-parent-panel="existing" class="{{ $parentMode === 'existing' ? '' : 'hidden' }} space-y-2">
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Pilih Akun Wali</label>
          <div class="relative" data-wali-picker>
            <input type="hidden" name="existing_parent_id" id="existing-parent-id" value="{{ $selectedWali?->id ?? '' }}" @if($parentMode === 'existing') required @endif>
            <button type="button" data-wali-toggle class="w-full flex items-center justify-between gap-3 px-4 py-2 border border-[#dee1e6] rounded-xl text-sm text-left bg-white focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none">
              <span data-wali-selected class="{{ $selectedWali ? 'text-[#171a1f]' : 'text-[#565d6d]' }}">
                {{ $selectedWali ? $selectedWali->name . ' - ' . $selectedWali->email . ' (' . $selectedWali->students_count . ' anak)' : 'Pilih wali murid' }}
              </span>
              <iconify-icon icon="lucide:chevron-down" width="16" class="text-[#565d6d]"></iconify-icon>
            </button>
            <div data-wali-menu class="hidden absolute z-[70] mt-2 w-full overflow-hidden rounded-xl border border-[#dee1e6] bg-white shadow-xl">
              <div class="border-b border-[#dee1e6] p-2">
                <div class="relative">
                  <div class="absolute inset-y-0 left-3 flex items-center text-[#565d6d] pointer-events-none">
                    <iconify-icon icon="lucide:search" width="15"></iconify-icon>
                  </div>
                  <input type="text" data-wali-search placeholder="Cari nama atau email wali" class="w-full rounded-lg border border-[#dee1e6] px-9 py-2 text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none">
                </div>
              </div>
              <div class="max-h-56 overflow-y-auto py-1">
            @forelse($waliUsers as $wali)
                @php
                  $waliLabel = $wali->name . ' - ' . $wali->email . ' (' . $wali->students_count . ' anak)';
                @endphp
                <button type="button" data-wali-option data-value="{{ $wali->id }}" data-label="{{ $waliLabel }}" data-search="{{ strtolower($wali->name . ' ' . $wali->email) }}" class="w-full px-4 py-2.5 text-left text-sm hover:bg-[#FFF7ED] focus:bg-[#FFF7ED] focus:outline-none">
                  <span class="block font-medium text-[#171a1f]">{{ $wali->name }}</span>
                  <span class="block text-xs text-[#565d6d]">{{ $wali->email }} &middot; {{ $wali->students_count }} anak</span>
                </button>
            @empty
                <p class="px-4 py-3 text-sm text-[#565d6d]">Belum ada akun wali</p>
            @endforelse
                <p data-wali-empty class="hidden px-4 py-3 text-sm text-[#565d6d]">Wali tidak ditemukan.</p>
              </div>
            </div>
          </div>
          <p class="text-xs text-[#565d6d] font-roboto">Gunakan opsi ini jika wali sudah pernah dibuat, termasuk saat wali punya 2 murid atau lebih.</p>
        </div>

        <div data-parent-panel="new" class="{{ $parentMode === 'new' ? '' : 'hidden' }} space-y-3">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-[#565d6d] mb-1">Nama Wali</label>
              <input type="text" name="parent_name" id="parent-name" value="{{ old('parent_name') }}" class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none" placeholder="Nama Wali" @if($parentMode === 'new') required @endif>
            </div>
            <div>
              <label class="block text-sm font-medium text-[#565d6d] mb-1">Email Wali</label>
              <input type="email" name="parent_email" id="parent-email" value="{{ old('parent_email') }}" class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none" placeholder="email@wali.com" @if($parentMode === 'new') required @endif>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-[#565d6d] mb-1">Password Awal Wali</label>
            <input type="text" name="parent_password" id="parent-password" value="{{ old('parent_password') }}" class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none" placeholder="Minimal 6 karakter" @if($parentMode === 'new') required @endif>
            <p class="text-xs text-[#565d6d] mt-1 font-roboto">Email dan password ini dipakai wali untuk login ke dashboard wali murid.</p>
          </div>
        </div>
      </div>

      <div class="flex gap-3 pt-2">
        <button type="button" onclick="document.getElementById('modal-tambah-murid').classList.add('hidden')" class="flex-1 py-2.5 border border-[#dee1e6] rounded-xl text-sm font-medium text-[#565d6d] hover:bg-gray-50">Batal</button>
        <button type="submit" class="flex-1 py-2.5 bg-[#F97316] text-white rounded-xl text-sm font-medium hover:bg-orange-600">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Murid -->
<div id="modal-edit-murid" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
  <div class="bg-white rounded-2xl p-8 w-full max-w-lg mx-4 shadow-xl">
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-xl font-bold text-[#171a1f]">Edit Data Murid</h3>
      <button onclick="document.getElementById('modal-edit-murid').classList.add('hidden')" class="text-[#565d6d] hover:text-[#171a1f]">
        <iconify-icon icon="lucide:x" width="20"></iconify-icon>
      </button>
    </div>
    <form id="form-edit-murid" method="POST" class="space-y-4">
      @csrf @method('PUT')
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">NIS</label>
          <input type="text" id="edit-murid-nis" readonly class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm bg-gray-50 text-[#565d6d] cursor-not-allowed">
          <p class="text-xs text-[#565d6d] mt-1">NIS tidak dapat diubah</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Nama Murid</label>
          <input type="text" name="name" id="edit-murid-name" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none">
        </div>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Level</label>
          <select name="classroom_id" id="edit-murid-classroom" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none">
            @foreach($classrooms as $classroom)
              <option value="{{ $classroom->id }}">{{ $classroom->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Tgl. Bergabung</label>
          <input type="date" name="join_date" id="edit-murid-joindate" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none">
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-[#565d6d] mb-1">Status</label>
        <select name="status" id="edit-murid-status" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none">
          <option value="aktif">Aktif</option>
          <option value="cuti">Cuti</option>
        </select>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="document.getElementById('modal-edit-murid').classList.add('hidden')" class="flex-1 py-2.5 border border-[#dee1e6] rounded-xl text-sm font-medium text-[#565d6d] hover:bg-gray-50">Batal</button>
        <button type="submit" class="flex-1 py-2.5 bg-[#F97316] text-white rounded-xl text-sm font-medium hover:bg-orange-600">Perbarui</button>
      </div>
    </form>
  </div>
</div>

<script>
function setParentMode(mode) {
  document.querySelectorAll('[data-parent-panel]').forEach((panel) => {
    panel.classList.toggle('hidden', panel.dataset.parentPanel !== mode);
  });

  const existingParent = document.getElementById('existing-parent-id');
  const parentName = document.getElementById('parent-name');
  const parentEmail = document.getElementById('parent-email');
  const parentPassword = document.getElementById('parent-password');

  if (existingParent) existingParent.required = mode === 'existing';
  [parentName, parentEmail, parentPassword].forEach((field) => {
    if (field) field.required = mode === 'new';
  });

  if (mode !== 'existing') {
    document.querySelector('[data-wali-menu]')?.classList.add('hidden');
  }
}

setParentMode(document.querySelector('input[name="parent_mode"]:checked')?.value || 'none');

function initWaliPicker() {
  const picker = document.querySelector('[data-wali-picker]');
  if (!picker) return;

  const toggle = picker.querySelector('[data-wali-toggle]');
  const menu = picker.querySelector('[data-wali-menu]');
  const search = picker.querySelector('[data-wali-search]');
  const hidden = document.getElementById('existing-parent-id');
  const selectedLabel = picker.querySelector('[data-wali-selected]');
  const options = Array.from(picker.querySelectorAll('[data-wali-option]'));
  const empty = picker.querySelector('[data-wali-empty]');

  const openMenu = () => {
    menu.classList.remove('hidden');
    search.value = '';
    filterOptions('');
    setTimeout(() => search.focus(), 0);
  };

  const closeMenu = () => {
    menu.classList.add('hidden');
  };

  const filterOptions = (term) => {
    const keyword = term.trim().toLowerCase();
    let visibleCount = 0;

    options.forEach((option) => {
      const match = option.dataset.search.includes(keyword);
      option.classList.toggle('hidden', !match);
      if (match) visibleCount++;
    });

    if (empty) empty.classList.toggle('hidden', visibleCount > 0);
  };

  toggle.addEventListener('click', () => {
    if (menu.classList.contains('hidden')) {
      openMenu();
    } else {
      closeMenu();
    }
  });

  search.addEventListener('input', () => filterOptions(search.value));
  search.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeMenu();
  });

  options.forEach((option) => {
    option.addEventListener('click', () => {
      hidden.value = option.dataset.value;
      selectedLabel.textContent = option.dataset.label;
      selectedLabel.classList.remove('text-[#565d6d]');
      selectedLabel.classList.add('text-[#171a1f]');
      closeMenu();
    });
  });

  document.addEventListener('click', (event) => {
    if (!picker.contains(event.target)) closeMenu();
  });
}

initWaliPicker();

function openEditMurid(id, name, nis, classroomId, joinDate, status) {
  document.getElementById('form-edit-murid').action = '/admin/murid/' + id;
  document.getElementById('edit-murid-name').value = name;
  document.getElementById('edit-murid-nis').value = nis;
  document.getElementById('edit-murid-classroom').value = classroomId;
  document.getElementById('edit-murid-joindate').value = joinDate;
  document.getElementById('edit-murid-status').value = status;
  document.getElementById('modal-edit-murid').classList.remove('hidden');
}
</script>
@endsection
