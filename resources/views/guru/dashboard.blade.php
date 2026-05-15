@extends('layouts.guru')

@section('title', 'Dashboard Motivator - E-Rapor BiMBA')
@section('page-title', 'Dashboard Motivator')

@section('content')
<!-- Welcome Section -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 rounded-2xl p-6 border border-[#dee1e6]" style="background: linear-gradient(135deg, rgba(249,115,22,0.10) 0%, rgba(59,130,246,0.08) 50%, rgba(34,197,94,0.08) 100%);">
  <div>
    <h1 class="text-2xl lg:text-3xl font-bold text-[#171a1f] tracking-tight">Dashboard Motivator</h1>
    <p class="text-[#565d6d] mt-1">Selamat datang kembali, {{ auth()->user()->name }}! Berikut adalah ringkasan progres kelas hari ini.</p>
  </div>
  <div class="flex items-center gap-3">
    <a href="{{ route('guru.nilai') }}" class="flex items-center gap-2 px-4 py-2 bg-[#F97316] text-white rounded-xl text-sm font-medium hover:bg-orange-600">
      <iconify-icon icon="lucide:plus" width="16"></iconify-icon>
      Input Nilai Baru
    </a>
  </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-1 gap-6 mb-8">
  <div class="bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow">
    <div class="flex justify-between items-start mb-4">
      <div class="w-10 h-10 bg-[#FFF7ED] rounded-2xl flex items-center justify-center">
        <iconify-icon icon="lucide:users" width="18" class="text-[#F97316]"></iconify-icon>
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
      <a href="{{ route('guru.grafik') }}" class="text-[#F97316] text-sm font-medium flex items-center gap-1 hover:underline">
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
        <div class="w-3 h-3 bg-[#22C55E] rounded-full flex-shrink-0"></div>
        <span class="text-xs text-[#565d6d]">Terampil (T)</span>
        <span class="text-xs font-bold ml-auto">{{ $stats['status_percent']['T'] ?? 0 }}%</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-3 h-3 bg-[#3B82F6] rounded-full flex-shrink-0"></div>
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

<!-- Jadwal Otomatis Pembagian Rapor -->
<section class="bg-white p-6 rounded-2xl main-shadow border border-gray-50 mt-8">
  <div class="mb-5">
    <div class="flex items-center gap-3 mb-1">
      <div class="w-8 h-8 bg-[#FFF7ED] rounded-lg flex items-center justify-center flex-shrink-0">
        <iconify-icon icon="lucide:calendar-clock" width="16" class="text-[#F97316]"></iconify-icon>
      </div>
      <h2 class="text-lg font-bold text-[#171a1f]">Jadwal Otomatis Pembagian Rapor</h2>
    </div>
    <p class="text-sm text-[#565d6d] ml-11">Dihitung otomatis per 3 bulan dari tanggal masuk murid di kelas Anda.</p>
  </div>

  @if($nextRaporSchedules->isEmpty())
    <div class="text-center py-8 text-[#565d6d]">
      <iconify-icon icon="lucide:calendar-x" width="40" class="mx-auto mb-3 text-[#dee1e6]"></iconify-icon>
      <p class="text-sm">Belum ada murid dengan tanggal masuk terdaftar.</p>
    </div>
  @else
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left border-b border-[#dee1e6]">
          <th class="py-2 pr-4 text-[#565d6d] font-semibold">Nama Murid</th>
          <th class="py-2 pr-4 text-[#565d6d] font-semibold">Kelas</th>
          <th class="py-2 pr-4 text-[#565d6d] font-semibold">Tanggal Masuk</th>
          <th class="py-2 pr-4 text-[#565d6d] font-semibold">Jadwal Bagi Rapor Berikutnya</th>
          <th class="py-2 pr-4 text-[#565d6d] font-semibold">Periode</th>
          <th class="py-2 text-[#565d6d] font-semibold">Sisa Hari</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#f1f5f9]">
        @foreach($nextRaporSchedules as $item)
        <tr>
          <td class="py-2.5 pr-4 font-medium text-[#171a1f]">{{ $item['student_name'] }}</td>
          <td class="py-2.5 pr-4 text-[#565d6d]">{{ $item['classroom'] }}</td>
          <td class="py-2.5 pr-4 text-[#565d6d]">{{ $item['join_date'] }}</td>
          <td class="py-2.5 pr-4 text-[#171a1f]">{{ $item['next_date'] }}</td>
          <td class="py-2.5 pr-4">
            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-[#FFEDD5] text-[#C2410C]">Ke-{{ $item['period_number'] }}</span>
          </td>
          <td class="py-2.5">
            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $item['days_left'] <= 14 ? 'bg-[#FFEDD5] text-[#C2410C]' : 'bg-[#FFF7ED] text-[#EA580C]' }}">
              {{ max(0, $item['days_left']) }} hari
            </span>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
const ctxBar = document.getElementById('dashboardBarChart').getContext('2d');
new Chart(ctxBar, {
  type: 'bar',
  data: {
    labels: ['Terampil (T)', 'Paham (P)', 'Kenal (K)'],
    datasets: [{
      data: [{{ $stats['status_counts']['T'] ?? 0 }}, {{ $stats['status_counts']['P'] ?? 0 }}, {{ $stats['status_counts']['K'] ?? 0 }}],
      backgroundColor: ['#22C55E', '#3B82F6', '#E2E8F0'],
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
      backgroundColor: ['#22C55E', '#3B82F6', '#E2E8F0'],
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
});
</script>
@endpush
