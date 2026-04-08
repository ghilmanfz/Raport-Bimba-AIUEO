@extends('layouts.admin')

@section('title', 'Manajemen Data Guru - E-Rapor BiMBA')
@section('page-title', 'Manajemen Data Guru')

@section('content')
<!-- Page Title & Actions -->
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-8">
  <div>
    <h1 class="text-2xl lg:text-3xl font-bold tracking-tight">Manajemen Data Guru/Motivator</h1>
    <p class="text-[#565d6d] mt-1 font-roboto">Kelola informasi motivator, beban kelas, dan spesialisasi pengajaran.</p>
  </div>
  <div class="flex flex-wrap items-center gap-3">
    <button class="flex items-center gap-2 px-4 py-2 bg-white border border-[#dee1e6] rounded-xl text-sm font-medium text-[#171a1f] hover:bg-gray-50">
      <iconify-icon icon="lucide:upload" width="16"></iconify-icon>
      Impor
    </button>
    <button class="flex items-center gap-2 px-4 py-2 bg-white border border-[#dee1e6] rounded-xl text-sm font-medium text-[#171a1f] hover:bg-gray-50">
      <iconify-icon icon="lucide:download" width="16"></iconify-icon>
      Ekspor
    </button>
    <button class="flex items-center gap-2 px-4 py-2 bg-[#3d8af5] text-white rounded-xl text-sm font-medium hover:bg-blue-600">
      <iconify-icon icon="lucide:plus" width="16"></iconify-icon>
      Tambah Guru
    </button>
  </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
  <div class="bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow flex items-center justify-between">
    <div>
      <p class="text-sm font-medium text-[#565d6d]">Total Guru</p>
      <h3 class="text-3xl font-bold mt-1 text-[#171a1f]">42</h3>
    </div>
    <div class="w-14 h-14 bg-[#F1F6FE] rounded-2xl flex items-center justify-center">
      <iconify-icon icon="lucide:users" width="24" class="text-[#3d8af5]"></iconify-icon>
    </div>
  </div>
  <div class="bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow flex items-center justify-between">
    <div>
      <p class="text-sm font-medium text-[#565d6d]">Guru Aktif</p>
      <h3 class="text-3xl font-bold mt-1 text-[#171a1f]">38</h3>
    </div>
    <div class="w-14 h-14 bg-[#DCFAE6] rounded-2xl flex items-center justify-center">
      <iconify-icon icon="lucide:user-check" width="24" class="text-[#16a34a]"></iconify-icon>
    </div>
  </div>
  <div class="bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow flex items-center justify-between">
    <div>
      <p class="text-sm font-medium text-[#565d6d]">Spesialisasi Baca</p>
      <h3 class="text-3xl font-bold mt-1 text-[#171a1f]">18</h3>
    </div>
    <div class="w-14 h-14 bg-[#FCF0E3] rounded-2xl flex items-center justify-center">
      <iconify-icon icon="lucide:book-open" width="24" class="text-orange-500"></iconify-icon>
    </div>
  </div>
  <div class="bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow flex items-center justify-between">
    <div>
      <p class="text-sm font-medium text-[#565d6d]">Beban Rata-rata</p>
      <h3 class="text-3xl font-bold mt-1 text-[#171a1f]">3.5</h3>
    </div>
    <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center">
      <iconify-icon icon="lucide:calculator" width="24" class="text-[#565d6d]"></iconify-icon>
    </div>
  </div>
</div>

<!-- Table Section -->
<div class="bg-white rounded-2xl border border-[#dee1e6] overflow-hidden main-shadow mb-8">
  <div class="p-4 border-b border-[#dee1e6] flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex flex-col sm:flex-row items-center gap-3">
      <div class="relative w-full sm:w-80">
        <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-[#565d6d]">
          <iconify-icon icon="lucide:search" width="16"></iconify-icon>
        </div>
        <input type="text" placeholder="Cari berdasarkan nama atau ID..." class="w-full pl-10 pr-4 py-2 bg-[#fafafb] border border-transparent rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20">
      </div>
      <button class="flex items-center gap-2 px-4 py-2 border border-[#dee1e6] rounded-xl text-sm font-medium text-[#171a1f] hover:bg-gray-50">
        <iconify-icon icon="lucide:filter" width="16"></iconify-icon>
        Spesialisasi
      </button>
    </div>
    <p class="text-sm text-[#565d6d] font-roboto">Menampilkan <span class="font-bold">5</span> dari <span class="font-bold">42</span> guru</p>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead class="bg-[#fafafb] border-b border-[#dee1e6]">
        <tr class="text-[#565d6d] text-sm font-semibold">
          <th class="px-6 py-4">ID</th>
          <th class="px-6 py-4">Nama Guru</th>
          <th class="px-6 py-4">Spesialisasi</th>
          <th class="px-6 py-4">Email</th>
          <th class="px-6 py-4 text-center">Beban Kelas</th>
          <th class="px-6 py-4">Status</th>
          <th class="px-6 py-4 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#dee1e6]">
        <tr class="hover:bg-gray-50/50">
          <td class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">T-001</td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <img src="{{ asset('assets/IMG_16.webp') }}" class="w-10 h-10 rounded-full object-cover" alt="Siti">
              <span class="text-sm font-semibold text-[#171a1f]">Siti Aminah</span>
            </div>
          </td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-2 text-sm text-[#565d6d]">
              <iconify-icon icon="lucide:book-open" width="14" class="text-[#3d8af5]"></iconify-icon>
              Baca-Tulis
            </div>
          </td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">siti.a@bimba.id</td>
          <td class="px-6 py-4 text-center">
            <span class="px-2 py-1 bg-[#3d8af5]/10 text-[#3d8af5] text-xs font-bold rounded-md">4 Kelas</span>
          </td>
          <td class="px-6 py-4"><span class="status-pill status-active">Aktif</span></td>
          <td class="px-6 py-4 text-right">
            <button class="p-2 text-[#565d6d] hover:bg-gray-100 rounded-lg"><iconify-icon icon="lucide:more-horizontal" width="16"></iconify-icon></button>
          </td>
        </tr>
        <tr class="hover:bg-gray-50/50 bg-[#fafafb]/30">
          <td class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">T-002</td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <img src="{{ asset('assets/IMG_19.webp') }}" class="w-10 h-10 rounded-full object-cover" alt="Budi">
              <span class="text-sm font-semibold text-[#171a1f]">Budi Santoso</span>
            </div>
          </td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-2 text-sm text-[#565d6d]">
              <iconify-icon icon="lucide:calculator" width="14" class="text-[#16a34a]"></iconify-icon>
              Matematika
            </div>
          </td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">budi.s@bimba.id</td>
          <td class="px-6 py-4 text-center">
            <span class="px-2 py-1 bg-[#3d8af5]/10 text-[#3d8af5] text-xs font-bold rounded-md">3 Kelas</span>
          </td>
          <td class="px-6 py-4"><span class="status-pill status-active">Aktif</span></td>
          <td class="px-6 py-4 text-right">
            <button class="p-2 text-[#565d6d] hover:bg-gray-100 rounded-lg"><iconify-icon icon="lucide:more-horizontal" width="16"></iconify-icon></button>
          </td>
        </tr>
        <tr class="hover:bg-gray-50/50">
          <td class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">T-003</td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <img src="{{ asset('assets/IMG_20.webp') }}" class="w-10 h-10 rounded-full object-cover" alt="Laras">
              <span class="text-sm font-semibold text-[#171a1f]">Larasati Putri</span>
            </div>
          </td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-2 text-sm text-[#565d6d]">
              <iconify-icon icon="lucide:globe" width="14" class="text-purple-500"></iconify-icon>
              Bahasa Inggris
            </div>
          </td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">laras.p@bimba.id</td>
          <td class="px-6 py-4 text-center">
            <span class="px-2 py-1 bg-[#D92626]/10 text-[#D92626] text-xs font-bold rounded-md">5 Kelas</span>
          </td>
          <td class="px-6 py-4"><span class="status-pill status-cuti">Cuti</span></td>
          <td class="px-6 py-4 text-right">
            <button class="p-2 text-[#565d6d] hover:bg-gray-100 rounded-lg"><iconify-icon icon="lucide:more-horizontal" width="16"></iconify-icon></button>
          </td>
        </tr>
        <tr class="hover:bg-gray-50/50 bg-[#fafafb]/30">
          <td class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">T-004</td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <img src="{{ asset('assets/IMG_21.webp') }}" class="w-10 h-10 rounded-full object-cover" alt="Andi">
              <span class="text-sm font-semibold text-[#171a1f]">Andi Wijaya</span>
            </div>
          </td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-2 text-sm text-[#565d6d]">
              <iconify-icon icon="lucide:palette" width="14" class="text-pink-500"></iconify-icon>
              Seni & Kreasi
            </div>
          </td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">andi.w@bimba.id</td>
          <td class="px-6 py-4 text-center">
            <span class="px-2 py-1 bg-[#3d8af5]/10 text-[#3d8af5] text-xs font-bold rounded-md">2 Kelas</span>
          </td>
          <td class="px-6 py-4"><span class="status-pill status-active">Aktif</span></td>
          <td class="px-6 py-4 text-right">
            <button class="p-2 text-[#565d6d] hover:bg-gray-100 rounded-lg"><iconify-icon icon="lucide:more-horizontal" width="16"></iconify-icon></button>
          </td>
        </tr>
        <tr class="hover:bg-gray-50/50">
          <td class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">T-005</td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <img src="{{ asset('assets/IMG_22.webp') }}" class="w-10 h-10 rounded-full object-cover" alt="Dewi">
              <span class="text-sm font-semibold text-[#171a1f]">Dewi Lestari</span>
            </div>
          </td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-2 text-sm text-[#565d6d]">
              <iconify-icon icon="lucide:book-open" width="14" class="text-[#3d8af5]"></iconify-icon>
              Baca-Tulis
            </div>
          </td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">dewi.l@bimba.id</td>
          <td class="px-6 py-4 text-center">
            <span class="px-2 py-1 bg-[#3d8af5]/10 text-[#3d8af5] text-xs font-bold rounded-md">4 Kelas</span>
          </td>
          <td class="px-6 py-4"><span class="status-pill status-active">Aktif</span></td>
          <td class="px-6 py-4 text-right">
            <button class="p-2 text-[#565d6d] hover:bg-gray-100 rounded-lg"><iconify-icon icon="lucide:more-horizontal" width="16"></iconify-icon></button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div class="p-4 border-t border-[#dee1e6] flex flex-col sm:flex-row items-center justify-between gap-4">
    <p class="text-sm text-[#565d6d] font-roboto">Menampilkan <span class="font-bold">1-5</span> dari <span class="font-bold">42</span> Motivator</p>
    <div class="flex items-center gap-2">
      <button class="w-8 h-8 flex items-center justify-center border border-[#dee1e6] rounded-lg opacity-50 cursor-not-allowed text-[#565d6d]"><iconify-icon icon="lucide:chevron-left" width="14"></iconify-icon></button>
      <button class="w-8 h-8 bg-[#3d8af5]/20 text-[#3d8af5] font-bold rounded-lg text-sm">1</button>
      <button class="w-8 h-8 border border-[#dee1e6] text-[#565d6d] hover:bg-gray-50 rounded-lg text-sm">2</button>
      <button class="w-8 h-8 border border-[#dee1e6] text-[#565d6d] hover:bg-gray-50 rounded-lg text-sm">3</button>
      <span class="text-[#565d6d] px-1">...</span>
      <button class="w-8 h-8 border border-[#dee1e6] text-[#565d6d] hover:bg-gray-50 rounded-lg text-sm">8</button>
      <button class="w-8 h-8 flex items-center justify-center border border-[#dee1e6] rounded-lg hover:bg-gray-50 text-[#565d6d]"><iconify-icon icon="lucide:chevron-right" width="14"></iconify-icon></button>
    </div>
  </div>
</div>

<!-- Bottom Panels -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
  <div class="xl:col-span-2 bg-[#0D7FF2] p-6 lg:p-8 rounded-2xl main-shadow flex flex-col sm:flex-row gap-6">
    <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex-shrink-0 flex items-center justify-center">
      <iconify-icon icon="lucide:lightbulb" width="22" class="text-[#3d8af5]"></iconify-icon>
    </div>
    <div>
      <h4 class="text-lg font-bold text-white mb-2">Tips Manajemen Motivator</h4>
      <p class="text-white/80 text-sm leading-relaxed">
        Pastikan beban kelas setiap guru tidak melebihi 5 kelas per minggu untuk menjaga kualitas pengajaran BiMBA AIUEO yang optimal bagi anak-anak. Gunakan filter spesialisasi untuk mencari guru pengganti yang tepat.
      </p>
    </div>
  </div>
  <div class="bg-white p-6 rounded-2xl border border-[#dee1e6] main-shadow flex flex-col items-center text-center">
    <div class="w-12 h-12 bg-[#63e98f]/10 rounded-full flex items-center justify-center mb-4">
      <iconify-icon icon="lucide:help-circle" width="22" class="text-[#16a34a]"></iconify-icon>
    </div>
    <h4 class="text-base font-bold text-[#171a1f] mb-1">Butuh Bantuan?</h4>
    <p class="text-xs text-[#565d6d] mb-6">Hubungi tim IT Pusat untuk bantuan teknis.</p>
    <button class="w-full py-2 border border-[#63e98f] text-[#16a34a] text-sm font-medium rounded-xl hover:bg-[#63e98f]/5">
      Pusat Bantuan
    </button>
  </div>
</div>
@endsection
