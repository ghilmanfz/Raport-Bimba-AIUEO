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
    <button class="flex items-center gap-2 px-4 py-2 bg-white border border-[#3d8af5]/30 rounded-xl text-[#3d8af5] font-medium text-sm hover:bg-blue-50">
      <iconify-icon icon="lucide:download" width="16"></iconify-icon>
      Export CSV
    </button>
    <button onclick="document.getElementById('modal-tambah-murid').classList.remove('hidden')" class="flex items-center gap-2 px-4 py-2 bg-[#3d8af5] text-white rounded-xl font-medium text-sm hover:bg-blue-600">
      <iconify-icon icon="lucide:plus" width="16"></iconify-icon>
      Tambah Murid
    </button>
  </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
  <div class="bg-[#F1F6FE] border border-[#3d8af5]/20 rounded-xl p-6 main-shadow">
    <div class="flex justify-between items-start">
      <div>
        <p class="text-sm font-medium text-[#565d6d]">Total Murid</p>
        <h3 class="text-3xl font-bold mt-2 text-[#171a1f]">{{ $totalMurid }}</h3>
      </div>
      <div class="w-12 h-12 bg-[#3d8af5]/20 rounded-2xl flex items-center justify-center">
        <iconify-icon icon="lucide:users" width="24" class="text-[#3d8af5]"></iconify-icon>
      </div>
    </div>
    <p class="mt-4 text-xs text-[#565d6d] font-roboto">Terdaftar tahun ajaran {{ date('Y') }}/{{ date('Y') + 1 }}</p>
  </div>
  <div class="bg-[#EDFDF1] border border-[#63e98f]/20 rounded-xl p-6 main-shadow">
    <div class="flex justify-between items-start">
      <div>
        <p class="text-sm font-medium text-[#565d6d]">Murid Aktif</p>
        <h3 class="text-3xl font-bold mt-2 text-[#171a1f]">{{ $muridAktif }}</h3>
      </div>
      <div class="w-12 h-12 bg-[#63e98f]/20 rounded-2xl flex items-center justify-center">
        <iconify-icon icon="lucide:user-check" width="24" class="text-[#16a34a]"></iconify-icon>
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
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama atau ID..." class="w-full pl-10 pr-4 py-2 bg-white border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20">
      </form>
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-[#f3f4f6]/30 text-[#565d6d] text-sm font-semibold border-b border-[#dee1e6]">
          <th class="px-6 py-4">ID</th>
          <th class="px-6 py-4">Nama Murid</th>
          <th class="px-6 py-4">Kelas</th>
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
              <div class="w-9 h-9 rounded-full bg-[#3d8af5] flex items-center justify-center text-white font-bold text-xs">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
              <span class="text-sm font-medium text-[#171a1f]">{{ $student->name }}</span>
            </div>
          </td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">{{ $student->classroom?->name ?? '-' }}</td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">{{ $student->parent?->name ?? '-' }}</td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">{{ $student->join_date->translatedFormat('d M Y') }}</td>
          <td class="px-6 py-4"><span class="status-pill {{ $student->status === 'aktif' ? 'status-active' : 'status-cuti' }}">{{ ucfirst($student->status) }}</span></td>
          <td class="px-6 py-4 text-right">
            <div class="flex items-center justify-end gap-1">
              <button class="p-2 text-[#3d8af5] hover:bg-blue-50 rounded-lg"><iconify-icon icon="lucide:pencil" width="14"></iconify-icon></button>
              <form method="POST" action="{{ route('admin.murid.destroy', $student) }}" onsubmit="return confirm('Yakin hapus data murid ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="p-2 text-[#D92626] hover:bg-red-50 rounded-lg"><iconify-icon icon="lucide:trash-2" width="14"></iconify-icon></button>
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
<div id="modal-tambah-murid" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
  <div class="bg-white rounded-2xl p-8 w-full max-w-lg mx-4 shadow-xl">
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-xl font-bold text-[#171a1f]">Tambah Murid Baru</h3>
      <button onclick="document.getElementById('modal-tambah-murid').classList.add('hidden')" class="text-[#565d6d] hover:text-[#171a1f]">
        <iconify-icon icon="lucide:x" width="20"></iconify-icon>
      </button>
    </div>
    <form method="POST" action="{{ route('admin.murid.store') }}" class="space-y-4">
      @csrf
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">NIS</label>
          <input type="text" name="nis" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#3d8af5]/20 focus:outline-none" placeholder="BM006">
        </div>
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Nama Murid</label>
          <input type="text" name="name" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#3d8af5]/20 focus:outline-none" placeholder="Nama Lengkap">
        </div>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Kelas</label>
          <select name="classroom_id" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#3d8af5]/20 focus:outline-none">
            @foreach($classrooms as $classroom)
              <option value="{{ $classroom->id }}">{{ $classroom->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Tgl. Bergabung</label>
          <input type="date" name="join_date" required value="{{ date('Y-m-d') }}" class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#3d8af5]/20 focus:outline-none">
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-[#565d6d] mb-1">Status</label>
        <select name="status" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#3d8af5]/20 focus:outline-none">
          <option value="aktif">Aktif</option>
          <option value="cuti">Cuti</option>
        </select>
      </div>
      <hr class="border-[#dee1e6]">
      <p class="text-sm font-semibold text-[#171a1f]">Data Wali Murid (Opsional)</p>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Nama Wali</label>
          <input type="text" name="parent_name" class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#3d8af5]/20 focus:outline-none" placeholder="Nama Wali">
        </div>
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Email Wali</label>
          <input type="email" name="parent_email" class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#3d8af5]/20 focus:outline-none" placeholder="email@wali.com">
        </div>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="document.getElementById('modal-tambah-murid').classList.add('hidden')" class="flex-1 py-2.5 border border-[#dee1e6] rounded-xl text-sm font-medium text-[#565d6d] hover:bg-gray-50">Batal</button>
        <button type="submit" class="flex-1 py-2.5 bg-[#3d8af5] text-white rounded-xl text-sm font-medium hover:bg-blue-600">Simpan</button>
      </div>
    </form>
  </div>
</div>
@endsection
