@extends('layouts.admin')

@section('title', 'Dashboard - E-Rapor BiMBA AIUEO')
@section('page-title', 'Dashboard')

@section('content')
<!-- Welcome Section -->
<section class="mb-8">
  <h1 class="text-2xl lg:text-3xl font-bold text-[#171a1f] tracking-tight">Ringkasan Dashboard</h1>
  <p class="text-[#565d6d] mt-2 font-roboto">Selamat datang kembali, Admin Pusat. Pantau statistik institusi Anda hari ini.</p>
</section>

<!-- Stats Grid -->
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
  <div class="bg-white p-6 rounded-xl main-shadow border border-gray-50">
    <div class="flex justify-between items-start mb-6">
      <div class="w-10 h-10 bg-[#3d8af5]/10 rounded-lg flex items-center justify-center">
        <iconify-icon icon="lucide:users" width="22" class="text-[#3d8af5]"></iconify-icon>
      </div>
      <div class="flex items-center gap-1 text-[#16a34a] text-xs font-medium font-roboto">
        <iconify-icon icon="lucide:trending-up" width="12"></iconify-icon>
        +12%
      </div>
    </div>
    <p class="text-sm font-medium text-[#565d6d] mb-1">Total Murid</p>
    <h3 class="text-3xl font-bold text-[#171a1f] mb-2">1,248</h3>
    <p class="text-xs text-[#565d6d] font-roboto">+48 murid baru bulan ini</p>
  </div>

  <div class="bg-white p-6 rounded-xl main-shadow border border-gray-50">
    <div class="flex justify-between items-start mb-6">
      <div class="w-10 h-10 bg-[#63e98f]/10 rounded-lg flex items-center justify-center">
        <iconify-icon icon="lucide:user-check" width="22" class="text-[#16a34a]"></iconify-icon>
      </div>
    </div>
    <p class="text-sm font-medium text-[#565d6d] mb-1">Total Guru</p>
    <h3 class="text-3xl font-bold text-[#171a1f] mb-2">56</h3>
    <p class="text-xs text-[#565d6d] font-roboto">8 guru sedang bertugas</p>
  </div>

  <div class="bg-white p-6 rounded-xl main-shadow border border-gray-50">
    <div class="flex justify-between items-start mb-6">
      <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
        <iconify-icon icon="lucide:school" width="22" class="text-[#171a1f]"></iconify-icon>
      </div>
    </div>
    <p class="text-sm font-medium text-[#565d6d] mb-1">Total Kelas</p>
    <h3 class="text-3xl font-bold text-[#171a1f] mb-2">24</h3>
    <p class="text-xs text-[#565d6d] font-roboto">Aktif di 3 cabang utama</p>
  </div>

  <div class="bg-white p-6 rounded-xl main-shadow border border-gray-50">
    <div class="flex justify-between items-start mb-6">
      <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
        <iconify-icon icon="lucide:star" width="22" class="text-[#171a1f]"></iconify-icon>
      </div>
      <div class="flex items-center gap-1 text-[#16a34a] text-xs font-medium font-roboto">
        <iconify-icon icon="lucide:trending-up" width="12"></iconify-icon>
        +5%
      </div>
    </div>
    <p class="text-sm font-medium text-[#565d6d] mb-1">Pencapaian Level</p>
    <h3 class="text-3xl font-bold text-[#171a1f] mb-2">88%</h3>
    <p class="text-xs text-[#565d6d] font-roboto">Kenaikan level rata-rata</p>
  </div>
</section>

<!-- Manajemen Data Murid Table -->
<section class="bg-white rounded-2xl main-shadow border border-gray-50 overflow-hidden mb-10">
  <div class="p-6 bg-[#f3f4f6]/5 border-b border-[#dee1e6] flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
      <h2 class="text-xl font-bold text-[#171a1f]">Manajemen Data Murid</h2>
      <p class="text-sm text-[#565d6d] font-roboto mt-1">Daftar seluruh murid yang terdaftar di sistem pusat.</p>
    </div>
    <div class="flex items-center gap-3">
      <div class="relative flex-1 md:w-64">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#565d6d]">
          <iconify-icon icon="lucide:search" width="16"></iconify-icon>
        </span>
        <input type="text" placeholder="Cari nama atau ID..." class="w-full pl-10 pr-4 py-2 bg-white border border-[#dee1e6] rounded-lg text-sm font-roboto focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20">
      </div>
      <a href="{{ route('admin.murid') }}" class="bg-[#3d8af5] text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 hover:bg-[#3479d9]">
        <iconify-icon icon="lucide:plus" width="16"></iconify-icon>
        Tambah Murid
      </a>
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead class="bg-[#f3f4f6]/30 border-b border-[#dee1e6]">
        <tr>
          <th class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">ID</th>
          <th class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">Nama Murid</th>
          <th class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">Kelas</th>
          <th class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">Wali Murid</th>
          <th class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">Tgl. Bergabung</th>
          <th class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">Status</th>
          <th class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#dee1e6]">
        <tr class="table-row-hover">
          <td class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">M001</td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <img src="{{ asset('assets/IMG_14.webp') }}" class="w-8 h-8 rounded-full object-cover" alt="Aisyah">
              <span class="text-sm font-semibold text-[#171a1f] font-roboto">Aisyah Putri</span>
            </div>
          </td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">Level 1 - A</td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">Bpk. Budi</td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">12 Jan 2024</td>
          <td class="px-6 py-4"><span class="status-pill status-active">Aktif</span></td>
          <td class="px-6 py-4 text-right">
            <div class="flex items-center justify-end gap-2">
              <button class="p-1.5 text-[#565d6d] hover:bg-gray-100 rounded" title="Edit"><iconify-icon icon="lucide:pencil" width="14"></iconify-icon></button>
              <button class="p-1.5 text-red-400 hover:bg-red-50 rounded" title="Hapus"><iconify-icon icon="lucide:trash-2" width="14"></iconify-icon></button>
            </div>
          </td>
        </tr>
        <tr class="table-row-hover">
          <td class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">M002</td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <img src="{{ asset('assets/IMG_18.webp') }}" class="w-8 h-8 rounded-full object-cover" alt="Bima">
              <span class="text-sm font-semibold text-[#171a1f] font-roboto">Bima Satria</span>
            </div>
          </td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">Level 2 - B</td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">Ibu Siti</td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">05 Feb 2024</td>
          <td class="px-6 py-4"><span class="status-pill status-active">Aktif</span></td>
          <td class="px-6 py-4 text-right">
            <div class="flex items-center justify-end gap-2">
              <button class="p-1.5 text-[#565d6d] hover:bg-gray-100 rounded" title="Edit"><iconify-icon icon="lucide:pencil" width="14"></iconify-icon></button>
              <button class="p-1.5 text-red-400 hover:bg-red-50 rounded" title="Hapus"><iconify-icon icon="lucide:trash-2" width="14"></iconify-icon></button>
            </div>
          </td>
        </tr>
        <tr class="table-row-hover">
          <td class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">M003</td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <img src="{{ asset('assets/IMG_19.webp') }}" class="w-8 h-8 rounded-full object-cover" alt="Citra">
              <span class="text-sm font-semibold text-[#171a1f] font-roboto">Citra Kirana</span>
            </div>
          </td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">Level 1 - C</td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">Bpk. Agus</td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">20 Des 2023</td>
          <td class="px-6 py-4"><span class="status-pill status-cuti">Cuti</span></td>
          <td class="px-6 py-4 text-right">
            <div class="flex items-center justify-end gap-2">
              <button class="p-1.5 text-[#565d6d] hover:bg-gray-100 rounded" title="Edit"><iconify-icon icon="lucide:pencil" width="14"></iconify-icon></button>
              <button class="p-1.5 text-red-400 hover:bg-red-50 rounded" title="Hapus"><iconify-icon icon="lucide:trash-2" width="14"></iconify-icon></button>
            </div>
          </td>
        </tr>
        <tr class="table-row-hover">
          <td class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">M004</td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <img src="{{ asset('assets/IMG_20.webp') }}" class="w-8 h-8 rounded-full object-cover" alt="Dedi">
              <span class="text-sm font-semibold text-[#171a1f] font-roboto">Dedi Kurniawan</span>
            </div>
          </td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">Level 3 - A</td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">Ibu Lani</td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">15 Feb 2024</td>
          <td class="px-6 py-4"><span class="status-pill status-active">Aktif</span></td>
          <td class="px-6 py-4 text-right">
            <div class="flex items-center justify-end gap-2">
              <button class="p-1.5 text-[#565d6d] hover:bg-gray-100 rounded" title="Edit"><iconify-icon icon="lucide:pencil" width="14"></iconify-icon></button>
              <button class="p-1.5 text-red-400 hover:bg-red-50 rounded" title="Hapus"><iconify-icon icon="lucide:trash-2" width="14"></iconify-icon></button>
            </div>
          </td>
        </tr>
        <tr class="table-row-hover">
          <td class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">M005</td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <img src="{{ asset('assets/IMG_21.webp') }}" class="w-8 h-8 rounded-full object-cover" alt="Eka">
              <span class="text-sm font-semibold text-[#171a1f] font-roboto">Eka Prasetya</span>
            </div>
          </td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">Level 4 - B</td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">Bpk. Toto</td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">01 Mar 2024</td>
          <td class="px-6 py-4"><span class="status-pill status-active">Aktif</span></td>
          <td class="px-6 py-4 text-right">
            <div class="flex items-center justify-end gap-2">
              <button class="p-1.5 text-[#565d6d] hover:bg-gray-100 rounded" title="Edit"><iconify-icon icon="lucide:pencil" width="14"></iconify-icon></button>
              <button class="p-1.5 text-red-400 hover:bg-red-50 rounded" title="Hapus"><iconify-icon icon="lucide:trash-2" width="14"></iconify-icon></button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="p-6 bg-[#f3f4f6]/5 border-t border-[#dee1e6] flex flex-col sm:flex-row items-center justify-between gap-4">
    <p class="text-sm text-[#565d6d] font-roboto">Menampilkan 5 dari 1,248 data murid</p>
    <div class="flex items-center gap-2">
      <button class="px-3 py-1.5 border border-[#dee1e6] rounded-lg text-sm font-medium text-[#565d6d] hover:bg-gray-50">Sebelumnya</button>
      <button class="w-8 h-8 bg-[#3d8af5] text-white rounded-lg text-sm font-medium">1</button>
      <button class="w-8 h-8 border border-[#dee1e6] rounded-lg text-sm font-medium text-[#565d6d] hover:bg-gray-50">2</button>
      <button class="px-3 py-1.5 border border-[#dee1e6] rounded-lg text-sm font-medium text-[#565d6d] hover:bg-gray-50">Selanjutnya</button>
    </div>
  </div>
</section>

<!-- Bottom Grid: Guru & Aktivitas -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

  <!-- Data Tenaga Pengajar -->
  <div class="lg:col-span-2 bg-white rounded-2xl main-shadow border border-gray-50 overflow-hidden">
    <div class="p-6 border-b border-[#dee1e6] flex items-center justify-between">
      <div>
        <h2 class="text-lg font-bold text-[#171a1f]">Data Tenaga Pengajar</h2>
        <p class="text-sm text-[#565d6d] font-roboto mt-1">Overview singkat status guru pengajar saat ini.</p>
      </div>
      <a href="{{ route('admin.guru') }}" class="px-4 py-2 border border-[#3d8af5]/20 text-[#3d8af5] rounded-lg text-sm font-medium hover:bg-[#3d8af5]/5">Lihat Semua Guru</a>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead class="bg-[#f3f4f6]/30">
          <tr>
            <th class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">Nama Guru</th>
            <th class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">Spesialisasi</th>
            <th class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">Beban Kelas</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#dee1e6]">
          <tr class="table-row-hover">
            <td class="px-6 py-4">
              <p class="text-sm font-medium text-[#171a1f] font-roboto">Ibu Ratna Sari</p>
              <p class="text-xs text-[#565d6d] font-roboto">ratna@bimba.com</p>
            </td>
            <td class="px-6 py-4">
              <span class="px-3 py-1 bg-[#3d8af5]/5 border border-[#3d8af5]/20 text-[#3d8af5] rounded-full text-[11px] font-roboto">Bahasa & Baca</span>
            </td>
            <td class="px-6 py-4 text-sm font-medium text-[#171a1f] font-roboto">3 Kelas</td>
          </tr>
          <tr class="table-row-hover">
            <td class="px-6 py-4">
              <p class="text-sm font-medium text-[#171a1f] font-roboto">Bpk. Andi Wijaya</p>
              <p class="text-xs text-[#565d6d] font-roboto">andi@bimba.com</p>
            </td>
            <td class="px-6 py-4">
              <span class="px-3 py-1 bg-[#3d8af5]/5 border border-[#3d8af5]/20 text-[#3d8af5] rounded-full text-[11px] font-roboto">Matematika Dasar</span>
            </td>
            <td class="px-6 py-4 text-sm font-medium text-[#171a1f] font-roboto">2 Kelas</td>
          </tr>
          <tr class="table-row-hover">
            <td class="px-6 py-4">
              <p class="text-sm font-medium text-[#171a1f] font-roboto">Ibu Maya Indah</p>
              <p class="text-xs text-[#565d6d] font-roboto">maya@bimba.com</p>
            </td>
            <td class="px-6 py-4">
              <span class="px-3 py-1 bg-[#3d8af5]/5 border border-[#3d8af5]/20 text-[#3d8af5] rounded-full text-[11px] font-roboto">Menulis & Seni</span>
            </td>
            <td class="px-6 py-4 text-sm font-medium text-[#171a1f] font-roboto">4 Kelas</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Right Column -->
  <div class="space-y-8">
    <!-- Aktivitas Terkini -->
    <div class="bg-white p-6 rounded-2xl main-shadow border border-gray-50">
      <h2 class="text-lg font-bold text-[#171a1f] mb-1">Aktivitas Terkini</h2>
      <p class="text-sm text-[#565d6d] font-roboto mb-6">Log aktivitas dalam 24 jam terakhir.</p>
      <div class="space-y-6 relative">
        <div class="absolute left-[5px] top-2 bottom-2 w-[1px] bg-[#dee1e6]"></div>
        <div class="relative pl-8">
          <div class="absolute left-0 top-1.5 w-3 h-3 bg-white border-2 border-[#3d8af5] rounded-full z-10"></div>
          <p class="text-sm font-medium text-[#171a1f] font-roboto">Input Nilai Level 2 - B</p>
          <p class="text-xs text-[#565d6d] font-roboto mt-1">Ibu Ratna • 2 menit lalu</p>
        </div>
        <div class="relative pl-8">
          <div class="absolute left-0 top-1.5 w-3 h-3 bg-white border-2 border-[#63e98f] rounded-full z-10"></div>
          <p class="text-sm font-medium text-[#171a1f] font-roboto">Tambah Murid Baru (M005)</p>
          <p class="text-xs text-[#565d6d] font-roboto mt-1">Bpk. Andi • 45 menit lalu</p>
        </div>
        <div class="relative pl-8">
          <div class="absolute left-0 top-1.5 w-3 h-3 bg-white border-2 border-[#dee1e6] rounded-full z-10"></div>
          <p class="text-sm font-medium text-[#171a1f] font-roboto">Update Pengaturan Jadwal</p>
          <p class="text-xs text-[#565d6d] font-roboto mt-1">Sistem • 3 jam lalu</p>
        </div>
      </div>
    </div>

    <!-- Pusat Bantuan -->
    <div class="bg-[#3d8af5] p-6 rounded-2xl text-white relative overflow-hidden shadow-[0px_4px_7px_0px_rgba(61,138,245,0.2)]">
      <div class="absolute -right-4 -top-4 opacity-10 rotate-12">
        <iconify-icon icon="lucide:graduation-cap" width="112" class="text-white"></iconify-icon>
      </div>
      <h2 class="text-lg font-bold mb-2 relative z-10">Pusat Bantuan BiMBA</h2>
      <p class="text-sm text-white/80 font-roboto mb-6 relative z-10 leading-relaxed">Butuh bantuan dalam mengelola data atau sistem rapor? Tim teknis kami siap membantu 24/7.</p>
      <button class="w-full py-2.5 bg-white text-[#1e2128] rounded-lg text-sm font-semibold hover:bg-gray-100 relative z-10">Hubungi Support</button>
    </div>
  </div>
</div>
@endsection
