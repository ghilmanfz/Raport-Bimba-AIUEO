@extends('layouts.guru')

@section('title', 'Cetak Rapor Digital - E-Rapor BiMBA')
@section('page-title', 'Cetak Rapor Digital')

@push('head')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
  .gradient-border-top {
    position: relative;
  }
  .gradient-border-top::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 12px;
    background: linear-gradient(90deg, #3d8af5 0%, #63e98f 50%, #f2bf8c 100%);
    border-radius: 16px 16px 0 0;
  }
  .font-poppins { font-family: 'Poppins', sans-serif; }
  @media print {
    .no-print { display: none !important; }
    body { background: white; }
  }
</style>
@endpush

@section('content')
<!-- Page Header (no-print) -->
<div class="no-print flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
  <div>
    <h1 class="text-2xl font-bold text-[#171a1f] font-poppins">Cetak Rapor Digital</h1>
    <p class="text-sm text-[#565d6d]">Laporan perkembangan belajar murid BiMBA AIUEO</p>
  </div>
  <div class="flex flex-wrap gap-2">
    <button class="flex items-center gap-2 px-4 py-2.5 border border-[#dee1e6] bg-white rounded-xl text-sm font-medium text-[#171a1f] hover:bg-gray-50">
      <iconify-icon icon="lucide:file-spreadsheet" width="16" class="text-[#63e98f]"></iconify-icon>
      Export Excel
    </button>
    <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2.5 border border-[#dee1e6] bg-white rounded-xl text-sm font-medium text-[#171a1f] hover:bg-gray-50">
      <iconify-icon icon="lucide:printer" width="16" class="text-[#3d8af5]"></iconify-icon>
      Cetak
    </button>
    <button class="flex items-center gap-2 px-4 py-2.5 bg-[#3d8af5] rounded-xl text-white text-sm font-medium shadow-md hover:bg-blue-600">
      <iconify-icon icon="lucide:file-down" width="16"></iconify-icon>
      Export PDF
    </button>
  </div>
</div>

<!-- Student Selector (no-print) -->
<div class="no-print bg-white rounded-2xl border border-[#dee1e6] main-shadow p-5 mb-6 grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
  <div class="md:col-span-5 space-y-1.5">
    <label class="text-xs font-semibold text-[#565d6d] uppercase tracking-wider">Pilih Murid</label>
    <div class="relative">
      <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-[#565d6d]">
        <iconify-icon icon="lucide:user" width="16"></iconify-icon>
      </div>
      <select class="w-full pl-9 pr-10 py-2.5 border border-[#dee1e6] rounded-xl text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20 bg-white">
        <option>Ananda Rizky Pratama</option>
        <option>Salsabila Aulia</option>
        <option>Bima Anugrah</option>
      </select>
      <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-[#565d6d]">
        <iconify-icon icon="lucide:chevron-down" width="14"></iconify-icon>
      </div>
    </div>
  </div>
  <div class="md:col-span-5 space-y-1.5">
    <label class="text-xs font-semibold text-[#565d6d] uppercase tracking-wider">Periode Laporan</label>
    <div class="relative">
      <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-[#565d6d]">
        <iconify-icon icon="lucide:calendar" width="16"></iconify-icon>
      </div>
      <select class="w-full pl-9 pr-10 py-2.5 border border-[#dee1e6] rounded-xl text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20 bg-white">
        <option>November 2023</option>
        <option>Oktober 2023</option>
        <option>September 2023</option>
      </select>
      <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-[#565d6d]">
        <iconify-icon icon="lucide:chevron-down" width="14"></iconify-icon>
      </div>
    </div>
  </div>
  <div class="md:col-span-2">
    <button class="w-full py-2.5 bg-[#3d8af5] text-white rounded-xl text-sm font-medium shadow-md hover:bg-blue-600 flex items-center justify-center gap-2">
      <iconify-icon icon="lucide:search" width="16"></iconify-icon>
      Tampilkan
    </button>
  </div>
</div>

<!-- Report Card -->
<div class="max-w-[1088px] mx-auto bg-white rounded-2xl gradient-border-top main-shadow overflow-hidden mb-6">
  <!-- Report Header -->
  <div class="p-8 pb-6 border-b border-[#dee1e6]">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="flex items-center gap-4">
        <div class="w-14 h-14 bg-[#3d8af5] rounded-2xl flex items-center justify-center flex-shrink-0">
          <iconify-icon icon="lucide:book-open" width="28" class="text-white"></iconify-icon>
        </div>
        <div>
          <h1 class="text-2xl font-extrabold text-[#3d8af5] font-poppins">BiMBA AIUEO</h1>
          <p class="text-sm font-semibold text-[#565d6d]">Laporan Perkembangan Belajar</p>
        </div>
      </div>
      <div class="text-right">
        <span class="px-4 py-1.5 border border-[#dee1e6] rounded-full text-xs font-semibold text-[#565d6d]">Periode: November 2023</span>
      </div>
    </div>
  </div>

  <!-- Student Info -->
  <div class="p-8 pb-0">
    <div class="bg-[#F1F6FE] rounded-2xl p-6 mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="flex items-center gap-4">
        <div class="w-16 h-16 bg-[#3d8af5] rounded-2xl flex items-center justify-center text-white text-xl font-black flex-shrink-0">AR</div>
        <div>
          <h2 class="text-xl font-bold text-[#171a1f] font-poppins">Ananda Rizky Pratama</h2>
          <div class="flex flex-wrap gap-3 mt-2 text-xs font-semibold text-[#565d6d]">
            <span class="flex items-center gap-1"><iconify-icon icon="lucide:id-card" width="12"></iconify-icon> ID: BMB-2023-089</span>
            <span class="flex items-center gap-1"><iconify-icon icon="lucide:layers" width="12"></iconify-icon> Level 2 (Lanjut)</span>
            <span class="flex items-center gap-1"><iconify-icon icon="lucide:calendar" width="12"></iconify-icon> Gabung 15 Jan 2023</span>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl px-5 py-3 text-center border border-[#dee1e6]">
        <p class="text-[10px] font-bold text-[#565d6d] uppercase tracking-wider">Tanggal Rapor</p>
        <p class="text-sm font-bold text-[#171a1f] mt-1">30 November 2023</p>
      </div>
    </div>
  </div>

  <!-- Radar Chart + Analysis -->
  <div class="px-8 pb-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Left: Analysis + Summary Cards -->
    <div class="flex flex-col justify-between gap-6">
      <div>
        <h3 class="text-base font-bold text-[#171a1f] mb-3 font-poppins">Analisis Perkembangan</h3>
        <p class="text-sm text-[#565d6d] leading-relaxed">
          Ananda Rizky menunjukkan perkembangan yang sangat positif pada aspek <strong>Membaca</strong> dan <strong>Minat Belajar</strong>. 
          Perlu perhatian lebih pada kemampuan <strong>Menulis Dasar</strong> untuk meningkatkan ketepatan penulisan huruf.
        </p>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-[#DCFAE6] border border-[#86EFAC] rounded-xl p-4">
          <div class="flex items-center gap-2 mb-2">
            <iconify-icon icon="lucide:trending-up" width="16" class="text-[#047857]"></iconify-icon>
            <span class="text-xs font-bold text-[#047857] uppercase tracking-wider">Tertinggi</span>
          </div>
          <p class="text-sm font-bold text-[#171a1f]">Minat Belajar</p>
          <p class="text-xs text-[#565d6d] mt-1">Sangat antusias dalam setiap sesi belajar</p>
        </div>
        <div class="bg-[#FEF9C3] border border-[#FDE047] rounded-xl p-4">
          <div class="flex items-center gap-2 mb-2">
            <iconify-icon icon="lucide:target" width="16" class="text-[#B45309]"></iconify-icon>
            <span class="text-xs font-bold text-[#B45309] uppercase tracking-wider">Fokus</span>
          </div>
          <p class="text-sm font-bold text-[#171a1f]">Tulis Dasar</p>
          <p class="text-xs text-[#565d6d] mt-1">Perlu latihan rutin penulisan huruf</p>
        </div>
      </div>
    </div>

    <!-- Right: Radar Chart -->
    <div class="bg-[#F1F6FE]/30 border border-[#3d8af5]/10 rounded-2xl p-6 flex flex-col items-center justify-center min-h-[280px] relative">
      <h3 class="text-sm font-bold text-[#565d6d] mb-4 font-poppins">Peta Kompetensi</h3>
      <div class="w-full max-w-[240px] h-[200px]">
        <canvas id="raporRadarChart"></canvas>
      </div>
    </div>
  </div>

  <!-- Detail Table -->
  <div class="px-8 pb-6">
    <h3 class="text-base font-bold text-[#171a1f] mb-4 font-poppins">Detail Progres Materi</h3>
    <div class="overflow-x-auto rounded-xl border border-[#dee1e6]">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-[#f3f4f6]/60 border-b border-[#dee1e6] text-xs font-bold text-[#565d6d] uppercase tracking-wider">
            <th class="px-5 py-3">Aspek</th>
            <th class="px-5 py-3">Materi</th>
            <th class="px-5 py-3 text-center">Status</th>
            <th class="px-5 py-3">Catatan</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#dee1e6]">
          @php
            $detail = [
              ['aspek' => 'Baca',   'materi' => 'Huruf Vokal (A-I-U-E-O)',    'status' => 'T', 'label' => 'T - Terampil', 'badge' => 'bg-[#A7F3D0] text-[#047857]', 'catatan' => 'Sangat baik, dapat membaca mandiri'],
              ['aspek' => 'Baca',   'materi' => 'Kata Sederhana',              'status' => 'P', 'label' => 'P - Paham',    'badge' => 'bg-[#BAE6FD] text-[#0369A1]', 'catatan' => 'Mengerti namun masih perlu latihan'],
              ['aspek' => 'Hitung', 'materi' => 'Angka 1-10',                  'status' => 'T', 'label' => 'T - Terampil', 'badge' => 'bg-[#A7F3D0] text-[#047857]', 'catatan' => 'Menguasai dengan baik'],
              ['aspek' => 'Hitung', 'materi' => 'Penjumlahan Dasar',           'status' => 'B', 'label' => 'B - Belum',    'badge' => 'bg-[#FDE68A] text-[#B45309]', 'catatan' => 'Perlu bimbingan lebih lanjut'],
              ['aspek' => 'Tulis',  'materi' => 'Huruf Kapital',               'status' => 'P', 'label' => 'P - Paham',    'badge' => 'bg-[#BAE6FD] text-[#0369A1]', 'catatan' => 'Sudah memahami bentuk huruf'],
            ];
          @endphp
          @foreach($detail as $d)
          <tr class="hover:bg-gray-50/50">
            <td class="px-5 py-3.5">
              <span class="px-2.5 py-0.5 bg-[#F1F6FE] text-[#3d8af5] rounded-full text-xs font-bold">{{ $d['aspek'] }}</span>
            </td>
            <td class="px-5 py-3.5 text-sm font-medium text-[#171a1f]">{{ $d['materi'] }}</td>
            <td class="px-5 py-3.5 text-center">
              <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-bold {{ $d['badge'] }}">{{ $d['label'] }}</span>
            </td>
            <td class="px-5 py-3.5 text-sm text-[#565d6d]">{{ $d['catatan'] }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <!-- Legend -->
    <div class="flex flex-wrap gap-4 mt-4 text-xs text-[#565d6d]">
      <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-[#E2E8F0] border border-[#CBD5E1] inline-block"></span> K = Kenal</span>
      <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-[#FDE68A] border border-[#FCD34D] inline-block"></span> B = Belum</span>
      <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-[#BAE6FD] border border-[#7DD3FC] inline-block"></span> P = Paham</span>
      <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-[#A7F3D0] border border-[#6EE7B7] inline-block"></span> T = Terampil</span>
    </div>
  </div>

  <!-- Signature & Footer Quote -->
  <div class="px-8 pb-8 grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-[#dee1e6] pt-6">
    <div>
      <p class="text-sm text-[#565d6d] italic leading-relaxed">
        <em>"Tujuan utama bukan hanya bisa baca, tapi MINAT baca yang tumbuh sepanjang hayat."</em>
      </p>
    </div>
    <div class="text-right">
      <p class="text-xs text-[#565d6d] mb-8">Tanda tangan Guru,</p>
      <p class="text-sm font-bold text-[#171a1f] border-t border-[#171a1f] pt-2 inline-block">Ibu Guru Maya</p>
    </div>
  </div>
</div>

<!-- Quick Action Cards (no-print) -->
<div class="no-print grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-[1088px] mx-auto">
  <a href="{{ route('guru.grafik') }}" class="flex items-center gap-4 p-5 border-2 border-dashed border-[#dee1e6] rounded-2xl hover:border-[#3d8af5] hover:bg-[#F1F6FE]/30 group">
    <div class="w-10 h-10 bg-[#3d8af5]/10 rounded-xl flex items-center justify-center group-hover:bg-[#3d8af5]/20">
      <iconify-icon icon="lucide:bar-chart-2" width="20" class="text-[#3d8af5]"></iconify-icon>
    </div>
    <div>
      <p class="text-sm font-semibold text-[#171a1f]">Lihat Grafik Lengkap</p>
      <p class="text-xs text-[#565d6d]">Visualisasi perkembangan semua murid</p>
    </div>
    <iconify-icon icon="lucide:arrow-right" width="16" class="text-[#565d6d] ml-auto group-hover:text-[#3d8af5]"></iconify-icon>
  </a>
  <a href="{{ route('guru.nilai') }}" class="flex items-center gap-4 p-5 border-2 border-dashed border-[#dee1e6] rounded-2xl hover:border-[#63e98f] hover:bg-[#DCFAE6]/30 group">
    <div class="w-10 h-10 bg-[#63e98f]/10 rounded-xl flex items-center justify-center group-hover:bg-[#63e98f]/20">
      <iconify-icon icon="lucide:file-edit" width="20" class="text-[#047857]"></iconify-icon>
    </div>
    <div>
      <p class="text-sm font-semibold text-[#171a1f]">Update Nilai Baru</p>
      <p class="text-xs text-[#565d6d]">Input progres materi terbaru murid</p>
    </div>
    <iconify-icon icon="lucide:arrow-right" width="16" class="text-[#565d6d] ml-auto group-hover:text-[#047857]"></iconify-icon>
  </a>
</div>
@endsection

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
@endpush

@push('scripts')
<script>
const radarCtx = document.getElementById('raporRadarChart').getContext('2d');
new Chart(radarCtx, {
  type: 'radar',
  data: {
    labels: ['Membaca', 'Menulis', 'Berhitung', 'Minat Belajar'],
    datasets: [{
      label: 'Kompetensi',
      data: [85, 55, 70, 92],
      borderColor: '#3d8af5',
      backgroundColor: 'rgba(61,138,245,0.15)',
      borderWidth: 2,
      pointRadius: 4,
      pointBackgroundColor: '#3d8af5',
      pointHoverRadius: 6
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      r: {
        beginAtZero: true,
        max: 100,
        ticks: { stepSize: 25, font: { size: 9, family: 'Roboto' }, color: '#9095a0', backdropColor: 'transparent' },
        grid: { color: 'rgba(61,138,245,0.12)' },
        angleLines: { color: 'rgba(61,138,245,0.12)' },
        pointLabels: { font: { size: 10, family: 'Inter', weight: 600 }, color: '#565d6d' }
      }
    }
  }
});
</script>
@endpush
