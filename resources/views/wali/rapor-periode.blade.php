@extends('layouts.wali')

@section('title')
Laporan Rapor Periode {{ $periodInfo['number'] ?? '' }} - E-Rapor BiMBA AIUEO
@endsection

@section('page-title')
Laporan Rapor Periode {{ $periodInfo['number'] ?? '' }}
@endsection

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
  .status-K { background: #f1f5f9; color: #475569; font-weight: 700; }

  @media print {
    .no-print { display: none !important; }
    body { background: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .rapor-sheet { box-shadow: none !important; border: none !important; margin: 0 !important; padding: 40px !important; }
    .rapor-table th, .rapor-table td { padding: 6px 10px; }
    @page { size: A4; margin: 15mm; }
  }
</style>
<script>
  // Auto print when page loads (optional)
  window.addEventListener('load', function() {
    // Uncomment next line if you want auto-print
    // setTimeout(() => window.print(), 500);
  });
</script>
@endpush

@section('content')
<!-- Page Header (no-print) -->
<div class="no-print flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
  <div>
    <h1 class="text-2xl font-bold text-[#171a1f] font-poppins">Laporan Rapor - Periode {{ $periodInfo['number'] }}</h1>
    <p class="text-sm text-[#565d6d]">{{ $periodInfo['start'] }} - {{ $periodInfo['end'] }}</p>
  </div>
  <div class="flex flex-wrap gap-2">
    <a href="{{ route('wali.riwayat', ['student_id' => $student->id]) }}" class="flex items-center gap-2 px-4 py-2.5 border border-[#dee1e6] bg-white rounded-xl text-sm font-medium text-[#171a1f] hover:bg-gray-50">
      <iconify-icon icon="lucide:arrow-left" width="16" class="text-[#3d8af5]"></iconify-icon>
      Kembali ke Riwayat
    </a>
    <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2.5 bg-[#3d8af5] text-white rounded-xl text-sm font-medium hover:bg-blue-600">
      <iconify-icon icon="lucide:printer" width="16"></iconify-icon>
      Cetak Rapor
    </button>
  </div>
</div>

@php
  $bacaPct = $reportData['baca']['percentage'] ?? 0;
  $tulisPct = $reportData['tulis']['percentage'] ?? 0;
  $hitungPct = $reportData['hitung']['percentage'] ?? 0;
  $avgPct = round(($bacaPct + $tulisPct + $hitungPct) / 3);

  $skills = ['Membaca' => $bacaPct, 'Menulis' => $tulisPct, 'Berhitung' => $hitungPct];
  $highest = array_keys($skills, max($skills))[0] ?? 'Membaca';
  $focus = array_keys($skills, min($skills))[0] ?? 'Membaca';

  $statusLabel = fn($s) => match($s) { 'T' => 'Terampil', 'P' => 'Paham', default => 'Kenal' };
@endphp

<!-- ==================== FORMAL REPORT CARD ==================== -->
<div class="rapor-sheet max-w-[850px] mx-auto bg-white rounded-lg shadow-lg border border-[#cbd5e1] p-10 mb-6" style="font-family: 'Times New Roman', Times, serif;">

  <!-- Report Title -->
  <div class="text-center mb-6">
    <h1 class="text-xl font-bold uppercase tracking-wide" style="letter-spacing: 2px;">Ringkasan Laporan Hasil Belajar Murid</h1>
    <div class="w-24 h-1 bg-[#1e293b] mx-auto mt-2"></div>
    <p class="mt-3 text-sm font-semibold" style="color: #dc2626;">PERIODE {{ $periodInfo['number'] }}</p>
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
        <td>{{ $student->classroom?->level ?? '-' }}</td>
      </tr>
      <tr>
        <td></td>
        <td></td>
        <td></td>
        <td>Periode Rapor</td>
        <td>:</td>
        <td><strong>{{ $periodInfo['start'] }} - {{ $periodInfo['end'] }}</strong></td>
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
      @if($reportData['baca']['by_level'] && $reportData['baca']['by_level']->count() > 0)
        @foreach($reportData['baca']['by_level']->sortKeys() as $level => $progresses)
          @foreach($progresses->sortBy('material.sort_order') as $prog)
          @php $globalNo++; @endphp
          <tr>
            <td class="text-center">{{ $globalNo }}</td>
            <td>{{ $prog->material->name }}</td>
            <td class="text-center">{{ preg_replace('/ - .*$/', '', $prog->material->level) }}</td>
            <td class="text-center status-{{ $prog->status }}">{{ $prog->status }}</td>
            <td>{{ $statusLabel($prog->status) }}</td>
          </tr>
          @endforeach
        @endforeach
      @else
        <tr>
          <td colspan="5" class="text-center" style="color: #9095a0; font-style: italic;">Belum ada data pada periode ini</td>
        </tr>
      @endif

      {{-- MENULIS --}}
      @php $globalNo = 0; @endphp
      <tr class="group-header">
        <td colspan="4"><strong>Menulis</strong></td>
        <td class="text-center"><strong>{{ round($tulisPct) }}%</strong></td>
      </tr>
      @if($reportData['tulis']['by_level'] && $reportData['tulis']['by_level']->count() > 0)
        @foreach($reportData['tulis']['by_level']->sortKeys() as $level => $progresses)
          @foreach($progresses->sortBy('material.sort_order') as $prog)
          @php $globalNo++; @endphp
          <tr>
            <td class="text-center">{{ $globalNo }}</td>
            <td>{{ $prog->material->name }}</td>
            <td class="text-center">{{ preg_replace('/ - .*$/', '', $prog->material->level) }}</td>
            <td class="text-center status-{{ $prog->status }}">{{ $prog->status }}</td>
            <td>{{ $statusLabel($prog->status) }}</td>
          </tr>
          @endforeach
        @endforeach
      @else
        <tr>
          <td colspan="5" class="text-center" style="color: #9095a0; font-style: italic;">Belum ada data pada periode ini</td>
        </tr>
      @endif

      {{-- BERHITUNG --}}
      @php $globalNo = 0; @endphp
      <tr class="group-header">
        <td colspan="4"><strong>Berhitung</strong></td>
        <td class="text-center"><strong>{{ round($hitungPct) }}%</strong></td>
      </tr>
      @if($reportData['hitung']['by_level'] && $reportData['hitung']['by_level']->count() > 0)
        @foreach($reportData['hitung']['by_level']->sortKeys() as $level => $progresses)
          @foreach($progresses->sortBy('material.sort_order') as $prog)
          @php $globalNo++; @endphp
          <tr>
            <td class="text-center">{{ $globalNo }}</td>
            <td>{{ $prog->material->name }}</td>
            <td class="text-center">{{ preg_replace('/ - .*$/', '', $prog->material->level) }}</td>
            <td class="text-center status-{{ $prog->status }}">{{ $prog->status }}</td>
            <td>{{ $statusLabel($prog->status) }}</td>
          </tr>
          @endforeach
        @endforeach
      @else
        <tr>
          <td colspan="5" class="text-center" style="color: #9095a0; font-style: italic;">Belum ada data pada periode ini</td>
        </tr>
      @endif

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
    <strong>Catatan Perkembangan Periode {{ $periodInfo['number'] }}:</strong>
    <p class="mt-1" style="line-height: 1.6;">
      <strong>{{ $student->name }}</strong> menunjukkan perkembangan yang
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
      <p>{{ \Carbon\Carbon::parse($periodInfo['end'])->translatedFormat('d F Y') }}</p>
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
    <p>Rapor Periode {{ $periodInfo['number'] }} ({{ $periodInfo['start'] }} - {{ $periodInfo['end'] }})</p>
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

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const radarCtx = document.getElementById('waliRaporRadarChart');
  if (!radarCtx) return;
  
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
        pointBorderColor: '#fff',
        pointBorderWidth: 2
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      scales: {
        r: {
          beginAtZero: true,
          max: 100,
          ticks: {
            stepSize: 25,
            font: { size: 10 },
            callback: function(value) {
              return value + '%';
            }
          },
          grid: { color: '#e2e8f0' },
          angleLines: { color: '#e2e8f0' },
          pointLabels: {
            font: { size: 11, weight: '600' },
            color: '#1e293b'
          }
        }
      },
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: function(context) {
              return context.label + ': ' + context.parsed.r + '%';
            }
          }
        }
      }
    }
  });
});
</script>
@endpush
