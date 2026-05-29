@extends('layouts.wali')

@section('title', 'Dashboard - E-Rapor BiMBA AIUEO')
@section('page-title', 'Dashboard')

@section('content')
@php
  $child = $selectedChildData ?? $childrenData->first();
  $studentName = $child ? $child['student']->name : 'Anak';
  $studentInitials = $child ? collect(explode(' ', $child['student']->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('') : '?';
  $classroomName = $child ? ($child['student']->classroom?->name ?? '-') : '-';
  $bacaPct = $child ? round($child['baca']) : 0;
  $tulisPct = $child ? round($child['tulis']) : 0;
  $hitungPct = $child ? round($child['hitung']) : 0;
  $selectedStudentId = $child ? $child['student']->id : null;
  $reportUrl = $selectedStudentId ? route('wali.rapor', ['student_id' => $selectedStudentId]) : route('wali.rapor');
  $statusClass = function ($status) {
    return $status === 'aktif' ? 'bg-[#DCFCE7] text-[#166534]' : ($status === 'lulus' ? 'bg-[#FEE2E2] text-[#991B1B]' : 'bg-[#FEF3C7] text-[#92400E]');
  };
@endphp

@if(session('wali_password_notice'))
<div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3">
  <div class="flex items-start gap-3">
    <iconify-icon icon="lucide:shield-alert" width="18" class="text-amber-600 mt-0.5"></iconify-icon>
    <div>
      <p class="text-sm font-semibold text-amber-900">Penting: Ubah Password Anda</p>
      <p class="text-sm text-amber-800 mt-0.5">{{ session('wali_password_notice') }}</p>
    </div>
  </div>
</div>
@endif

<!-- Hero Section -->
<section class="hero-gradient rounded-3xl p-6 lg:p-8 custom-shadow border border-[#F973161a] flex flex-col lg:flex-row items-center lg:justify-between gap-6 mb-8">
  <div class="flex flex-col lg:flex-row items-center gap-6">
    <div class="w-20 h-20 rounded-full overflow-hidden custom-shadow border-2 border-white flex-shrink-0">
      <div class="w-full h-full bg-[#FDBA74] flex items-center justify-center text-white text-2xl font-black">{{ $studentInitials }}</div>
    </div>
    <div class="text-center lg:text-left">
      <h1 class="text-2xl lg:text-3xl font-bold text-[#F97316] mb-3">Semangat Belajar, {{ $studentName }}! 👋</h1>
      <div class="flex flex-wrap justify-center lg:justify-start gap-3">
        <div class="flex items-center gap-2 px-3 py-1 bg-white/80 backdrop-blur-sm border border-[#F9731633] rounded-full text-xs font-semibold">
          <iconify-icon icon="lucide:calendar" width="12" class="text-[#F97316]"></iconify-icon>
          Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }}
        </div>
        <div class="flex items-center gap-2 px-3 py-1 bg-white/80 backdrop-blur-sm border border-[#F9731633] rounded-full text-xs font-semibold">
          <iconify-icon icon="lucide:layers" width="12" class="text-[#F97316]"></iconify-icon>
          {{ $classroomName }}
        </div>
      </div>
    </div>
  </div>
  <form method="GET" action="{{ route('wali.dashboard') }}" class="w-full lg:w-80 bg-white/85 backdrop-blur-sm border border-[#F9731633] rounded-2xl p-4">
    <label class="text-xs font-semibold text-[#565d6d] uppercase tracking-wider">Pilih Anak</label>
    <div class="relative mt-2">
      <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-[#F97316]">
        <iconify-icon icon="lucide:user-round" width="16"></iconify-icon>
      </div>
      <select name="student_id" onchange="this.form.submit()" class="w-full pl-9 pr-10 py-2.5 bg-white border border-[#dee1e6] rounded-xl text-sm font-medium text-[#171a1f] appearance-none focus:outline-none focus:ring-2 focus:ring-[#F97316]/20">
        @forelse($children as $student)
          <option value="{{ $student->id }}" {{ $selectedStudentId === $student->id ? 'selected' : '' }}>
            {{ $student->name }} ({{ $student->classroom?->name ?? '-' }})
          </option>
        @empty
          <option value="">Belum ada anak terhubung</option>
        @endforelse
      </select>
      <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-[#565d6d]">
        <iconify-icon icon="lucide:chevron-down" width="14"></iconify-icon>
      </div>
    </div>
  </form>
</section>

<!-- Status Anak -->
<section class="mb-8">
  <div class="bg-white rounded-2xl p-5 custom-shadow border border-[#dee1e6]">
    <div class="flex items-center gap-2 mb-4">
      <iconify-icon icon="lucide:users-round" width="18" class="text-[#3d8af5]"></iconify-icon>
      <h3 class="text-base font-bold text-[#171a1f]">Status Siswa</h3>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      @forelse($childrenData as $item)
        <div class="flex items-center justify-between px-4 py-3 bg-[#fafafb] rounded-xl border border-[#eef0f3]">
          <p class="text-sm font-medium text-[#171a1f]">{{ $item['student']->name }}</p>
          <span class="text-xs font-semibold px-2.5 py-1 rounded-lg {{ $statusClass($item['student']->status) }}">{{ ucfirst($item['student']->status) }}</span>
        </div>
      @empty
        <p class="text-sm text-[#565d6d]">Belum ada data siswa.</p>
      @endforelse
    </div>
  </div>
</section>

@if(!$child)
<div class="bg-white rounded-2xl border border-[#dee1e6] p-12 text-center custom-shadow">
  <iconify-icon icon="lucide:user-x" width="48" class="text-[#dee1e6] mx-auto mb-4"></iconify-icon>
  <h2 class="text-lg font-bold text-[#171a1f] mb-2">Belum Ada Anak Terhubung</h2>
  <p class="text-sm text-[#565d6d] max-w-md mx-auto">Akun wali ini belum terhubung ke data murid. Silakan hubungi admin BiMBA untuk menghubungkan akun wali dengan murid yang sesuai.</p>
</div>
@else

<!-- Summary Title -->
<div class="flex items-center gap-2 mb-6">
  <iconify-icon icon="lucide:trending-up" width="20" class="text-[#F97316]"></iconify-icon>
  <h2 class="text-xl font-bold text-[#171a1f]">Ringkasan Progres Saat Ini</h2>
</div>

<!-- 3 Subject Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
  @php
    $skillVisuals = [
      'baca' => ['icon' => 'lucide:book-open', 'bg' => 'bg-[#FFEDD5]', 'text' => 'text-[#F97316]', 'bar' => 'bg-[#F97316]'],
      'tulis' => ['icon' => 'lucide:pencil', 'bg' => 'bg-[#DBEAFE]', 'text' => 'text-[#3B82F6]', 'bar' => 'bg-[#3B82F6]'],
      'hitung' => ['icon' => 'lucide:calculator', 'bg' => 'bg-[#DCFCE7]', 'text' => 'text-[#22C55E]', 'bar' => 'bg-[#22C55E]'],
    ];
    $statusBadgeClasses = [
      'T' => 'bg-[#FFEDD5] text-[#C2410C] border border-[#FDBA74]',
      'P' => 'bg-[#DBEAFE] text-[#2563EB] border border-[#BFDBFE]',
      'K' => 'bg-gray-100 text-[#171a1f] border border-gray-200',
    ];
  @endphp
  @foreach(['baca', 'tulis', 'hitung'] as $skillKey)
    @php
      $card = $skillCards[$skillKey];
      $visual = $skillVisuals[$skillKey];
      $badgeClass = $statusBadgeClasses[$card['status']] ?? $statusBadgeClasses['K'];
    @endphp
    <div class="bg-white rounded-xl p-6 custom-shadow flex flex-col h-full">
      <div class="flex justify-between items-start mb-6">
        <div class="w-12 h-12 {{ $visual['bg'] }} rounded-2xl flex items-center justify-center">
          <iconify-icon icon="{{ $visual['icon'] }}" width="24" class="{{ $visual['text'] }}"></iconify-icon>
        </div>
        <span class="{{ $badgeClass }} text-[10px] font-bold px-3 py-1 rounded-full">Status: {{ $card['status'] }}</span>
      </div>
      <h3 class="text-lg font-bold text-[#171a1f] mb-2">{{ $card['label'] }}</h3>
      <p class="text-sm text-[#565d6d] mb-6 flex-1">{{ $card['description'] }}</p>
      <div class="space-y-2 mb-6">
        <div class="flex justify-between text-xs font-semibold">
          <span>Progres Belajar</span>
          <span>{{ $card['percentage'] }}%</span>
        </div>
        <div class="w-full h-2 bg-[#f3f4f6] rounded-full overflow-hidden">
          <div class="h-full {{ $visual['bar'] }}" style="width: {{ $card['percentage'] }}%"></div>
        </div>
      </div>
      <a href="{{ $reportUrl }}" class="w-full flex items-center justify-center gap-2 text-[#F97316] text-sm font-medium hover:underline">
        Lihat Detail Laporan
        <iconify-icon icon="lucide:arrow-right" width="16"></iconify-icon>
      </a>
    </div>
  @endforeach
</div>

<!-- Jadwal Pembagian Rapor -->
<div class="bg-white rounded-xl p-6 custom-shadow mb-8">
  <div class="mb-4">
    <div>
      <h3 class="text-base font-bold text-[#171a1f]">Jadwal Otomatis Pembagian Rapor</h3>
      <p class="text-xs text-[#565d6d]">Jadwal tiap anak dihitung otomatis per 3 bulan dari tanggal masuk.</p>
    </div>
  </div>

  <div class="space-y-2">
    @foreach($raporSchedules as $item)
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1.5 border border-[#eef2f7] rounded-lg px-3 py-2.5">
        <div>
          <p class="text-sm font-semibold text-[#171a1f]">{{ $item['student_name'] }}</p>
          <p class="text-xs text-[#565d6d]">Periode ke-{{ $item['period_number'] }} • Jadwal: {{ $item['next_date'] }}</p>
        </div>
        <span class="inline-flex text-xs font-semibold px-2.5 py-1 rounded-full {{ $item['days_left'] <= 14 ? 'bg-[#FFEDD5] text-[#C2410C]' : 'bg-[#FFEDD5] text-[#9A3412]' }}">
          {{ max(0, $item['days_left']) }} hari lagi
        </span>
      </div>
    @endforeach
  </div>
</div>

<!-- Bottom Grid: Chart + Side Panels -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
  <!-- Chart Section (xl: 2/3) -->
  <div class="xl:col-span-2 bg-white rounded-xl p-6 custom-shadow">
    <h3 class="text-lg font-bold text-[#171a1f] mb-1">Grafik Perkembangan Kemampuan</h3>
    <p class="text-sm text-[#565d6d] mb-6">Visualisasi pertumbuhan belajar selama 5 bulan terakhir</p>
    <div class="h-[300px]">
      <canvas id="waliDashboardChart"></canvas>
    </div>
  </div>

  <!-- Right Panels -->
  <div class="space-y-6">
    <!-- Panduan Penilaian -->
    <div class="bg-[#fafafb] rounded-xl p-6 custom-shadow">
      <div class="flex items-center gap-2 mb-5">
        <iconify-icon icon="lucide:info" width="16" class="text-[#F97316]"></iconify-icon>
        <h3 class="text-sm font-bold text-[#171a1f]">Panduan Penilaian BiMBA</h3>
      </div>
      <div class="grid grid-cols-3 gap-2">
        <div class="bg-white p-2 rounded-xl custom-shadow text-center">
          <div class="w-8 h-8 bg-[#f3f4f6] rounded-full flex items-center justify-center mx-auto mb-2">
            <span class="text-xs font-bold text-[#565d6d]">K</span>
          </div>
          <p class="text-[10px] font-bold mb-0.5">Kenal</p>
          <p class="text-[8px] text-[#565d6d] leading-tight">Tahap pengenalan</p>
        </div>
        <div class="bg-white p-2 rounded-xl custom-shadow text-center">
          <div class="w-8 h-8 bg-[#F97316] rounded-full flex items-center justify-center mx-auto mb-2">
            <span class="text-xs font-bold text-white">P</span>
          </div>
          <p class="text-[10px] font-bold mb-0.5">Paham</p>
          <p class="text-[8px] text-[#565d6d] leading-tight">Memahami konsep</p>
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
        @forelse($recentProgress as $progress)
          @php
            $progressDate = $progress->updated_at ?? $progress->created_at;
            $statusClass = match($progress->status) {
              'T' => 'bg-[#FFEDD5] border-[#FCA5A5] text-[#C2410C]',
              'P' => 'bg-[#FFF7ED] border-[#FDE047] text-[#C2410C]',
              default => 'bg-[#f3f4f6] border-[#dee1e6] text-[#475569]',
            };
          @endphp
          <div class="px-5 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
              <div class="text-center w-8">
                <p class="text-[8px] font-bold text-[#565d6d] uppercase">{{ $progressDate?->translatedFormat('M') ?? '-' }}</p>
                <p class="text-sm font-bold text-[#171a1f]">{{ $progressDate?->format('d') ?? '-' }}</p>
              </div>
              <p class="text-sm font-medium text-[#171a1f]">{{ $progress->material?->name ?? 'Materi belum tersedia' }}</p>
            </div>
            <div class="w-6 h-6 border rounded-full flex items-center justify-center text-[10px] font-bold {{ $statusClass }}">{{ $progress->status }}</div>
          </div>
        @empty
          <div class="px-5 py-8 text-center">
            <iconify-icon icon="lucide:clipboard-list" width="28" class="text-[#dee1e6] mx-auto mb-2"></iconify-icon>
            <p class="text-sm text-[#565d6d]">Belum ada aktivitas nilai untuk {{ $studentName }}.</p>
          </div>
        @endforelse
      </div>
    </div>
  </div>
</div>

@endif

<!-- Footer -->
<footer class="mt-12 py-6 border-t border-[#dee1e6] text-center">
  <p class="text-xs text-[#565d6d]">© {{ date('Y') }} E-Rapor {{ $institutionName }}. All rights reserved.</p>
</footer>
@endsection

@if($child)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
const waliDashboardChartData = @json($skillTrend);
const waliCtx = document.getElementById('waliDashboardChart').getContext('2d');
new Chart(waliCtx, {
  type: 'line',
  data: {
    labels: waliDashboardChartData.labels,
    datasets: [
      {
        label: waliDashboardChartData.series.baca.label,
        data: waliDashboardChartData.series.baca.data,
        borderColor: '#F97316',
        backgroundColor: 'rgba(249,115,22,0.08)',
        borderWidth: 2.5,
        fill: true,
        tension: 0.4,
        pointRadius: 4,
        pointBackgroundColor: '#F97316'
      },
      {
        label: waliDashboardChartData.series.tulis.label,
        data: waliDashboardChartData.series.tulis.data,
        borderColor: '#3B82F6',
        backgroundColor: 'rgba(99,233,143,0.08)',
        borderWidth: 2.5,
        fill: true,
        tension: 0.4,
        pointRadius: 4,
        pointBackgroundColor: '#3B82F6'
      },
      {
        label: waliDashboardChartData.series.hitung.label,
        data: waliDashboardChartData.series.hitung.data,
        borderColor: '#FDBA74',
        backgroundColor: 'rgba(242,191,140,0.08)',
        borderWidth: 2.5,
        fill: true,
        tension: 0.4,
        pointRadius: 4,
        pointBackgroundColor: '#FDBA74'
      }
    ]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20, font: { size: 11, family: 'Roboto' } } }
    },
    scales: {
      x: { grid: { display: false }, ticks: { font: { size: 11, family: 'Roboto' }, color: '#9095a0' } },
      y: { beginAtZero: true, max: 100, grid: { color: 'rgba(222,225,230,0.5)' }, ticks: { font: { size: 11, family: 'Roboto' }, color: '#9095a0' } }
    }
  }
});
});
</script>
@endpush
@endif
