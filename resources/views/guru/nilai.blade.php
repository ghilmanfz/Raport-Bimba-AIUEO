@extends('layouts.guru')

@section('title', 'Pengolahan Nilai - E-Rapor BiMBA')
@section('page-title', 'Pengolahan Nilai')

@section('content')
<!-- Page Header Card -->
<div class="bg-white rounded-2xl p-6 border border-[#dee1e6] main-shadow flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
  <div class="flex items-start gap-4">
    <div class="w-12 h-12 bg-[#3d8af5]/10 rounded-xl flex items-center justify-center flex-shrink-0">
      <iconify-icon icon="lucide:file-text" width="22" class="text-[#3d8af5]"></iconify-icon>
    </div>
    <div>
      <h1 class="text-2xl font-bold text-[#171a1f]">Pengolahan Nilai Progres</h1>
      <p class="text-sm text-[#565d6d]">Input hasil belajar harian dan evaluasi materi murid.</p>
    </div>
  </div>
  <div class="flex flex-col sm:flex-row items-center gap-3">
    <div class="relative w-full sm:w-64">
      <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-[#565d6d]">
        <iconify-icon icon="lucide:user" width="16"></iconify-icon>
      </div>
      <select class="w-full pl-10 pr-10 py-2.5 bg-white border border-[#dee1e6] rounded-xl text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20">
        <option>Andi Wijaya (Level 1)</option>
        <option>Budi Santoso (Level 1)</option>
        <option>Citra Lestari (Level 2)</option>
        <option>Deni Pratama (Level 1)</option>
      </select>
      <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-[#565d6d]">
        <iconify-icon icon="lucide:chevron-down" width="14"></iconify-icon>
      </div>
    </div>
    <button class="w-full sm:w-auto px-6 py-2.5 bg-[#3d8af5] text-white rounded-xl text-sm font-medium shadow-md hover:bg-blue-600 flex items-center justify-center gap-2">
      <iconify-icon icon="lucide:save" width="16"></iconify-icon>
      Simpan Semua
    </button>
  </div>
</div>

<!-- Level Tabs -->
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
  <div class="bg-[#f3f4f6] p-1 rounded-full flex w-fit">
    <button class="px-6 py-1.5 bg-[#3d8af5] text-white rounded-full text-sm font-medium shadow-sm">Level 1</button>
    <button class="px-6 py-1.5 text-[#565d6d] hover:text-[#171a1f] rounded-full text-sm font-medium">Level 2</button>
    <button class="px-6 py-1.5 text-[#565d6d] hover:text-[#171a1f] rounded-full text-sm font-medium">Level 3</button>
    <button class="px-6 py-1.5 text-[#565d6d] hover:text-[#171a1f] rounded-full text-sm font-medium">Level 4</button>
  </div>
  <div class="flex items-center gap-2 px-4 py-2 border border-[#dee1e6] rounded-xl bg-white/50 text-xs font-medium text-[#171a1f]">
    <iconify-icon icon="lucide:info" width="16" class="text-[#3d8af5]"></iconify-icon>
    Status penilaian dihitung otomatis berdasarkan kelengkapan tanggal progres.
  </div>
</div>

<!-- Subject Tabs -->
<div class="border-b border-[#dee1e6] flex gap-8 mb-6">
  <button class="flex items-center gap-2 px-2 py-3 border-b-2 border-[#3d8af5] text-[#3d8af5] font-semibold text-base">
    <iconify-icon icon="lucide:book-open" width="18"></iconify-icon>
    Baca
  </button>
  <button class="flex items-center gap-2 px-2 py-3 border-b-2 border-transparent text-[#565d6d] hover:text-[#171a1f] font-semibold text-base">
    <iconify-icon icon="lucide:pencil" width="18"></iconify-icon>
    Tulis
  </button>
  <button class="flex items-center gap-2 px-2 py-3 border-b-2 border-transparent text-[#565d6d] hover:text-[#171a1f] font-semibold text-base">
    <iconify-icon icon="lucide:calculator" width="18"></iconify-icon>
    Hitung
  </button>
</div>

<!-- Table Card -->
<div class="bg-white rounded-2xl border border-[#dee1e6] main-shadow overflow-hidden mb-6">
  <div class="p-6 border-b border-[#dee1e6] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h2 class="text-lg font-semibold text-[#171a1f]">Modul Pembelajaran: Baca - Level 1</h2>
      <p class="text-sm text-[#565d6d]">Detail progres materi untuk Andi Wijaya</p>
    </div>
    <button class="px-4 py-1.5 border border-[#dee1e6] rounded-lg text-xs font-medium text-[#171a1f] hover:bg-gray-50">
      Reset Form
    </button>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-[#f3f4f6]/50 border-b border-[#dee1e6] text-sm font-semibold text-[#171a1f]">
          <th class="px-6 py-4 min-w-[280px]">Nama Materi</th>
          <th class="px-6 py-4 min-w-[160px]">Tanggal Mulai</th>
          <th class="px-6 py-4 min-w-[160px]">Tanggal Paham</th>
          <th class="px-6 py-4 min-w-[160px]">Tanggal Terampil</th>
          <th class="px-6 py-4 text-center min-w-[140px]">Status (K/B/P/T)</th>
          <th class="px-6 py-4 text-right min-w-[60px]">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#dee1e6]">
        @php
          $materi = [
            ['nama' => 'Pengenalan Huruf Vokal (A-I-U-E-O)',     'mulai' => '2024-03-01', 'paham' => '2024-03-10', 'terampil' => '2024-03-15', 'status' => 'T', 'label' => 'T - Terampil', 'badge' => 'bg-[#A7F3D0] border border-[#6EE7B7] text-[#047857]'],
            ['nama' => 'Membaca Suku Kata Terbuka (Ba, Bi, Bu)', 'mulai' => '2024-03-16', 'paham' => '2024-03-25', 'terampil' => '',            'status' => 'P', 'label' => 'P - Paham',    'badge' => 'bg-[#BAE6FD] border border-[#7DD3FC] text-[#0369A1]'],
            ['nama' => 'Pengenalan Kata Bermakna 4 Huruf',       'mulai' => '2024-03-26', 'paham' => '',            'terampil' => '',            'status' => 'B', 'label' => 'B - Belum',    'badge' => 'bg-[#FDE68A] border border-[#FCD34D] text-[#B45309]'],
            ['nama' => 'Kalimat Sederhana (3 Kata)',             'mulai' => '',            'paham' => '',            'terampil' => '',            'status' => 'K', 'label' => 'K - Kenal',    'badge' => 'bg-[#E2E8F0] border border-[#CBD5E1] text-[#334155]'],
            ['nama' => 'Pengenalan Huruf Konsonan B-D-G-K',      'mulai' => '2024-02-15', 'paham' => '2024-02-28', 'terampil' => '2024-03-05', 'status' => 'T', 'label' => 'T - Terampil', 'badge' => 'bg-[#A7F3D0] border border-[#6EE7B7] text-[#047857]'],
          ];
        @endphp
        @foreach($materi as $m)
        <tr class="hover:bg-gray-50/50">
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="w-2 h-2 rounded-full bg-[#3d8af5] flex-shrink-0"></div>
              <span class="text-sm font-medium text-[#171a1f]">{{ $m['nama'] }}</span>
            </div>
          </td>
          <td class="px-6 py-4">
            <input type="date" value="{{ $m['mulai'] }}" class="w-full px-3 py-2 border border-[#dee1e6] rounded-lg text-sm focus:ring-1 focus:ring-[#3d8af5] outline-none">
          </td>
          <td class="px-6 py-4">
            <input type="date" value="{{ $m['paham'] }}" class="w-full px-3 py-2 border border-[#dee1e6] rounded-lg text-sm focus:ring-1 focus:ring-[#3d8af5] outline-none">
          </td>
          <td class="px-6 py-4">
            <input type="date" value="{{ $m['terampil'] }}" class="w-full px-3 py-2 border border-[#dee1e6] rounded-lg text-sm focus:ring-1 focus:ring-[#3d8af5] outline-none">
          </td>
          <td class="px-6 py-4 text-center">
            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $m['badge'] }}">{{ $m['label'] }}</span>
          </td>
          <td class="px-6 py-4 text-right">
            <button class="text-gray-400 hover:text-gray-600 p-1">
              <iconify-icon icon="lucide:more-vertical" width="16"></iconify-icon>
            </button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="p-4 bg-[#f3f4f6]/30 border-t border-[#dee1e6] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <p class="text-sm text-[#565d6d] font-roboto">Menampilkan <span class="font-semibold text-[#171a1f]">5</span> materi di aspek <span class="font-semibold text-[#171a1f]">Baca</span></p>
    <div class="flex gap-2">
      <button class="px-4 py-2 border border-[#dee1e6] bg-white rounded-xl text-sm font-medium text-[#171a1f] hover:bg-gray-50">Import Excel</button>
      <button class="px-4 py-2 border border-[#dee1e6] bg-white rounded-xl text-sm font-medium text-[#171a1f] hover:bg-gray-50">Export PDF</button>
    </div>
  </div>
</div>

<!-- Status Legend Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
  <div class="bg-[#F1F6FE] p-6 rounded-xl border border-[#dee1e6]/30 text-center">
    <h3 class="text-2xl font-bold text-[#3d8af5] mb-1">K</h3>
    <p class="text-xs font-bold text-[#3d8af5]/70 tracking-widest uppercase mb-2">Kenal</p>
    <p class="text-sm text-[#3d8af5]/60">Belum ada progres</p>
  </div>
  <div class="bg-[#FCF0E3] p-6 rounded-xl border border-[#dee1e6]/30 text-center">
    <h3 class="text-2xl font-bold text-[#171a1f] mb-1">B</h3>
    <p class="text-xs font-bold text-[#171a1f]/70 tracking-widest uppercase mb-2">Belum</p>
    <p class="text-sm text-[#171a1f]/60">Baru mulai belajar</p>
  </div>
  <div class="bg-[#E1F4FE] p-6 rounded-xl border border-[#dee1e6]/30 text-center">
    <h3 class="text-2xl font-bold text-[#171a1f] mb-1">P</h3>
    <p class="text-xs font-bold text-[#171a1f]/70 tracking-widest uppercase mb-2">Paham</p>
    <p class="text-sm text-[#171a1f]/60">Materi dikuasai</p>
  </div>
  <div class="bg-[#DCFAE6] p-6 rounded-xl border border-[#dee1e6]/30 text-center">
    <h3 class="text-2xl font-bold text-[#171a1f] mb-1">T</h3>
    <p class="text-xs font-bold text-[#171a1f]/70 tracking-widest uppercase mb-2">Terampil</p>
    <p class="text-sm text-[#171a1f]/60">Dapat mengaplikasikan</p>
  </div>
</div>
@endsection
