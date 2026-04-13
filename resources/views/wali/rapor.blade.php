@extends('layouts.wali')

@section('title', 'Laporan Rapor - E-Rapor BiMBA AIUEO')
@section('page-title', 'Laporan Rapor')

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
  .status-T { background: #dcfce7; color: #166534; font-weight: 700; }
  .status-P { background: #dbeafe; color: #1e40af; font-weight: 700; }
  .status-B { background: #fef9c3; color: #92400e; font-weight: 700; }
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
    <h1 class="text-2xl font-bold text-[#171a1f] font-poppins">Laporan Rapor</h1>
    <p class="text-sm text-[#565d6d]">Pantau progres belajar Ananda secara berkala</p>
  </div>
  <div class="flex flex-wrap gap-2">
    <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2.5 border border-[#dee1e6] bg-white rounded-xl text-sm font-medium text-[#171a1f] hover:bg-gray-50">
      <iconify-icon icon="lucide:printer" width="16" class="text-[#3d8af5]"></iconify-icon>
      Cetak Rapor
    </button>
  </div>
</div>

<!-- Child Selector (no-print) -->
<form method="GET" action="{{ route('wali.rapor') }}" class="no-print bg-white rounded-2xl border border-[#dee1e6] main-shadow p-5 mb-6 grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
  <div class="md:col-span-8 space-y-1.5">
    <label class="text-xs font-semibold text-[#565d6d] uppercase tracking-wider">Pilih Anak</label>
    <div class="relative">
      <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-[#565d6d]">
        <iconify-icon icon="lucide:user" width="16"></iconify-icon>
      </div>
      <select name="student_id" class="w-full pl-9 pr-10 py-2.5 border border-[#dee1e6] rounded-xl text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20 bg-white">
        @foreach($children as $c)
          <option value="{{ $c->id }}" {{ $student?->id == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->classroom?->name ?? '-' }})</option>
        @endforeach
      </select>
      <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-[#565d6d]">
        <iconify-icon icon="lucide:chevron-down" width="14"></iconify-icon>
      </div>
    </div>
  </div>
  <div class="md:col-span-4">
    <button type="submit" class="w-full py-2.5 bg-[#3d8af5] text-white rounded-xl text-sm font-medium shadow-md hover:bg-blue-600 flex items-center justify-center gap-2">
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

  $statusLabel = fn($s) => match($s) { 'T' => 'Terampil', 'P' => 'Paham', 'B' => 'Belum', default => 'Kenal' };
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
        <td>Kelas</td>
        <td>:</td>
        <td>{{ $student->classroom?->name ?? '-' }}</td>
      </tr>
      <tr>
        <td>Tanggal Lahir</td>
        <td>:</td>
        <td>{{ $student->birth_date?->translatedFormat('d F Y') ?? '-' }}</td>
        <td>Level</td>
        <td>:</td>
        <td>{{ $student->classroom?->level ?? '-' }}</td>
      </tr>
      <tr>
        <td>Jenis Kelamin</td>
        <td>:</td>
        <td>{{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
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

      {{-- KELOMPOK A: MEMBACA --}}
      <tr class="group-header">
        <td colspan="4"><strong>Kelompok A — Membaca</strong></td>
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

      {{-- KELOMPOK B: MENULIS --}}
      @php $globalNo = 0; @endphp
      <tr class="group-header">
        <td colspan="4"><strong>Kelompok B — Menulis</strong></td>
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

      {{-- KELOMPOK C: BERHITUNG --}}
      @php $globalNo = 0; @endphp
      <tr class="group-header">
        <td colspan="4"><strong>Kelompok C — Berhitung</strong></td>
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
    <span class="ml-2">K = Kenal</span> &nbsp;|&nbsp;
    <span>B = Belum</span> &nbsp;|&nbsp;
    <span>P = Paham</span> &nbsp;|&nbsp;
    <span>T = Terampil</span>
  </div>

  <!-- Analysis Section -->
  <div class="border border-[#1e293b] rounded p-4 mb-6" style="font-size: 13px;">
    <strong>Catatan Perkembangan:</strong>
    <p class="mt-1" style="line-height: 1.6;">
      Ananda <strong>{{ $student->name }}</strong> menunjukkan perkembangan yang
      @if($avgPct >= 70) sangat positif @elseif($avgPct >= 40) cukup baik @else perlu ditingkatkan @endif
      dengan rata-rata penguasaan <strong>{{ $avgPct }}%</strong>.
      Aspek terkuat pada bidang <strong>{{ $highest }}</strong> ({{ $skills[$highest] }}%)
      dan fokus pengembangan pada bidang <strong>{{ $focus }}</strong> ({{ $skills[$focus] }}%).
    </p>
  </div>

  <!-- Radar Chart -->
  <div class="flex justify-center mb-6">
    <div style="width: 280px; height: 220px;">
      <canvas id="waliRaporRadarChart"></canvas>
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
        <p class="mt-1"><strong>{{ auth()->user()->name }}</strong></p>
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

<!-- Summary Cards (no-print) -->
<div class="no-print max-w-[850px] mx-auto grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
  @foreach(['baca' => ['Membaca', 'lucide:book-open', '#22C55E'], 'tulis' => ['Menulis', 'lucide:pencil', '#3d8af5'], 'hitung' => ['Berhitung', 'lucide:calculator', '#f59e0b']] as $skillKey => [$label, $icon, $color])
  <div class="bg-white rounded-2xl border border-[#dee1e6] p-5 shadow-sm">
    <div class="flex items-center gap-3 mb-3">
      <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: {{ $color }}15;">
        <iconify-icon icon="{{ $icon }}" width="20" style="color: {{ $color }};"></iconify-icon>
      </div>
      <h4 class="text-sm font-bold text-[#171a1f]">{{ $label }}</h4>
    </div>
    <div class="flex items-end justify-between">
      <span class="text-2xl font-black text-[#171a1f]">{{ round($reportData[$skillKey]['percentage']) }}%</span>
      <span class="text-xs text-[#565d6d]">{{ $reportData[$skillKey]['details']->where('status', 'T')->count() }}/{{ $reportData[$skillKey]['details']->count() }} Terampil</span>
    </div>
    <div class="w-full h-2 bg-gray-100 rounded-full mt-3 overflow-hidden">
      <div class="h-full rounded-full" style="width: {{ $reportData[$skillKey]['percentage'] }}%; background: {{ $color }};"></div>
    </div>
  </div>
  @endforeach
</div>

<!-- Panduan Penilaian (no-print) -->
<div class="no-print max-w-[850px] mx-auto bg-white rounded-2xl border border-[#dee1e6] p-6 shadow-sm">
  <div class="flex items-center gap-2 mb-4">
    <iconify-icon icon="lucide:info" width="18" class="text-[#3d8af5]"></iconify-icon>
    <h3 class="text-sm font-bold text-[#171a1f]">Panduan Penilaian BiMBA AIUEO</h3>
  </div>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
    <div class="bg-[#f1f5f9] border border-[#e2e8f0] rounded-xl p-3 text-center">
      <div class="w-8 h-8 bg-[#475569]/10 rounded-full flex items-center justify-center mx-auto mb-2">
        <span class="text-[#475569] font-bold text-sm">K</span>
      </div>
      <p class="text-xs font-bold text-[#171a1f]">Kenal</p>
      <p class="text-[10px] text-[#565d6d]">Tahap pengenalan awal</p>
    </div>
    <div class="bg-[#fef9c3] border border-[#fde68a] rounded-xl p-3 text-center">
      <div class="w-8 h-8 bg-[#92400e]/10 rounded-full flex items-center justify-center mx-auto mb-2">
        <span class="text-[#92400e] font-bold text-sm">B</span>
      </div>
      <p class="text-xs font-bold text-[#171a1f]">Belum</p>
      <p class="text-[10px] text-[#565d6d]">Belum menguasai konsep</p>
    </div>
    <div class="bg-[#dbeafe] border border-[#93c5fd] rounded-xl p-3 text-center">
      <div class="w-8 h-8 bg-[#1e40af]/10 rounded-full flex items-center justify-center mx-auto mb-2">
        <span class="text-[#1e40af] font-bold text-sm">P</span>
      </div>
      <p class="text-xs font-bold text-[#171a1f]">Paham</p>
      <p class="text-[10px] text-[#565d6d]">Memahami konsep materi</p>
    </div>
    <div class="bg-[#dcfce7] border border-[#86efac] rounded-xl p-3 text-center">
      <div class="w-8 h-8 bg-[#166534]/10 rounded-full flex items-center justify-center mx-auto mb-2">
        <span class="text-[#166534] font-bold text-sm">T</span>
      </div>
      <p class="text-xs font-bold text-[#171a1f]">Terampil</p>
      <p class="text-[10px] text-[#565d6d]">Mahir dan mandiri</p>
    </div>
  </div>
</div>

@else
<div class="bg-white rounded-2xl border border-[#dee1e6] main-shadow p-12 text-center">
  <iconify-icon icon="lucide:file-text" width="48" class="text-[#dee1e6] mx-auto mb-4"></iconify-icon>
  <h3 class="text-lg font-bold text-[#171a1f] mb-2">Pilih Anak</h3>
  <p class="text-sm text-[#565d6d]">Silakan pilih anak di atas untuk menampilkan rapor.</p>
</div>
@endif
@endsection

@if($student && $reportData)
@push('scripts')
<script>
const radarCtx = document.getElementById('waliRaporRadarChart').getContext('2d');
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
