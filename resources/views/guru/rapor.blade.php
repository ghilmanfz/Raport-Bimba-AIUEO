@extends('layouts.guru')

@section('title', 'Cetak Rapor Digital - E-Rapor BiMBA')
@section('page-title', 'Cetak Rapor Digital')

@push('head')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<style>
  .font-poppins { font-family: 'Poppins', sans-serif; }
  .rapor-table { border-collapse: collapse; width: 100%; }
  .rapor-table th,
  .rapor-table td { border: 1.5px solid #1e293b; padding: 8px 12px; font-size: 13px; }
  .rapor-table th { background: #f1f5f9; font-weight: 700; text-align: center; }
  .rapor-header-table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
  .rapor-header-table td { padding: 3px 8px; font-size: 13px; border: none; vertical-align: top; }
  .group-header td { background: #e2e8f0; font-weight: 700; font-size: 13px; }
  .status-T { background: #FEE2E2; color: #991B1B; font-weight: 700; }
  .status-P { background: #dbeafe; color: #1e40af; font-weight: 700; }
  .status-K { background: #f1f5f9; color: #475569; font-weight: 700; }

  @media print {
    .no-print { display: none !important; }
    body { background: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .rapor-sheet { box-shadow: none !important; border: none !important; margin: 0 !important; padding: 40px !important; }
    .rapor-table th, .rapor-table td { padding: 6px 10px; }
    @page { size: A4; margin: 15mm; }
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
    <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2.5 border border-[#dee1e6] bg-white rounded-xl text-sm font-medium text-[#171a1f] hover:bg-gray-50">
      <iconify-icon icon="lucide:printer" width="16" class="text-[#2563EB]"></iconify-icon>
      Cetak
    </button>
  </div>
</div>

<!-- Student Selector (no-print) -->
<form method="GET" action="{{ route('guru.rapor') }}" class="no-print bg-white rounded-2xl border border-[#dee1e6] main-shadow p-5 mb-6 grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
  <div class="md:col-span-8 space-y-1.5">
    <label class="text-xs font-semibold text-[#565d6d] uppercase tracking-wider">Pilih Murid</label>
    <div class="relative">
      <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-[#565d6d]">
        <iconify-icon icon="lucide:user" width="16"></iconify-icon>
      </div>
      <select name="student_id" class="w-full pl-9 pr-10 py-2.5 border border-[#dee1e6] rounded-xl text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20 bg-white">
        @foreach($students as $s)
          <option value="{{ $s->id }}" {{ $student?->id == $s->id ? 'selected' : '' }}>{{ $s->name }} ({{ $s->classroom?->name ?? '-' }})</option>
        @endforeach
      </select>
      <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-[#565d6d]">
        <iconify-icon icon="lucide:chevron-down" width="14"></iconify-icon>
      </div>
    </div>
  </div>
  <div class="md:col-span-4">
    <button type="submit" class="w-full py-2.5 bg-[#2563EB] text-white rounded-xl text-sm font-medium shadow-md hover:bg-blue-600 flex items-center justify-center gap-2">
      <iconify-icon icon="lucide:search" width="16"></iconify-icon>
      Tampilkan
    </button>
  </div>
</form>

@if($student && $reportData)
@php
  $bacaPct = $reportData['baca']['percentage'];
  $tulisPct = $reportData['tulis']['percentage'];
  $hitungPct = $reportData['hitung']['percentage'];
  $avgPct = round(($bacaPct + $tulisPct + $hitungPct) / 3);

  $skills = ['Membaca' => $bacaPct, 'Menulis' => $tulisPct, 'Berhitung' => $hitungPct];
  $highest = array_keys($skills, max($skills))[0];
  $focus = array_keys($skills, min($skills))[0];

  $statusLabel = fn($s) => match($s) { 'T' => 'Terampil', 'P' => 'Paham', default => 'Kenal (Pengenalan)' };
  $statusValue = fn($s) => match($s) { 'T' => 3, 'P' => 2, default => 1 };
@endphp

<!-- ==================== FORMAL REPORT CARD ==================== -->
<div class="rapor-sheet max-w-[850px] mx-auto bg-white rounded-lg shadow-lg border border-[#cbd5e1] p-10 mb-6" style="font-family: 'Times New Roman', Times, serif;">

  <!-- Report Title -->
  <div class="text-center mb-6">
    <h1 class="text-xl font-bold uppercase tracking-wide" style="letter-spacing: 2px;">Ringkasan Laporan Hasil Belajar Murid</h1>
    <div class="w-24 h-1 bg-[#1e293b] mx-auto mt-2"></div>
  </div>

  <!-- Institution Header -->
  <div class="text-center mb-6">
    <h2 class="text-lg font-bold uppercase">{{ $institutionName }}</h2>
    @if($institutionAddress)
      <p class="text-sm text-[#475569] mt-0.5">{{ $institutionAddress }}</p>
    @endif
  </div>

  <!-- Student Identity Table -->
  <table class="rapor-header-table">
    <tbody>
      <tr>
        <td style="width:15%;">Nama</td>
        <td style="width:2%;">:</td>
        <td style="width:33%;"><strong>{{ $student->name }}</strong></td>
        <td style="width:15%;">Lembaga</td>
        <td style="width:2%;">:</td>
        <td style="width:33%;">{{ $institutionName }}</td>
      </tr>
      <tr>
        <td>NIS</td>
        <td>:</td>
        <td><strong>{{ $student->nis }}</strong></td>
        <td>Unit</td>
        <td>:</td>
        <td>{{ $unitName ?: '-' }}</td>
      </tr>
      <tr>
        <td>Tanggal Lahir</td>
        <td>:</td>
        <td>{{ $student->birth_date?->translatedFormat('d F Y') ?? '-' }}</td>
        <td>Kelas</td>
        <td>:</td>
        <td>{{ $student->classroom?->name ?? '-' }}</td>
      </tr>
      <tr>
        <td>Jenis Kelamin</td>
        <td>:</td>
        <td>{{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
        <td>Level</td>
        <td>:</td>
        <td>{{ preg_replace('/ - .*$/', '', $student->classroom?->level ?? '-') }}</td>
      </tr>
      <tr>
        <td></td>
        <td></td>
        <td></td>
        <td>Periode</td>
        <td>:</td>
        <td>{{ now()->translatedFormat('F Y') }}</td>
      </tr>
    </tbody>
  </table>

  <!-- Main Grades Table -->
  <table class="rapor-table">
    <thead>
      <tr>
        <th style="width:6%;">No</th>
        <th style="width:42%;">Materi Pembelajaran</th>
        <th style="width:14%;">Level</th>
        <th style="width:14%;">Status</th>
        <th style="width:24%;">Keterangan</th>
      </tr>
    </thead>
    <tbody>
      @php $globalNo = 0; @endphp

      {{-- MEMBACA --}}
      <tr class="group-header">
        <td colspan="4"><strong>Membaca</strong></td>
        <td class="text-center"><strong>{{ round($bacaPct) }}%</strong></td>
      </tr>
      @foreach($reportData['baca']['by_level']->sortKeys() as $level => $progresses)
        @foreach($progresses->sortBy('material.sort_order') as $prog)
        @php $globalNo++; @endphp
        <tr>
          <td class="text-center">{{ $globalNo }}</td>
          <td>{{ $prog->material->name }}</td>
          <td class="text-center">{{ $prog->material->level }}</td>
          <td class="text-center status-{{ $prog->status }}">{{ $prog->status }}</td>
          <td>{{ $statusLabel($prog->status) }}</td>
        </tr>
        @endforeach
      @endforeach

      {{-- MENULIS --}}
      @php $globalNo = 0; @endphp
      <tr class="group-header">
        <td colspan="4"><strong>Menulis</strong></td>
        <td class="text-center"><strong>{{ round($tulisPct) }}%</strong></td>
      </tr>
      @foreach($reportData['tulis']['by_level']->sortKeys() as $level => $progresses)
        @foreach($progresses->sortBy('material.sort_order') as $prog)
        @php $globalNo++; @endphp
        <tr>
          <td class="text-center">{{ $globalNo }}</td>
          <td>{{ $prog->material->name }}</td>
          <td class="text-center">{{ $prog->material->level }}</td>
          <td class="text-center status-{{ $prog->status }}">{{ $prog->status }}</td>
          <td>{{ $statusLabel($prog->status) }}</td>
        </tr>
        @endforeach
      @endforeach

      {{-- BERHITUNG --}}
      @php $globalNo = 0; @endphp
      <tr class="group-header">
        <td colspan="4"><strong>Berhitung</strong></td>
        <td class="text-center"><strong>{{ round($hitungPct) }}%</strong></td>
      </tr>
      @foreach($reportData['hitung']['by_level']->sortKeys() as $level => $progresses)
        @foreach($progresses->sortBy('material.sort_order') as $prog)
        @php $globalNo++; @endphp
        <tr>
          <td class="text-center">{{ $globalNo }}</td>
          <td>{{ $prog->material->name }}</td>
          <td class="text-center">{{ $prog->material->level }}</td>
          <td class="text-center status-{{ $prog->status }}">{{ $prog->status }}</td>
          <td>{{ $statusLabel($prog->status) }}</td>
        </tr>
        @endforeach
      @endforeach

      {{-- RATA-RATA --}}
      <tr style="background: #e2e8f0;">
        <td colspan="4" class="text-center"><strong>Rata-Rata Penguasaan</strong></td>
        <td class="text-center"><strong>{{ $avgPct }}%</strong></td>
      </tr>
    </tbody>
  </table>

  <!-- Status Legend -->
  <div class="mt-4 mb-6" style="font-size: 12px; color: #475569;">
    <strong>Keterangan Status:</strong>
    <span class="ml-2">K = Kenal (Pengenalan)</span> &nbsp;|&nbsp;
    <span>P = Paham (Pemahaman)</span> &nbsp;|&nbsp;
    <span>T = Terampil (Pembiasaan)</span>
  </div>

  <!-- Analysis Section -->
  <div class="border border-[#1e293b] rounded p-4 mb-6" style="font-size: 13px;">
    <strong>Catatan Perkembangan:</strong>
    <p class="mt-1" style="line-height: 1.6;">
      <strong>{{ $student->name }}</strong> menunjukkan perkembangan yang
      @if($avgPct >= 70) sangat positif @elseif($avgPct >= 40) cukup baik @else perlu ditingkatkan @endif
      dengan rata-rata penguasaan <strong>{{ $avgPct }}%</strong>.
      Aspek terkuat pada bidang <strong>{{ $highest }}</strong> ({{ $skills[$highest] }}%)
      dan fokus pengembangan pada bidang <strong>{{ $focus }}</strong> ({{ $skills[$focus] }}%).
    </p>
    @if($student->development_notes)
    <p class="mt-2" style="line-height: 1.6;"><strong>Catatan Guru:</strong> {{ $student->development_notes }}</p>
    @endif
  </div>

  <!-- Manual Notes Form (no-print) -->
  <div class="no-print border border-[#dee1e6] rounded-lg p-4 mb-6">
    <form method="POST" action="{{ route('guru.rapor.notes') }}">
      @csrf
      <input type="hidden" name="student_id" value="{{ $student->id }}">
      <label class="block text-sm font-bold text-[#171a1f] mb-2">Catatan Perkembangan Manual</label>
      <textarea name="development_notes" rows="3" class="w-full px-3 py-2 border border-[#dee1e6] rounded-lg text-sm focus:ring-1 focus:ring-[#2563EB] outline-none" placeholder="Tulis catatan perkembangan murid di sini...">{{ $student->development_notes }}</textarea>
      <button type="submit" class="mt-2 px-4 py-2 bg-[#2563EB] text-white rounded-lg text-sm font-medium hover:bg-blue-600">
        <iconify-icon icon="lucide:save" width="14" class="inline mr-1"></iconify-icon>
        Simpan Catatan
      </button>
    </form>
  </div>

  <!-- Radar Chart -->
  <div class="flex justify-center mb-6">
    <div style="width: 280px; height: 220px;">
      <canvas id="raporRadarChart"></canvas>
    </div>
  </div>

  <!-- Signature Section -->
  <div class="grid grid-cols-2 gap-8 mt-10" style="font-size: 13px;">
    <div class="text-center">
      <p>Mengetahui,</p>
      <p>Orang Tua / Wali Murid</p>
      @if($qrCodeBase64)
      <div class="my-3 flex justify-center">
        <div class="p-2 bg-white border border-[#dee1e6] rounded-lg shadow-sm">
          <img src="data:image/svg+xml;base64,{{ $qrCodeBase64 }}" alt="QR Code Rapor" style="width: 100px; height: 100px;">
        </div>
      </div>
      @endif
      <div class="mt-4 mb-1">
        <div class="border-b border-[#1e293b] mx-auto" style="width: 180px;"></div>
      </div>
    </div>
    <div class="text-center">
      <p>{{ now()->translatedFormat('d F Y') }}</p>
      <p>Guru Pengajar</p>
      @if($qrCodeBase64)
      <div class="my-3 flex justify-center">
        <div class="p-2 bg-white border border-[#dee1e6] rounded-lg shadow-sm">
          <img src="data:image/svg+xml;base64,{{ $qrCodeBase64 }}" alt="QR Code Rapor" style="width: 100px; height: 100px;">
        </div>
      </div>
      @endif
      <div class="mt-4 mb-1">
        <div class="border-b border-[#1e293b] mx-auto" style="width: 180px;"></div>
        <p class="mt-1"><strong>{{ auth()->user()->name }}</strong></p>
      </div>
    </div>
  </div>

  @if($qrCodeBase64)
  <div class="text-center mt-4" style="font-size: 11px; color: #64748b;">
    <p>Scan QR code di atas untuk mengunduh rapor digital dalam format PDF</p>
  </div>
  @endif
</div>
<!-- ==================== END FORMAL REPORT CARD ==================== -->

<!-- Perbandingan Rapor Sebelumnya (no-print) -->
@if($prevReportData)
<div class="no-print max-w-[850px] mx-auto bg-white rounded-2xl border border-[#dee1e6] p-6 shadow-sm mb-4">
  <div class="flex items-center gap-2 mb-5">
    <iconify-icon icon="lucide:git-compare" width="18" class="text-[#2563EB]"></iconify-icon>
    <h3 class="text-base font-bold text-[#171a1f]">Perbandingan dengan Periode Sebelumnya</h3>
  </div>
  <p class="text-xs text-[#565d6d] mb-4">Perbandingan pencapaian bulan ini vs bulan {{ now()->subMonth()->translatedFormat('F Y') }}</p>

  <div class="space-y-4">
    @foreach(['baca' => ['Membaca', '#DC2626'], 'tulis' => ['Menulis', '#2563EB'], 'hitung' => ['Berhitung', '#EAB308']] as $skillKey => [$label, $color])
    @php
      $current = round($reportData[$skillKey]['percentage']);
      $prev = round($prevReportData[$skillKey]['percentage']);
      $diff = $current - $prev;
    @endphp
    <div>
      <div class="flex justify-between items-center mb-2">
        <span class="text-sm font-semibold text-[#171a1f]">{{ $label }}</span>
        <div class="flex items-center gap-3 text-sm">
          <span class="text-[#9095a0]">{{ $prev }}%</span>
          <iconify-icon icon="lucide:arrow-right" width="14" class="text-[#9095a0]"></iconify-icon>
          <span class="font-bold text-[#171a1f]">{{ $current }}%</span>
          @if($diff > 0)
            <span class="flex items-center gap-0.5 text-emerald-600 text-xs font-bold bg-emerald-50 px-2 py-0.5 rounded-full">
              <iconify-icon icon="lucide:trending-up" width="12"></iconify-icon> +{{ $diff }}%
            </span>
          @elseif($diff < 0)
            <span class="flex items-center gap-0.5 text-red-500 text-xs font-bold bg-red-50 px-2 py-0.5 rounded-full">
              <iconify-icon icon="lucide:trending-down" width="12"></iconify-icon> {{ $diff }}%
            </span>
          @else
            <span class="flex items-center gap-0.5 text-[#9095a0] text-xs font-bold bg-gray-50 px-2 py-0.5 rounded-full">
              <iconify-icon icon="lucide:minus" width="12"></iconify-icon> 0%
            </span>
          @endif
        </div>
      </div>
      <div class="relative w-full h-3 bg-gray-100 rounded-full overflow-hidden">
        <div class="absolute h-full rounded-full opacity-30" style="width: {{ $prev }}%; background: {{ $color }};"></div>
        <div class="absolute h-full rounded-full" style="width: {{ $current }}%; background: {{ $color }};"></div>
      </div>
      <div class="flex justify-between mt-1">
        <span class="text-[10px] text-[#9095a0]">{{ now()->subMonth()->translatedFormat('F') }}: {{ $prev }}%</span>
        <span class="text-[10px] font-semibold" style="color: {{ $color }};">{{ now()->translatedFormat('F') }}: {{ $current }}%</span>
      </div>
    </div>
    @endforeach
  </div>
</div>
@endif

<!-- Quick Action Cards (no-print) -->
<div class="no-print grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-[850px] mx-auto">
  <a href="{{ route('guru.grafik') }}" class="flex items-center gap-4 p-5 border-2 border-dashed border-[#dee1e6] rounded-2xl hover:border-[#2563EB] hover:bg-[#F1F6FE]/30 group">
    <div class="w-10 h-10 bg-[#2563EB]/10 rounded-xl flex items-center justify-center group-hover:bg-[#2563EB]/20">
      <iconify-icon icon="lucide:bar-chart-2" width="20" class="text-[#2563EB]"></iconify-icon>
    </div>
    <div>
      <p class="text-sm font-semibold text-[#171a1f]">Lihat Grafik Lengkap</p>
      <p class="text-xs text-[#565d6d]">Visualisasi perkembangan semua murid</p>
    </div>
    <iconify-icon icon="lucide:arrow-right" width="16" class="text-[#565d6d] ml-auto group-hover:text-[#2563EB]"></iconify-icon>
  </a>
  <a href="{{ route('guru.nilai') }}" class="flex items-center gap-4 p-5 border-2 border-dashed border-[#dee1e6] rounded-2xl hover:border-[#DC2626] hover:bg-[#FEE2E2]/30 group">
    <div class="w-10 h-10 bg-[#DC2626]/10 rounded-xl flex items-center justify-center group-hover:bg-[#DC2626]/20">
      <iconify-icon icon="lucide:file-edit" width="20" class="text-[#991B1B]"></iconify-icon>
    </div>
    <div>
      <p class="text-sm font-semibold text-[#171a1f]">Update Nilai Baru</p>
      <p class="text-xs text-[#565d6d]">Input progres materi terbaru murid</p>
    </div>
    <iconify-icon icon="lucide:arrow-right" width="16" class="text-[#565d6d] ml-auto group-hover:text-[#991B1B]"></iconify-icon>
  </a>
</div>
@else
<div class="bg-white rounded-2xl border border-[#dee1e6] main-shadow p-12 text-center">
  <iconify-icon icon="lucide:file-text" width="48" class="text-[#dee1e6] mx-auto mb-4"></iconify-icon>
  <h3 class="text-lg font-bold text-[#171a1f] mb-2">Pilih Murid</h3>
  <p class="text-sm text-[#565d6d]">Silakan pilih murid di atas untuk menampilkan rapor.</p>
</div>
@endif
@endsection

@if($student && $reportData)
@push('scripts')
<script>
const radarCtx = document.getElementById('raporRadarChart').getContext('2d');
new Chart(radarCtx, {
  type: 'radar',
  data: {
    labels: ['Membaca', 'Menulis', 'Berhitung'],
    datasets: [{
      label: 'Kompetensi (%)',
      data: [{{ $reportData['baca']['percentage'] }}, {{ $reportData['tulis']['percentage'] }}, {{ $reportData['hitung']['percentage'] }}],
      borderColor: '#1e40af',
      backgroundColor: 'rgba(30,64,175,0.12)',
      borderWidth: 2,
      pointRadius: 5,
      pointBackgroundColor: '#1e40af',
      pointHoverRadius: 7
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: true, position: 'bottom', labels: { font: { size: 11, family: 'sans-serif' } } }
    },
    scales: {
      r: {
        beginAtZero: true,
        max: 100,
        ticks: { stepSize: 25, font: { size: 9 }, color: '#64748b', backdropColor: 'transparent' },
        grid: { color: 'rgba(30,64,175,0.1)' },
        angleLines: { color: 'rgba(30,64,175,0.1)' },
        pointLabels: { font: { size: 11, weight: '600' }, color: '#334155' }
      }
    }
  }
});
</script>
@endpush
@endif
