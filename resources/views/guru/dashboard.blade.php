@extends('layouts.guru')

@section('title', 'Dashboard Motivator - E-Rapor BiMBA')
@section('page-title', 'Dashboard Motivator')

@section('content')
<!-- Welcome Section -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 rounded-2xl p-6 border border-[#dee1e6]" style="background: linear-gradient(135deg, rgba(37,99,235,0.12) 0 33.33%, rgba(250,204,21,0.12) 33.33% 66.66%, rgba(220,38,38,0.12) 66.66% 100%);">
  <div>
    <h1 class="text-2xl lg:text-3xl font-bold text-[#171a1f] tracking-tight">Dashboard Motivator</h1>
    <p class="text-[#565d6d] mt-1">Selamat datang kembali, {{ auth()->user()->name }}! Berikut adalah ringkasan progres kelas hari ini.</p>
  </div>
  <div class="flex items-center gap-3">
    <a href="{{ route('guru.nilai') }}" class="flex items-center gap-2 px-4 py-2 bg-[#2563EB] text-white rounded-xl text-sm font-medium hover:bg-blue-600">
      <iconify-icon icon="lucide:plus" width="16"></iconify-icon>
      Input Nilai Baru
    </a>
  </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-1 gap-6 mb-8">
  <div class="bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow">
    <div class="flex justify-between items-start mb-4">
      <div class="w-10 h-10 bg-[#F1F6FE] rounded-2xl flex items-center justify-center">
        <iconify-icon icon="lucide:users" width="18" class="text-[#2563EB]"></iconify-icon>
      </div>
    </div>
    <h3 class="text-3xl font-bold text-[#171a1f]">{{ $stats['total_murid'] }}</h3>
    <p class="text-sm font-medium text-[#171a1f] mt-1">Total Murid</p>
    <p class="text-xs text-[#565d6d] mt-1">Siswa aktif di kelas Anda</p>
  </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">
  <div class="xl:col-span-2 bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow">
    <div class="flex justify-between items-start mb-6">
      <div>
        <h2 class="text-lg font-bold text-[#171a1f]">Distribusi Status Progres</h2>
        <p class="text-sm text-[#565d6d]">Ringkasan status K/B/P/T semua murid</p>
      </div>
      <a href="{{ route('guru.grafik') }}" class="text-[#2563EB] text-sm font-medium flex items-center gap-1 hover:underline">
        Detail Lengkap
        <iconify-icon icon="lucide:arrow-right" width="14"></iconify-icon>
      </a>
    </div>
    <div class="h-56">
      <canvas id="dashboardBarChart"></canvas>
    </div>
  </div>

  <!-- Status Kompetensi Donut -->
  <div class="bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow">
    <h2 class="text-lg font-bold text-[#171a1f]">Status Kompetensi</h2>
    <p class="text-sm text-[#565d6d] mb-6">Berdasarkan penilaian terbaru (K, B, P, T)</p>
    <div class="relative w-40 h-40 mx-auto mb-6">
      <canvas id="dashboardDonutChart"></canvas>
      <div class="absolute inset-0 flex items-center justify-center">
        <div class="text-center"><p class="text-lg font-bold text-[#171a1f]">{{ $stats['total_murid'] }}</p><p class="text-xs text-[#565d6d]">murid</p></div>
      </div>
    </div>
    <div class="grid grid-cols-2 gap-y-3 gap-x-2">
      <div class="flex items-center gap-2">
        <div class="w-3 h-3 bg-[#DC2626] rounded-full flex-shrink-0"></div>
        <span class="text-xs text-[#565d6d]">Terampil (T)</span>
        <span class="text-xs font-bold ml-auto">{{ $stats['status_percent']['T'] ?? 0 }}%</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-3 h-3 bg-[#EAB308] rounded-full flex-shrink-0"></div>
        <span class="text-xs text-[#565d6d]">Paham (P)</span>
        <span class="text-xs font-bold ml-auto">{{ $stats['status_percent']['P'] ?? 0 }}%</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-3 h-3 bg-[#E2E8F0] rounded-full flex-shrink-0"></div>
        <span class="text-xs text-[#565d6d]">Kenal (K)</span>
        <span class="text-xs font-bold ml-auto">{{ $stats['status_percent']['K'] ?? 0 }}%</span>
      </div>
    </div>
  </div>
</div>
@endsection

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
@endpush

@push('scripts')
<script>
const ctxBar = document.getElementById('dashboardBarChart').getContext('2d');
new Chart(ctxBar, {
  type: 'bar',
  data: {
    labels: ['Terampil (T)', 'Paham (P)', 'Kenal (K)'],
    datasets: [{
      data: [{{ $stats['status_counts']['T'] ?? 0 }}, {{ $stats['status_counts']['P'] ?? 0 }}, {{ $stats['status_counts']['K'] ?? 0 }}],
      backgroundColor: ['#DC2626', '#EAB308', '#E2E8F0'],
      borderRadius: 8,
      borderSkipped: false,
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { display: false }, ticks: { font: { size: 11, family: 'Roboto' }, color: '#9095a0' } },
      y: { beginAtZero: true, grid: { color: 'rgba(222,225,230,0.5)' }, ticks: { font: { size: 11, family: 'Roboto' }, color: '#9095a0', stepSize: 1 } }
    }
  }
});

const ctxDonut = document.getElementById('dashboardDonutChart').getContext('2d');
new Chart(ctxDonut, {
  type: 'doughnut',
  data: {
    labels: ['Terampil (T)', 'Paham (P)', 'Kenal (K)'],
    datasets: [{
      data: [{{ $stats['status_percent']['T'] ?? 0 }}, {{ $stats['status_percent']['P'] ?? 0 }}, {{ $stats['status_percent']['K'] ?? 0 }}],
      backgroundColor: ['#DC2626', '#EAB308', '#E2E8F0'],
      borderWidth: 0,
      hoverOffset: 6
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: true,
    cutout: '62%',
    plugins: { legend: { display: false } }
  }
});
</script>
@endpush
