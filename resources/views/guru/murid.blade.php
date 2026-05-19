@extends('layouts.guru')

@section('title', 'Daftar Murid Bimbingan - E-Rapor BiMBA')
@section('page-title', 'Daftar Murid Bimbingan')

@section('content')
<!-- Page Title -->
<div class="mb-8">
  <h1 class="text-2xl lg:text-3xl font-bold tracking-tight">Daftar Murid Bimbingan</h1>
  <p class="text-[#565d6d] mt-1 font-roboto">Lihat daftar murid yang menjadi bimbingan Anda.</p>
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
    <p class="mt-4 text-xs text-[#565d6d] font-roboto">Murid bimbingan Anda</p>
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
        <p class="text-sm font-medium text-[#565d6d]">Lainnya</p>
        <h3 class="text-3xl font-bold mt-2 text-[#171a1f]">{{ $muridLulus + $muridKeluar }}</h3>
      </div>
      <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center justify-center">
        <iconify-icon icon="lucide:archive" width="24" class="text-[#565d6d]"></iconify-icon>
      </div>
    </div>
    <p class="mt-4 text-xs text-[#565d6d] font-roboto">Lulus / Keluar / Cuti</p>
  </div>
</div>

<!-- Table Section -->
<div class="bg-white rounded-2xl border border-[#dee1e6] overflow-hidden main-shadow">
  <div class="p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-[#dee1e6]">
    <h2 class="text-xl font-semibold tracking-tight">Daftar Murid Bimbingan</h2>
    <div class="flex flex-wrap items-center gap-3">
      <form method="GET" action="{{ route('guru.murid') }}" class="relative w-full sm:w-64">
        <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-[#565d6d]">
          <iconify-icon icon="lucide:search" width="16"></iconify-icon>
        </div>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIS..." class="w-full pl-10 pr-4 py-2 bg-white border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20">
      </form>
      <select name="status" onchange="window.location.href='{{ route('guru.murid') }}?status=' + this.value" class="px-4 py-2 bg-white border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20">
        <option value="">Semua Status</option>
        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
        <option value="lulus" {{ request('status') === 'lulus' ? 'selected' : '' }}>Lulus</option>
        <option value="keluar" {{ request('status') === 'keluar' ? 'selected' : '' }}>Keluar</option>
        <option value="cuti" {{ request('status') === 'cuti' ? 'selected' : '' }}>Cuti</option>
      </select>
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-[#f3f4f6]/30 text-[#565d6d] text-sm font-semibold border-b border-[#dee1e6]">
          <th class="px-6 py-4">NIS</th>
          <th class="px-6 py-4">Nama Murid</th>
          <th class="px-6 py-4">Tahapan</th>
          <th class="px-6 py-4">Wali Murid</th>
          <th class="px-6 py-4">Tgl. Bergabung</th>
          <th class="px-6 py-4">Status</th>
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
          <td class="px-6 py-4">
            <span class="status-pill {{ $student->status === 'aktif' ? 'status-active' : ($student->status === 'lulus' ? 'status-lulus' : ($student->status === 'cuti' ? 'status-cuti' : 'status-keluar')) }}">
              {{ ucfirst($student->status) }}
            </span>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="px-6 py-8 text-center text-sm text-[#565d6d]">Anda belum memiliki murid bimbingan.</td>
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

<!-- Info Box -->
<div class="mt-8 bg-[#EFF6FF] border border-[#BFDBFE] rounded-2xl p-6">
  <div class="flex gap-4">
    <div class="w-12 h-12 bg-[#BFDBFE]/50 rounded-2xl flex-shrink-0 flex items-center justify-center">
      <iconify-icon icon="lucide:info" width="22" class="text-[#0284C7]"></iconify-icon>
    </div>
    <div>
      <h4 class="text-base font-semibold text-[#0C2D6B] mb-1">Catatan</h4>
      <p class="text-sm text-[#0C2D6B]/80 leading-relaxed">
        Daftar murid di atas adalah murid yang menjadi bimbingan Anda. Anda dapat melihat informasi kontak wali murid untuk memudahkan komunikasi tentang perkembangan belajar anak.
      </p>
    </div>
  </div>
</div>

<style>
  .status-pill {
    @apply inline-block px-3 py-1.5 text-xs font-medium rounded-lg;
  }
  .status-pill.status-active {
    @apply bg-[#DCFAE6] text-[#166534];
  }
  .status-pill.status-lulus {
    @apply bg-[#FEE2E2] text-[#991B1B];
  }
  .status-pill.status-keluar {
    @apply bg-[#FEF3C7] text-[#92400E];
  }
  .status-pill.status-cuti {
    @apply bg-[#E0E7FF] text-[#3730A3];
  }
</style>
@endsection
