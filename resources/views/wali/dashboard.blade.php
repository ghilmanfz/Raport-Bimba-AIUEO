@extends('layouts.wali')

@section('title', 'Dashboard - E-Rapor BiMBA AIUEO')
@section('page-title', 'Dashboard')

@section('content')
<!-- Hero Section -->
<section class="hero-gradient rounded-3xl p-6 lg:p-8 custom-shadow border border-[#3d8af51a] flex flex-col lg:flex-row items-center lg:justify-between gap-6 mb-8">
  <div class="flex flex-col lg:flex-row items-center gap-6">
    <div class="w-20 h-20 rounded-full overflow-hidden custom-shadow border-2 border-white flex-shrink-0">
      <div class="w-full h-full bg-[#f2bf8c] flex items-center justify-center text-white text-2xl font-black">AS</div>
    </div>
    <div class="text-center lg:text-left">
      <h1 class="text-2xl lg:text-3xl font-bold text-[#3d8af5] mb-3">Semangat Belajar, Ananda Salsabila! 👋</h1>
      <div class="flex flex-wrap justify-center lg:justify-start gap-3">
        <div class="flex items-center gap-2 px-3 py-1 bg-white/80 backdrop-blur-sm border border-[#3d8af533] rounded-full text-xs font-semibold">
          <iconify-icon icon="lucide:calendar" width="12" class="text-[#3d8af5]"></iconify-icon>
          Tahun Ajaran 2023/2024
        </div>
        <div class="flex items-center gap-2 px-3 py-1 bg-white/80 backdrop-blur-sm border border-[#3d8af533] rounded-full text-xs font-semibold">
          <iconify-icon icon="lucide:layers" width="12" class="text-[#3d8af5]"></iconify-icon>
          Level 2 - Unit Pondok Aren
        </div>
      </div>
    </div>
  </div>

  <div class="flex items-center gap-0 bg-white/50 rounded-2xl border border-[#3d8af50d] p-4">
    <div class="px-6 text-center border-r border-[#dee1e6]">
      <p class="text-[10px] uppercase tracking-wider font-bold text-[#565d6d] mb-1">Total Bintang</p>
      <div class="flex items-center justify-center gap-2">
        <iconify-icon icon="lucide:star" width="22" class="text-[#EAB308]"></iconify-icon>
        <span class="text-2xl font-black text-[#EAB308]">248</span>
      </div>
    </div>
    <div class="px-6 text-center">
      <p class="text-[10px] uppercase tracking-wider font-bold text-[#565d6d] mb-1">Peringkat</p>
      <div class="flex items-center justify-center gap-2">
        <iconify-icon icon="lucide:award" width="22" class="text-[#3d8af5]"></iconify-icon>
        <span class="text-2xl font-black text-[#3d8af5]">Top 5</span>
      </div>
    </div>
  </div>
</section>

<!-- Summary Title -->
<div class="flex items-center gap-2 mb-6">
  <iconify-icon icon="lucide:trending-up" width="20" class="text-[#3d8af5]"></iconify-icon>
  <h2 class="text-xl font-bold text-[#171a1f]">Ringkasan Progres Saat Ini</h2>
</div>

<!-- 3 Subject Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
  <!-- Membaca -->
  <div class="bg-white rounded-xl p-6 custom-shadow flex flex-col h-full">
    <div class="flex justify-between items-start mb-6">
      <div class="w-12 h-12 bg-[#DCFCE7] rounded-2xl flex items-center justify-center">
        <iconify-icon icon="lucide:book-open" width="24" class="text-[#22C55E]"></iconify-icon>
      </div>
      <span class="bg-gray-100 text-[#171a1f] text-[10px] font-bold px-3 py-1 rounded-full border border-gray-200">Status: T</span>
    </div>
    <h3 class="text-lg font-bold text-[#171a1f] mb-2">Membaca (Baca)</h3>
    <p class="text-sm text-[#565d6d] mb-6 flex-1">Anak sudah mampu membaca kalimat sederhana dengan lancar.</p>
    <div class="space-y-2 mb-6">
      <div class="flex justify-between text-xs font-semibold">
        <span>Progres Belajar</span>
        <span>95%</span>
      </div>
      <div class="w-full h-2 bg-[#f3f4f6] rounded-full overflow-hidden">
        <div class="h-full bg-[#3d8af5]" style="width: 95%"></div>
      </div>
    </div>
    <a href="{{ route('wali.rapor') }}" class="w-full flex items-center justify-center gap-2 text-[#3d8af5] text-sm font-medium hover:underline">
      Lihat Detail Laporan
      <iconify-icon icon="lucide:arrow-right" width="16"></iconify-icon>
    </a>
  </div>

  <!-- Menulis -->
  <div class="bg-white rounded-xl p-6 custom-shadow flex flex-col h-full">
    <div class="flex justify-between items-start mb-6">
      <div class="w-12 h-12 bg-[#DBEAFE] rounded-2xl flex items-center justify-center">
        <iconify-icon icon="lucide:pencil" width="24" class="text-[#3d8af5]"></iconify-icon>
      </div>
      <span class="bg-[#3d8af5] text-white text-[10px] font-bold px-3 py-1 rounded-full">Status: P</span>
    </div>
    <h3 class="text-lg font-bold text-[#171a1f] mb-2">Menulis (Tulis)</h3>
    <p class="text-sm text-[#565d6d] mb-6 flex-1">Anak memahami struktur huruf dan mulai merangkai kata.</p>
    <div class="space-y-2 mb-6">
      <div class="flex justify-between text-xs font-semibold">
        <span>Progres Belajar</span>
        <span>80%</span>
      </div>
      <div class="w-full h-2 bg-[#f3f4f6] rounded-full overflow-hidden">
        <div class="h-full bg-[#3d8af5]" style="width: 80%"></div>
      </div>
    </div>
    <a href="{{ route('wali.rapor') }}" class="w-full flex items-center justify-center gap-2 text-[#3d8af5] text-sm font-medium hover:underline">
      Lihat Detail Laporan
      <iconify-icon icon="lucide:arrow-right" width="16"></iconify-icon>
    </a>
  </div>

  <!-- Berhitung -->
  <div class="bg-white rounded-xl p-6 custom-shadow flex flex-col h-full">
    <div class="flex justify-between items-start mb-6">
      <div class="w-12 h-12 bg-[#FEF9C3] rounded-2xl flex items-center justify-center">
        <iconify-icon icon="lucide:calculator" width="24" class="text-[#EAB308]"></iconify-icon>
      </div>
      <span class="bg-gray-100 text-[#171a1f] text-[10px] font-bold px-3 py-1 rounded-full border border-gray-200">Status: B</span>
    </div>
    <h3 class="text-lg font-bold text-[#171a1f] mb-2">Berhitung (Hitung)</h3>
    <p class="text-sm text-[#565d6d] mb-6 flex-1">Sedang dalam tahap pengenalan angka 1-20 dan penjumlahan dasar.</p>
    <div class="space-y-2 mb-6">
      <div class="flex justify-between text-xs font-semibold">
        <span>Progres Belajar</span>
        <span>65%</span>
      </div>
      <div class="w-full h-2 bg-[#f3f4f6] rounded-full overflow-hidden">
        <div class="h-full bg-[#3d8af5]" style="width: 65%"></div>
      </div>
    </div>
    <a href="{{ route('wali.rapor') }}" class="w-full flex items-center justify-center gap-2 text-[#3d8af5] text-sm font-medium hover:underline">
      Lihat Detail Laporan
      <iconify-icon icon="lucide:arrow-right" width="16"></iconify-icon>
    </a>
  </div>
</div>

<!-- Bottom Grid: Chart + Side Panels -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
  <!-- Chart Section (xl: 2/3) -->
  <div class="xl:col-span-2 bg-white rounded-xl p-6 custom-shadow">
    <h3 class="text-lg font-bold text-[#171a1f] mb-1">Grafik Perkembangan Kemampuan</h3>
    <p class="text-sm text-[#565d6d] mb-6">Visualisasi pertumbuhan belajar Ananda selama 5 bulan terakhir</p>
    <div class="relative h-[300px] bg-[#f3f4f6]/50 rounded-xl border border-[#dee1e6] flex flex-col items-center justify-center gap-3 overflow-hidden">
      <!-- Y-axis labels -->
      <div class="absolute left-4 top-6 bottom-8 flex flex-col justify-between text-[10px] text-[#565d6d] pointer-events-none">
        <span>100</span><span>75</span><span>50</span><span>25</span><span>0</span>
      </div>
      <!-- X-axis labels -->
      <div class="absolute bottom-3 left-14 right-6 flex justify-between text-[10px] text-[#565d6d]">
        <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>Mei</span>
      </div>
      <iconify-icon icon="lucide:line-chart" width="48" class="text-[#dee1e6]"></iconify-icon>
      <p class="text-sm text-[#565d6d]">Grafik perkembangan</p>
    </div>
  </div>

  <!-- Right Panels -->
  <div class="space-y-6">
    <!-- Panduan Penilaian -->
    <div class="bg-[#fafafb] rounded-xl p-6 custom-shadow">
      <div class="flex items-center gap-2 mb-5">
        <iconify-icon icon="lucide:info" width="16" class="text-[#3d8af5]"></iconify-icon>
        <h3 class="text-sm font-bold text-[#171a1f]">Panduan Penilaian BiMBA</h3>
      </div>
      <div class="grid grid-cols-4 gap-2">
        <div class="bg-white p-2 rounded-xl custom-shadow text-center">
          <div class="w-8 h-8 bg-[#f3f4f6] rounded-full flex items-center justify-center mx-auto mb-2">
            <span class="text-xs font-bold text-[#565d6d]">K</span>
          </div>
          <p class="text-[10px] font-bold mb-0.5">Kurang</p>
          <p class="text-[8px] text-[#565d6d] leading-tight">Butuh bimbingan</p>
        </div>
        <div class="bg-white p-2 rounded-xl custom-shadow text-center">
          <div class="w-8 h-8 bg-white border border-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
            <span class="text-xs font-bold text-[#171a1f]">B</span>
          </div>
          <p class="text-[10px] font-bold mb-0.5">Belum</p>
          <p class="text-[8px] text-[#565d6d] leading-tight">Pengenalan</p>
        </div>
        <div class="bg-white p-2 rounded-xl custom-shadow text-center">
          <div class="w-8 h-8 bg-[#3d8af5] rounded-full flex items-center justify-center mx-auto mb-2">
            <span class="text-xs font-bold text-white">P</span>
          </div>
          <p class="text-[10px] font-bold mb-0.5">Paham</p>
          <p class="text-[8px] text-[#565d6d] leading-tight">Konsep dasar</p>
        </div>
        <div class="bg-white p-2 rounded-xl custom-shadow text-center">
          <div class="w-8 h-8 bg-white border border-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
            <span class="text-xs font-bold text-[#171a1f]">T</span>
          </div>
          <p class="text-[10px] font-bold mb-0.5">Terampil</p>
          <p class="text-[8px] text-[#565d6d] leading-tight">Mahir mandiri</p>
        </div>
      </div>
    </div>

    <!-- Aktivitas Terakhir -->
    <div class="bg-white rounded-xl custom-shadow overflow-hidden">
      <div class="p-5 border-b border-[#dee1e6]">
        <h3 class="text-sm font-bold text-[#171a1f]">Aktivitas Terakhir</h3>
      </div>
      <div class="divide-y divide-[#dee1e6]">
        <div class="px-5 py-4 flex items-center justify-between">
          <div class="flex items-center gap-4">
            <div class="text-center">
              <p class="text-[8px] font-bold text-[#565d6d] uppercase">Okt</p>
              <p class="text-sm font-bold text-[#171a1f]">22</p>
            </div>
            <p class="text-sm font-medium text-[#171a1f]">Modul Baca 1B Selesai</p>
          </div>
          <div class="w-6 h-6 bg-[#A7F3D0] border border-[#6EE7B7] rounded-full flex items-center justify-center text-[10px] font-bold text-[#047857]">T</div>
        </div>
        <div class="px-5 py-4 flex items-center justify-between">
          <div class="flex items-center gap-4">
            <div class="text-center">
              <p class="text-[8px] font-bold text-[#565d6d] uppercase">Okt</p>
              <p class="text-sm font-bold text-[#171a1f]">20</p>
            </div>
            <p class="text-sm font-medium text-[#171a1f]">Latihan Menulis Nama</p>
          </div>
          <div class="w-6 h-6 bg-[#BAE6FD] border border-[#7DD3FC] rounded-full flex items-center justify-center text-[10px] font-bold text-[#0369A1]">P</div>
        </div>
        <div class="px-5 py-4 flex items-center justify-between">
          <div class="flex items-center gap-4">
            <div class="text-center">
              <p class="text-[8px] font-bold text-[#565d6d] uppercase">Okt</p>
              <p class="text-sm font-bold text-[#171a1f]">18</p>
            </div>
            <p class="text-sm font-medium text-[#171a1f]">Pengenalan Angka 1-10</p>
          </div>
          <div class="w-6 h-6 bg-[#FDE68A] border border-[#FCD34D] rounded-full flex items-center justify-center text-[10px] font-bold text-[#B45309]">B</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="mt-12 py-6 border-t border-[#dee1e6] text-center">
  <p class="text-xs text-[#565d6d]">© 2026 E-Rapor BiMBA AIUEO Smart Education Centre. All rights reserved.</p>
</footer>
@endsection
