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
    <button class="flex items-center gap-2 px-4 py-2 bg-[#3d8af5] text-white rounded-xl font-medium text-sm hover:bg-blue-600">
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
        <h3 class="text-3xl font-bold mt-2 text-[#171a1f]">128</h3>
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
        <h3 class="text-3xl font-bold mt-2 text-[#171a1f]">112</h3>
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
        <h3 class="text-3xl font-bold mt-2 text-[#171a1f]">16</h3>
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
      <div class="relative w-full sm:w-64">
        <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-[#565d6d]">
          <iconify-icon icon="lucide:search" width="16"></iconify-icon>
        </div>
        <input type="text" placeholder="Cari Nama atau ID..." class="w-full pl-10 pr-4 py-2 bg-white border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20">
      </div>
      <button class="flex items-center gap-2 px-4 py-2 border border-[#dee1e6] rounded-xl text-sm font-medium text-[#565d6d] hover:bg-gray-50">
        <iconify-icon icon="lucide:filter" width="16"></iconify-icon>
        Filter Kelas
      </button>
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
        <tr class="hover:bg-gray-50/50">
          <td class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">BM001</td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <img src="{{ asset('assets/IMG_14.webp') }}" class="w-9 h-9 rounded-full object-cover" alt="Aisyah">
              <span class="text-sm font-medium text-[#171a1f]">Aisyah Putri</span>
            </div>
          </td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">Level 1 - Persiapan</td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">Bpk. Budi</td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">12 Jan 2024</td>
          <td class="px-6 py-4"><span class="status-pill status-active">Aktif</span></td>
          <td class="px-6 py-4 text-right">
            <div class="flex items-center justify-end gap-1">
              <button class="p-2 text-[#3d8af5] hover:bg-blue-50 rounded-lg"><iconify-icon icon="lucide:pencil" width="14"></iconify-icon></button>
              <button class="p-2 text-[#D92626] hover:bg-red-50 rounded-lg"><iconify-icon icon="lucide:trash-2" width="14"></iconify-icon></button>
              <button class="p-2 text-[#565d6d] hover:bg-gray-100 rounded-lg"><iconify-icon icon="lucide:more-horizontal" width="14"></iconify-icon></button>
            </div>
          </td>
        </tr>
        <tr class="hover:bg-gray-50/50">
          <td class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">BM002</td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <img src="{{ asset('assets/IMG_18.webp') }}" class="w-9 h-9 rounded-full object-cover" alt="Bima">
              <span class="text-sm font-medium text-[#171a1f]">Bima Satria</span>
            </div>
          </td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">Level 2 - Dasar</td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">Ibu Siti</td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">05 Feb 2024</td>
          <td class="px-6 py-4"><span class="status-pill status-active">Aktif</span></td>
          <td class="px-6 py-4 text-right">
            <div class="flex items-center justify-end gap-1">
              <button class="p-2 text-[#3d8af5] hover:bg-blue-50 rounded-lg"><iconify-icon icon="lucide:pencil" width="14"></iconify-icon></button>
              <button class="p-2 text-[#D92626] hover:bg-red-50 rounded-lg"><iconify-icon icon="lucide:trash-2" width="14"></iconify-icon></button>
              <button class="p-2 text-[#565d6d] hover:bg-gray-100 rounded-lg"><iconify-icon icon="lucide:more-horizontal" width="14"></iconify-icon></button>
            </div>
          </td>
        </tr>
        <tr class="hover:bg-gray-50/50">
          <td class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">BM003</td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <img src="{{ asset('assets/IMG_19.webp') }}" class="w-9 h-9 rounded-full object-cover" alt="Citra">
              <span class="text-sm font-medium text-[#171a1f]">Citra Kirana</span>
            </div>
          </td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">Level 1 - Persiapan</td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">Bpk. Agus</td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">20 Feb 2024</td>
          <td class="px-6 py-4"><span class="status-pill status-cuti">Cuti</span></td>
          <td class="px-6 py-4 text-right">
            <div class="flex items-center justify-end gap-1">
              <button class="p-2 text-[#3d8af5] hover:bg-blue-50 rounded-lg"><iconify-icon icon="lucide:pencil" width="14"></iconify-icon></button>
              <button class="p-2 text-[#D92626] hover:bg-red-50 rounded-lg"><iconify-icon icon="lucide:trash-2" width="14"></iconify-icon></button>
              <button class="p-2 text-[#565d6d] hover:bg-gray-100 rounded-lg"><iconify-icon icon="lucide:more-horizontal" width="14"></iconify-icon></button>
            </div>
          </td>
        </tr>
        <tr class="hover:bg-gray-50/50">
          <td class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">BM004</td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <img src="{{ asset('assets/IMG_20.webp') }}" class="w-9 h-9 rounded-full object-cover" alt="Dedi">
              <span class="text-sm font-medium text-[#171a1f]">Dedi Kurniawan</span>
            </div>
          </td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">Level 3 - Lanjutan</td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">Ibu Lani</td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">15 Mar 2024</td>
          <td class="px-6 py-4"><span class="status-pill status-active">Aktif</span></td>
          <td class="px-6 py-4 text-right">
            <div class="flex items-center justify-end gap-1">
              <button class="p-2 text-[#3d8af5] hover:bg-blue-50 rounded-lg"><iconify-icon icon="lucide:pencil" width="14"></iconify-icon></button>
              <button class="p-2 text-[#D92626] hover:bg-red-50 rounded-lg"><iconify-icon icon="lucide:trash-2" width="14"></iconify-icon></button>
              <button class="p-2 text-[#565d6d] hover:bg-gray-100 rounded-lg"><iconify-icon icon="lucide:more-horizontal" width="14"></iconify-icon></button>
            </div>
          </td>
        </tr>
        <tr class="hover:bg-gray-50/50">
          <td class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">BM005</td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <img src="{{ asset('assets/IMG_21.webp') }}" class="w-9 h-9 rounded-full object-cover" alt="Eka">
              <span class="text-sm font-medium text-[#171a1f]">Eka Prasetya</span>
            </div>
          </td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">Level 4 - Mahir</td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">Bpk. Toto</td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">01 Apr 2024</td>
          <td class="px-6 py-4"><span class="status-pill status-active">Aktif</span></td>
          <td class="px-6 py-4 text-right">
            <div class="flex items-center justify-end gap-1">
              <button class="p-2 text-[#3d8af5] hover:bg-blue-50 rounded-lg"><iconify-icon icon="lucide:pencil" width="14"></iconify-icon></button>
              <button class="p-2 text-[#D92626] hover:bg-red-50 rounded-lg"><iconify-icon icon="lucide:trash-2" width="14"></iconify-icon></button>
              <button class="p-2 text-[#565d6d] hover:bg-gray-100 rounded-lg"><iconify-icon icon="lucide:more-horizontal" width="14"></iconify-icon></button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div class="p-6 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-[#dee1e6]">
    <p class="text-sm text-[#565d6d] font-roboto">Menampilkan <span class="font-medium">1-5</span> dari <span class="font-medium">128</span> murid</p>
    <div class="flex items-center gap-2">
      <button class="px-3 py-1.5 border border-[#dee1e6] rounded-lg text-sm font-medium text-[#565d6d] hover:bg-gray-50 opacity-50 cursor-not-allowed">Sebelumnya</button>
      <button class="w-8 h-8 bg-[#3d8af5] text-white rounded-lg text-sm font-medium">1</button>
      <button class="w-8 h-8 border border-[#dee1e6] rounded-lg text-sm font-medium text-[#565d6d] hover:bg-gray-50">2</button>
      <button class="w-8 h-8 border border-[#dee1e6] rounded-lg text-sm font-medium text-[#565d6d] hover:bg-gray-50">3</button>
      <button class="px-3 py-1.5 border border-[#dee1e6] rounded-lg text-sm font-medium text-[#565d6d] hover:bg-gray-50">Selanjutnya</button>
    </div>
  </div>
</div>
@endsection
