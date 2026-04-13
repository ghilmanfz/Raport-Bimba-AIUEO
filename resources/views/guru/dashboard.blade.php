@extends('layouts.guru')

@section('title', 'Dashboard Motivator - E-Rapor BiMBA')
@section('page-title', 'Dashboard Motivator')

@section('content')
<!-- Welcome Section -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
  <div>
    <h1 class="text-2xl lg:text-3xl font-bold text-[#171a1f] tracking-tight">Dashboard Motivator</h1>
    <p class="text-[#565d6d] mt-1">Selamat datang kembali, {{ auth()->user()->name }}! Berikut adalah ringkasan progres kelas hari ini.</p>
  </div>
  <div class="flex items-center gap-3">
    <a href="{{ route('guru.nilai') }}" class="flex items-center gap-2 px-4 py-2 bg-[#3d8af5] text-white rounded-xl text-sm font-medium hover:bg-blue-600">
      <iconify-icon icon="lucide:plus" width="16"></iconify-icon>
      Input Nilai Baru
    </a>
  </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
  <div class="bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow">
    <div class="flex justify-between items-start mb-4">
      <div class="w-10 h-10 bg-[#F1F6FE] rounded-2xl flex items-center justify-center">
        <iconify-icon icon="lucide:users" width="18" class="text-[#3d8af5]"></iconify-icon>
      </div>
    </div>
    <h3 class="text-3xl font-bold text-[#171a1f]">{{ $stats['total_murid'] }}</h3>
    <p class="text-sm font-medium text-[#171a1f] mt-1">Total Murid</p>
    <p class="text-xs text-[#565d6d] mt-1">Siswa aktif di kelas Anda</p>
  </div>
  <div class="bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow">
    <div class="flex justify-between items-start mb-4">
      <div class="w-10 h-10 bg-[#F1F6FE] rounded-2xl flex items-center justify-center">
        <iconify-icon icon="lucide:trending-up" width="18" class="text-[#3d8af5]"></iconify-icon>
      </div>
    </div>
    <h3 class="text-3xl font-bold text-[#171a1f]">Lv {{ $stats['avg_level'] }}</h3>
    <p class="text-sm font-medium text-[#171a1f] mt-1">Rata-rata Level</p>
    <p class="text-xs text-[#565d6d] mt-1">Kemajuan rata-rata kelas</p>
  </div>
  <div class="bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow">
    <div class="flex justify-between items-start mb-4">
      <div class="w-10 h-10 bg-[#F1F6FE] rounded-2xl flex items-center justify-center">
        <iconify-icon icon="lucide:check-circle-2" width="18" class="text-[#3d8af5]"></iconify-icon>
      </div>
      @if($stats['total_murid'] > 0)
      <span class="px-2 py-1 bg-[#f3f4f6] rounded-full text-xs font-semibold text-[#3d8af5]">{{ round($stats['terampil'] / max($stats['total_murid'],1) * 100) }}% efektivitas</span>
      @endif
    </div>
    <h3 class="text-3xl font-bold text-[#171a1f]">{{ $stats['terampil'] }}</h3>
    <p class="text-sm font-medium text-[#171a1f] mt-1">Siswa Terampil</p>
    <p class="text-xs text-[#565d6d] mt-1">Mencapai target (T)</p>
  </div>
  <div class="bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow">
    <div class="flex justify-between items-start mb-4">
      <div class="w-10 h-10 bg-[#F1F6FE] rounded-2xl flex items-center justify-center">
        <iconify-icon icon="lucide:alert-circle" width="18" class="text-[#3d8af5]"></iconify-icon>
      </div>
      @if($stats['perlu_perhatian'] > 0)
      <span class="px-2 py-1 bg-[#f3f4f6] rounded-full text-xs font-semibold text-[#D92626]">Tindak lanjut</span>
      @endif
    </div>
    <h3 class="text-3xl font-bold text-[#171a1f]">{{ $stats['perlu_perhatian'] }}</h3>
    <p class="text-sm font-medium text-[#171a1f] mt-1">Butuh Perhatian</p>
    <p class="text-xs text-[#565d6d] mt-1">Masih di tahap (K/B)</p>
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
      <a href="{{ route('guru.grafik') }}" class="text-[#3d8af5] text-sm font-medium flex items-center gap-1 hover:underline">
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
        <div class="w-3 h-3 bg-[#63e98f] rounded-full flex-shrink-0"></div>
        <span class="text-xs text-[#565d6d]">Terampil (T)</span>
        <span class="text-xs font-bold ml-auto">{{ $stats['status_percent']['T'] ?? 0 }}%</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-3 h-3 bg-[#86d2f9] rounded-full flex-shrink-0"></div>
        <span class="text-xs text-[#565d6d]">Paham (P)</span>
        <span class="text-xs font-bold ml-auto">{{ $stats['status_percent']['P'] ?? 0 }}%</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-3 h-3 bg-[#F2930D] rounded-full flex-shrink-0"></div>
        <span class="text-xs text-[#565d6d]">Belajar (B)</span>
        <span class="text-xs font-bold ml-auto">{{ $stats['status_percent']['B'] ?? 0 }}%</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-3 h-3 bg-[#E2E8F0] rounded-full flex-shrink-0"></div>
        <span class="text-xs text-[#565d6d]">Kenal (K)</span>
        <span class="text-xs font-bold ml-auto">{{ $stats['status_percent']['K'] ?? 0 }}%</span>
      </div>
    </div>
  </div>
</div>

<!-- Table Section -->
<div class="bg-white rounded-xl border border-[#dee1e6] overflow-hidden main-shadow">
  <div class="p-6 bg-[#fafafb]/50 border-b border-[#dee1e6] flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
      <h2 class="text-xl font-bold text-[#171a1f]">Daftar Murid Aktif</h2>
      <p class="text-sm text-[#565d6d]">Kelola dan pantau progres individu murid Anda</p>
    </div>
    <form method="GET" action="{{ route('guru.dashboard') }}" class="relative w-full md:w-80">
      <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-[#565d6d]">
        <iconify-icon icon="lucide:search" width="16"></iconify-icon>
      </div>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama murid..." class="w-full pl-10 pr-4 py-2 bg-white border border-[#dee1e6]/60 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20">
    </form>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-[#f3f4f6]/30 text-[#565d6d] text-sm font-semibold border-b border-[#dee1e6]">
          <th class="px-6 py-4">Nama Murid</th>
          <th class="px-6 py-4">Kelas</th>
          <th class="px-6 py-4">Status Terbaru</th>
          <th class="px-6 py-4">Progres Terampil</th>
          <th class="px-6 py-4 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#dee1e6]">
        @forelse($students as $s)
        @php
          $prog = $s->progress;
          $total = $prog->count();
          $tCount = $prog->where('status', 'T')->count();
          $pct = $total > 0 ? round($tCount / $total * 100) : 0;
          $latestStatus = $prog->sortByDesc('updated_at')->first()?->status ?? 'K';
          $badge = match($latestStatus) {
            'T' => 'bg-[#A7F3D0] text-[#047857]',
            'P' => 'bg-[#BAE6FD] text-[#0369A1]',
            'B' => 'bg-[#FDE68A] text-[#B45309]',
            default => 'bg-[#E2E8F0] text-[#334155]',
          };
          $label = match($latestStatus) {
            'T' => 'T - Terampil', 'P' => 'P - Paham', 'B' => 'B - Belum', default => 'K - Kenal',
          };
          $initials = collect(explode(' ', $s->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
          $colors = ['#3d8af5','#63e98f','#f2bf8c','#bf93ec','#D92626','#F2930D'];
          $color = $colors[$s->id % count($colors)];
        @endphp
        <tr class="hover:bg-gray-50/50">
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0" style="background-color: {{ $color }}">{{ $initials }}</div>
              <div>
                <p class="text-sm font-semibold text-[#171a1f]">{{ $s->name }}</p>
                <p class="text-xs text-[#565d6d]">NIS: {{ $s->nis }}</p>
              </div>
            </div>
          </td>
          <td class="px-6 py-4">
            <span class="px-3 py-1 bg-[#f3f4f6] rounded-full text-xs font-medium text-[#1e2128]">{{ $s->classroom?->name ?? '-' }}</span>
          </td>
          <td class="px-6 py-4">
            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $badge }}">{{ $label }}</span>
          </td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="w-24 h-2 bg-[#f3f4f6] rounded-full overflow-hidden">
                <div class="h-full bg-[#3d8af5]" style="width: {{ $pct }}%"></div>
              </div>
              <span class="text-xs font-bold text-[#565d6d]">{{ $pct }}%</span>
            </div>
          </td>
          <td class="px-6 py-4 text-right">
            <div class="flex justify-end gap-1">
              <a href="{{ route('guru.nilai', ['student_id' => $s->id]) }}" class="p-1.5 hover:bg-gray-100 rounded text-[#3d8af5]" title="Input Nilai">
                <iconify-icon icon="lucide:pencil" width="14"></iconify-icon>
              </a>
              <a href="{{ route('guru.rapor', ['student_id' => $s->id]) }}" class="p-1.5 hover:bg-gray-100 rounded text-[#565d6d]" title="Lihat Rapor">
                <iconify-icon icon="lucide:eye" width="14"></iconify-icon>
              </a>
              <a href="{{ route('guru.grafik') }}" class="p-1.5 hover:bg-gray-100 rounded text-[#565d6d]" title="Grafik">
                <iconify-icon icon="lucide:bar-chart-2" width="14"></iconify-icon>
              </a>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="px-6 py-8 text-center text-sm text-[#565d6d]">Tidak ada murid ditemukan.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="p-4 bg-[#fafafb]/30 border-t border-[#dee1e6]">
    <span class="text-xs text-[#565d6d] font-roboto">Menampilkan {{ $students->count() }} murid aktif</span>
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
    labels: ['Terampil (T)', 'Paham (P)', 'Belajar (B)', 'Kenal (K)'],
    datasets: [{
      data: [{{ $stats['status_counts']['T'] ?? 0 }}, {{ $stats['status_counts']['P'] ?? 0 }}, {{ $stats['status_counts']['B'] ?? 0 }}, {{ $stats['status_counts']['K'] ?? 0 }}],
      backgroundColor: ['#63e98f', '#86d2f9', '#F2930D', '#E2E8F0'],
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
    labels: ['Terampil (T)', 'Paham (P)', 'Belajar (B)', 'Kenal (K)'],
    datasets: [{
      data: [{{ $stats['status_percent']['T'] ?? 0 }}, {{ $stats['status_percent']['P'] ?? 0 }}, {{ $stats['status_percent']['B'] ?? 0 }}, {{ $stats['status_percent']['K'] ?? 0 }}],
      backgroundColor: ['#63e98f', '#86d2f9', '#F2930D', '#E2E8F0'],
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
