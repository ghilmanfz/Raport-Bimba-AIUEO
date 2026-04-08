@extends('layouts.wali')

@section('title', 'Laporan Rapor - E-Rapor BiMBA AIUEO')
@section('page-title', 'Laporan Rapor')

@push('head')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
  .font-poppins { font-family: 'Poppins', sans-serif; }
  .shadow-soft { box-shadow: 0 1px 2.5px 0 rgba(23,26,31,0.07), 0 0 2px 0 rgba(23,26,31,0.08); }
  .table-row-hover:hover { background-color: #f8fafc; }
</style>
@endpush

@section('content')
<!-- Title & Actions -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
  <div>
    <h1 class="text-3xl font-extrabold tracking-tight text-[#171a1f] font-poppins">Laporan Rapor</h1>
    <p class="text-[#565d6d] mt-1 text-sm font-roboto">Pantau progres belajar Ananda Salsabila secara berkala.</p>
  </div>
  <div class="flex gap-3">
    <button class="flex items-center gap-2 px-4 py-2 bg-white border border-[#3d8af5]/20 rounded-2xl shadow-soft text-[#3d8af5] font-medium text-sm">
      <iconify-icon icon="lucide:file-spreadsheet" width="16"></iconify-icon>
      Export Excel
    </button>
    <button class="flex items-center gap-2 px-4 py-2 bg-[#3d8af5] rounded-2xl text-white font-medium text-sm shadow-md hover:bg-blue-600">
      <iconify-icon icon="lucide:file-down" width="16"></iconify-icon>
      Export PDF
    </button>
  </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-3xl p-6 shadow-soft mb-8 grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
  <div class="md:col-span-5 space-y-2">
    <label class="text-[10px] font-bold text-[#565d6d] uppercase tracking-wider font-roboto">Pilih Murid</label>
    <div class="relative">
      <input type="text" value="Ananda Salsabila" readonly class="w-full px-4 py-2.5 bg-white border border-[#dee1e6] rounded-2xl text-sm font-roboto cursor-pointer focus:outline-none">
      <div class="absolute right-4 top-1/2 -translate-y-1/2 text-[#565d6d]">
        <iconify-icon icon="lucide:chevron-down" width="16"></iconify-icon>
      </div>
    </div>
  </div>
  <div class="md:col-span-5 space-y-2">
    <label class="text-[10px] font-bold text-[#565d6d] uppercase tracking-wider font-roboto">Periode</label>
    <div class="relative">
      <input type="text" value="Semester Ganjil 2023" readonly class="w-full px-4 py-2.5 bg-white border border-[#dee1e6] rounded-2xl text-sm font-roboto cursor-pointer focus:outline-none">
      <div class="absolute right-4 top-1/2 -translate-y-1/2 text-[#565d6d]">
        <iconify-icon icon="lucide:chevron-down" width="16"></iconify-icon>
      </div>
    </div>
  </div>
  <div class="md:col-span-2">
    <button class="w-full h-[44px] bg-[#F1F6FE] rounded-2xl flex items-center justify-center text-[#3d8af5] hover:bg-[#3d8af5] hover:text-white">
      <iconify-icon icon="lucide:search" width="18"></iconify-icon>
    </button>
  </div>
</div>

<!-- Student Info & Stats -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
  <!-- Profile Card -->
  <div class="lg:col-span-8 bg-[#F1F6FE] rounded-[32px] p-8 shadow-soft relative overflow-hidden flex flex-col md:flex-row items-center gap-8">
    <div class="absolute top-0 right-0 w-32 h-32 bg-[#3d8af5]/5 rounded-full blur-3xl -mr-16 -mt-16"></div>
    <div class="w-24 h-24 rounded-full border-4 border-white shadow-lg overflow-hidden flex-shrink-0 bg-[#f2bf8c] flex items-center justify-center text-white text-2xl font-black">AS</div>
    <div class="flex-1 space-y-3 text-center md:text-left">
      <div class="flex flex-wrap items-center justify-center md:justify-start gap-3">
        <h2 class="text-2xl font-bold font-poppins text-[#171a1f]">Ananda Salsabila</h2>
        <span class="px-3 py-0.5 border border-[#dee1e6] rounded-full text-[10px] font-bold bg-white/50">Aktif</span>
      </div>
      <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 text-[#565d6d] text-sm font-medium font-roboto">
        <div class="flex items-center gap-1.5">
          <iconify-icon icon="lucide:layers" width="14" class="text-[#3d8af5]"></iconify-icon>
          Level 2 - Unit Pondok Aren
        </div>
        <div class="hidden md:block w-1.5 h-1.5 bg-gray-300 rounded-full"></div>
        <div class="flex items-center gap-1.5">
          <iconify-icon icon="lucide:id-card" width="14"></iconify-icon>
          ID: BiMBA-2023-0892
        </div>
      </div>
      <p class="text-[#3d8af5] font-semibold font-roboto">Selamat! Ananda mencapai progres yang sangat baik bulan ini. 👋</p>
    </div>
  </div>

  <!-- Achievement Card -->
  <div class="lg:col-span-4 bg-white rounded-[32px] p-6 shadow-soft space-y-4">
    <div class="flex items-center gap-2">
      <iconify-icon icon="lucide:award" width="20" class="text-[#3d8af5]"></iconify-icon>
      <h3 class="text-lg font-bold font-roboto text-[#171a1f]">Capaian Ananda</h3>
    </div>
    <div class="grid grid-cols-2 gap-4">
      <div class="bg-[#FDF7F2] border border-[#dee1e6] rounded-3xl p-4 text-center space-y-1">
        <p class="text-[10px] font-bold text-[#565d6d] uppercase tracking-widest font-roboto">Total Bintang</p>
        <div class="flex items-center justify-center gap-2">
          <iconify-icon icon="lucide:star" width="22" class="text-[#EAB308]"></iconify-icon>
          <span class="text-2xl font-black text-[#171a1f]">248</span>
        </div>
      </div>
      <div class="bg-[#F1F6FE] border border-[#3d8af5]/10 rounded-3xl p-4 text-center space-y-1">
        <p class="text-[10px] font-bold text-[#565d6d] uppercase tracking-widest font-roboto">Peringkat</p>
        <div class="flex items-center justify-center gap-2">
          <iconify-icon icon="lucide:trophy" width="22" class="text-[#3d8af5]"></iconify-icon>
          <span class="text-2xl font-black text-[#3d8af5]">TOP 5</span>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Subject Progress Cards + Activity -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
  <!-- Subject Cards -->
  <div class="lg:col-span-8 space-y-6">
    <div class="flex items-center gap-2">
      <iconify-icon icon="lucide:trending-up" width="20" class="text-[#3d8af5]"></iconify-icon>
      <h3 class="text-lg font-bold text-[#171a1f]">Ringkasan Progres Saat Ini</h3>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <!-- Membaca -->
      <div class="bg-white rounded-2xl p-6 shadow-soft flex flex-col h-full">
        <div class="flex justify-between items-start mb-6">
          <div class="w-12 h-12 bg-[#22C55E]/10 rounded-2xl flex items-center justify-center">
            <iconify-icon icon="lucide:book-open" width="24" class="text-[#22C55E]"></iconify-icon>
          </div>
          <span class="px-3 py-1 border border-[#dee1e6] rounded-full text-[10px] font-bold">Status: T</span>
        </div>
        <h4 class="text-lg font-bold text-[#171a1f] mb-2">Membaca (Baca)</h4>
        <p class="text-sm text-[#565d6d] font-roboto leading-relaxed flex-1">Ananda sudah mampu membaca kalimat sederhana dengan lancar tanpa terbata-bata.</p>
        <div class="mt-6 space-y-2">
          <div class="flex justify-between text-xs font-bold">
            <span>Progres Belajar</span><span>95%</span>
          </div>
          <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full bg-[#22C55E]" style="width: 95%"></div>
          </div>
        </div>
      </div>

      <!-- Menulis -->
      <div class="bg-white rounded-2xl p-6 shadow-soft flex flex-col h-full">
        <div class="flex justify-between items-start mb-6">
          <div class="w-12 h-12 bg-[#3d8af5]/10 rounded-2xl flex items-center justify-center">
            <iconify-icon icon="lucide:pencil" width="24" class="text-[#3d8af5]"></iconify-icon>
          </div>
          <span class="px-3 py-1 bg-[#3d8af5] text-white rounded-full text-[10px] font-bold">Status: P</span>
        </div>
        <h4 class="text-lg font-bold text-[#171a1f] mb-2">Menulis (Tulis)</h4>
        <p class="text-sm text-[#565d6d] font-roboto leading-relaxed flex-1">Memahami struktur huruf besar-kecil dan mulai merangkai kata dengan rapi.</p>
        <div class="mt-6 space-y-2">
          <div class="flex justify-between text-xs font-bold">
            <span>Progres Belajar</span><span>80%</span>
          </div>
          <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full bg-[#3d8af5]" style="width: 80%"></div>
          </div>
        </div>
      </div>

      <!-- Berhitung -->
      <div class="bg-white rounded-2xl p-6 shadow-soft flex flex-col h-full">
        <div class="flex justify-between items-start mb-6">
          <div class="w-12 h-12 bg-[#9CA3AF]/10 rounded-2xl flex items-center justify-center">
            <iconify-icon icon="lucide:calculator" width="24" class="text-[#9CA3AF]"></iconify-icon>
          </div>
          <span class="px-3 py-1 border border-[#dee1e6] rounded-full text-[10px] font-bold">Status: B</span>
        </div>
        <h4 class="text-lg font-bold text-[#171a1f] mb-2">Berhitung (Hitung)</h4>
        <p class="text-sm text-[#565d6d] font-roboto leading-relaxed flex-1">Sedang dalam tahap pengenalan angka 1-20 dan operasi penjumlahan dasar.</p>
        <div class="mt-6 space-y-2">
          <div class="flex justify-between text-xs font-bold">
            <span>Progres Belajar</span><span>65%</span>
          </div>
          <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full bg-[#3d8af5]" style="width: 65%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Aktivitas Terakhir -->
  <div class="lg:col-span-4 bg-white rounded-[32px] p-6 shadow-soft flex flex-col">
    <div class="flex items-center gap-2 mb-6">
      <iconify-icon icon="lucide:activity" width="18" class="text-[#3d8af5]"></iconify-icon>
      <h3 class="text-lg font-bold font-roboto text-[#171a1f]">Aktivitas Terakhir</h3>
    </div>
    <div class="space-y-6 flex-1">
      <div class="flex items-center gap-4">
        <div class="w-10 h-10 bg-white shadow-soft rounded-full flex flex-col items-center justify-center border border-gray-50 flex-shrink-0">
          <span class="text-[8px] font-black text-[#565d6d]">OKT</span>
          <span class="text-sm font-black text-[#171a1f]">22</span>
        </div>
        <div class="flex-1"><p class="text-sm font-bold text-[#171a1f]">Lulus Modul Baca 1B</p></div>
        <div class="w-6 h-6 bg-[#22C55E] rounded-full flex items-center justify-center text-white text-[10px] font-black">T</div>
      </div>
      <div class="flex items-center gap-4">
        <div class="w-10 h-10 bg-white shadow-soft rounded-full flex flex-col items-center justify-center border border-gray-50 flex-shrink-0">
          <span class="text-[8px] font-black text-[#565d6d]">OKT</span>
          <span class="text-sm font-black text-[#171a1f]">20</span>
        </div>
        <div class="flex-1"><p class="text-sm font-bold text-[#171a1f]">Latihan Menulis Nama</p></div>
        <div class="w-6 h-6 bg-[#3d8af5] rounded-full flex items-center justify-center text-white text-[10px] font-black">P</div>
      </div>
      <div class="flex items-center gap-4">
        <div class="w-10 h-10 bg-white shadow-soft rounded-full flex flex-col items-center justify-center border border-gray-50 flex-shrink-0">
          <span class="text-[8px] font-black text-[#565d6d]">OKT</span>
          <span class="text-sm font-black text-[#171a1f]">18</span>
        </div>
        <div class="flex-1"><p class="text-sm font-bold text-[#171a1f]">Belajar Angka 1-10</p></div>
        <div class="w-6 h-6 bg-[#9CA3AF] rounded-full flex items-center justify-center text-white text-[10px] font-black">B</div>
      </div>
    </div>
    <button class="w-full mt-8 py-2.5 text-[#3d8af5] text-xs font-bold hover:bg-blue-50 rounded-xl">
      Lihat Semua Aktivitas
    </button>
  </div>
</div>

<!-- Chart & Legend -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
  <!-- Chart -->
  <div class="lg:col-span-8 bg-white rounded-[32px] p-8 shadow-soft">
    <h3 class="text-xl font-bold text-[#171a1f] mb-1 font-poppins">Grafik Perkembangan Kemampuan</h3>
    <p class="text-sm text-[#565d6d] font-roboto mb-8">Visualisasi pertumbuhan belajar Ananda selama 5 bulan terakhir.</p>
    <div class="relative h-[260px] bg-[#f3f4f6]/50 rounded-xl border border-[#dee1e6] flex flex-col items-center justify-center gap-3 overflow-hidden">
      <div class="absolute left-4 top-6 bottom-8 flex flex-col justify-between text-[10px] text-[#565d6d]">
        <span>100</span><span>75</span><span>50</span><span>25</span><span>0</span>
      </div>
      <div class="absolute bottom-3 left-14 right-6 flex justify-between text-[10px] text-[#565d6d]">
        <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>Mei</span>
      </div>
      <iconify-icon icon="lucide:line-chart" width="48" class="text-[#dee1e6]"></iconify-icon>
      <p class="text-sm text-[#565d6d]">Grafik pertumbuhan belajar</p>
    </div>
  </div>

  <!-- Legend & Agenda -->
  <div class="lg:col-span-4 space-y-6">
    <!-- Panduan Penilaian -->
    <div class="bg-white rounded-[32px] p-6 shadow-soft">
      <div class="flex items-center gap-2 mb-5">
        <iconify-icon icon="lucide:info" width="18" class="text-[#3d8af5]"></iconify-icon>
        <h3 class="text-lg font-bold font-roboto text-[#171a1f]">Panduan Penilaian</h3>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div class="bg-[#FEE2E2]/60 border border-[#FEE2E2] rounded-2xl p-3 text-center">
          <div class="w-8 h-8 bg-[#EF4444]/10 rounded-full flex items-center justify-center mx-auto mb-2">
            <span class="text-[#EF4444] font-bold">K</span>
          </div>
          <p class="text-xs font-bold">Kurang</p>
          <p class="text-[10px] text-[#565d6d] font-roboto">Butuh bimbingan</p>
        </div>
        <div class="bg-[#f3f4f6]/60 border border-[#E5E7EB] rounded-2xl p-3 text-center">
          <div class="w-8 h-8 bg-[#9CA3AF]/10 rounded-full flex items-center justify-center mx-auto mb-2">
            <span class="text-[#4B5563] font-bold">B</span>
          </div>
          <p class="text-xs font-bold">Belum</p>
          <p class="text-[10px] text-[#565d6d] font-roboto">Tahap pengenalan</p>
        </div>
        <div class="bg-[#E0F2FE]/60 border border-[#BAE6FD] rounded-2xl p-3 text-center">
          <div class="w-8 h-8 bg-[#3d8af5]/10 rounded-full flex items-center justify-center mx-auto mb-2">
            <span class="text-[#3d8af5] font-bold">P</span>
          </div>
          <p class="text-xs font-bold">Paham</p>
          <p class="text-[10px] text-[#565d6d] font-roboto">Mengerti konsep</p>
        </div>
        <div class="bg-[#DCFCE7]/60 border border-[#BBF7D0] rounded-2xl p-3 text-center">
          <div class="w-8 h-8 bg-[#22C55E]/10 rounded-full flex items-center justify-center mx-auto mb-2">
            <span class="text-[#22C55E] font-bold">T</span>
          </div>
          <p class="text-xs font-bold">Terampil</p>
          <p class="text-[10px] text-[#565d6d] font-roboto">Mahir dan mandiri</p>
        </div>
      </div>
    </div>

    <!-- Agenda -->
    <div class="bg-white rounded-[32px] shadow-soft overflow-hidden">
      <div class="bg-[#3d8af5] p-6 flex items-center gap-3">
        <iconify-icon icon="lucide:calendar" width="16" class="text-white"></iconify-icon>
        <h3 class="text-white font-bold font-roboto">Agenda Mendatang</h3>
      </div>
      <div class="p-6 space-y-6">
        <div class="flex gap-4">
          <div class="w-1 h-10 bg-[#3d8af5] rounded-full flex-shrink-0"></div>
          <div>
            <p class="text-[10px] font-bold text-[#9CA3AF] uppercase tracking-wider">28 Okt 2023</p>
            <p class="text-sm font-bold text-[#171a1f]">Evaluasi Level 2 Unit 1</p>
          </div>
        </div>
        <div class="flex gap-4">
          <div class="w-1 h-10 bg-[#dee1e6] rounded-full flex-shrink-0"></div>
          <div>
            <p class="text-[10px] font-bold text-[#9CA3AF] uppercase tracking-wider">02 Nov 2023</p>
            <p class="text-sm font-bold text-[#171a1f]">Pertemuan Wali Murid</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Teacher Notes -->
<div class="bg-[#DCFAE6]/30 rounded-[32px] p-8 shadow-soft mb-8">
  <div class="flex items-center gap-3 mb-4">
    <div class="w-9 h-9 bg-[#63e98f]/20 rounded-2xl flex items-center justify-center">
      <iconify-icon icon="lucide:message-circle" width="20" class="text-[#22C55E]"></iconify-icon>
    </div>
    <h3 class="text-lg font-bold text-[#171a1f]">Catatan Guru &amp; Rekomendasi</h3>
  </div>
  <p class="text-lg text-[#171a1f] font-medium italic leading-relaxed mb-6">
    "Ananda Salsabila menunjukkan antusiasme yang sangat tinggi dalam materi Membaca. Dia sudah bisa menggabungkan bunyi huruf dengan sangat lancar. Untuk materi Berhitung, kami menyarankan wali murid untuk sering mengajak Ananda menghitung benda di sekitar rumah untuk memperkuat konsep angka 1-20."
  </p>
  <div class="flex items-center gap-4">
    <div class="w-10 h-10 rounded-full bg-[#63e98f] flex items-center justify-center text-white text-sm font-bold flex-shrink-0">SN</div>
    <div>
      <p class="text-sm font-bold text-[#171a1f]">Ibu Siti Nurhaliza</p>
      <p class="text-xs text-[#565d6d] font-roboto">Motivator Level 2</p>
    </div>
  </div>
</div>

<!-- History Table -->
<div class="space-y-4">
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <h3 class="text-lg font-bold text-[#171a1f]">Histori Penilaian</h3>
    <div class="relative w-full md:w-64">
      <div class="absolute left-4 top-1/2 -translate-y-1/2 text-[#565d6d]">
        <iconify-icon icon="lucide:search" width="16"></iconify-icon>
      </div>
      <input type="text" placeholder="Cari modul atau catatan..." class="w-full pl-10 pr-4 py-2 bg-white border border-[#dee1e6] rounded-2xl text-sm font-roboto focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20">
    </div>
  </div>

  <div class="bg-white rounded-[32px] shadow-soft overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-gray-100">
            <th class="px-6 py-4 text-sm font-bold text-[#171a1f]">Tanggal</th>
            <th class="px-6 py-4 text-sm font-bold text-[#171a1f]">Modul / Materi</th>
            <th class="px-6 py-4 text-sm font-bold text-[#171a1f]">Aspek</th>
            <th class="px-6 py-4 text-sm font-bold text-[#171a1f]">Status</th>
            <th class="px-6 py-4 text-sm font-bold text-[#171a1f] text-right">Skor</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          @php
            $histori = [
              ['tanggal' => '22 Okt 2023', 'modul' => 'Modul Baca 1B',           'aspek' => 'Baca',   'status' => 'T: Terampil', 'badge' => 'bg-[#DCFCE7] text-[#22C55E]', 'skor' => '95%'],
              ['tanggal' => '20 Okt 2023', 'modul' => 'Latihan Menulis Nama',     'aspek' => 'Tulis',  'status' => 'P: Paham',    'badge' => 'bg-[#E0F2FE] text-[#3d8af5]', 'skor' => '80%'],
              ['tanggal' => '18 Okt 2023', 'modul' => 'Pengenalan Angka 1-10',   'aspek' => 'Hitung', 'status' => 'B: Belum',    'badge' => 'bg-[#f3f4f6] text-[#6B7280]',  'skor' => '65%'],
              ['tanggal' => '15 Okt 2023', 'modul' => 'Modul Baca 1A',           'aspek' => 'Baca',   'status' => 'T: Terampil', 'badge' => 'bg-[#DCFCE7] text-[#22C55E]', 'skor' => '92%'],
              ['tanggal' => '12 Okt 2023', 'modul' => 'Garis Tegak &amp; Datar', 'aspek' => 'Tulis',  'status' => 'P: Paham',    'badge' => 'bg-[#E0F2FE] text-[#3d8af5]', 'skor' => '75%'],
            ];
          @endphp
          @foreach($histori as $h)
          <tr class="table-row-hover">
            <td class="px-6 py-5 text-sm text-[#565d6d] font-medium">{{ $h['tanggal'] }}</td>
            <td class="px-6 py-5 text-sm font-bold text-[#171a1f]">{!! $h['modul'] !!}</td>
            <td class="px-6 py-5">
              <span class="px-3 py-1 bg-[#F1F6FE] text-[#3d8af5] rounded-full text-xs font-bold">{{ $h['aspek'] }}</span>
            </td>
            <td class="px-6 py-5">
              <span class="px-3 py-1 rounded-full text-xs font-bold {{ $h['badge'] }}">{{ $h['status'] }}</span>
            </td>
            <td class="px-6 py-5 text-sm font-bold text-right text-[#171a1f]">{{ $h['skor'] }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="bg-[#f3f4f6]/20 px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-[#dee1e6]">
      <span class="text-xs text-[#565d6d] font-roboto">Menampilkan 1-5 dari 24 entri</span>
      <div class="flex items-center gap-2">
        <button class="w-8 h-8 flex items-center justify-center bg-white border border-[#dee1e6] rounded-lg opacity-50">
          <iconify-icon icon="lucide:chevron-left" width="14"></iconify-icon>
        </button>
        <button class="w-8 h-8 flex items-center justify-center bg-[#3d8af5] text-white rounded-lg font-bold text-sm">1</button>
        <button class="w-8 h-8 flex items-center justify-center bg-white border border-[#dee1e6] rounded-lg font-bold text-sm">2</button>
        <button class="w-8 h-8 flex items-center justify-center bg-white border border-[#dee1e6] rounded-lg font-bold text-sm">3</button>
        <button class="w-8 h-8 flex items-center justify-center bg-white border border-[#dee1e6] rounded-lg">
          <iconify-icon icon="lucide:chevron-right" width="14"></iconify-icon>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Footer Actions -->
<div class="flex flex-wrap justify-center gap-4 pt-8 pb-4">
  <button class="flex items-center gap-3 px-8 py-3 bg-white border border-[#3d8af5] rounded-2xl text-[#3d8af5] font-medium font-roboto hover:bg-[#F1F6FE]">
    <iconify-icon icon="lucide:printer" width="18"></iconify-icon>
    Cetak Rapor
  </button>
  <button class="flex items-center gap-3 px-8 py-3 bg-[#3d8af5] rounded-2xl text-white font-medium font-roboto shadow-md hover:bg-blue-600">
    <iconify-icon icon="lucide:download" width="18"></iconify-icon>
    Unduh Rapor (.PDF)
  </button>
</div>

<!-- Footer -->
<footer class="pt-8 pb-6 border-t border-[#dee1e6] text-center space-y-3">
  <p class="text-sm text-[#565d6d] font-medium">
    © 2026 <span class="text-[#3d8af5] font-bold">E-Rapor BiMBA AIUEO</span> Smart Education Centre. All rights reserved.
  </p>
  <div class="flex justify-center gap-6 text-xs text-[#565d6d] font-roboto">
    <a href="#" class="hover:text-[#3d8af5]">Syarat &amp; Ketentuan</a>
    <a href="#" class="hover:text-[#3d8af5]">Kebijakan Privasi</a>
    <a href="#" class="hover:text-[#3d8af5]">Pusat Bantuan</a>
  </div>
</footer>
@endsection
