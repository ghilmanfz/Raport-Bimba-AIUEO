@extends('layouts.guru')

@section('title', 'Grafik Perkembangan - E-Rapor BiMBA')
@section('page-title', 'Grafik Perkembangan')

@section('content')
<!-- Filter Section -->
<form method="GET" action="{{ route('guru.grafik') }}" class="bg-white rounded-2xl p-5 border border-[#dee1e6] main-shadow mb-6">
  <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 items-end">
    <div>
      <label class="block text-xs font-semibold text-[#565d6d] mb-1.5">Kelas</label>
      <div class="relative">
        <select name="classroom_id" class="w-full pl-3 pr-8 py-2.5 border border-[#dee1e6] rounded-xl text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20 bg-white">
          <option value="">Semua Kelas</option>
          @foreach($classrooms as $c)
            <option value="{{ $c->id }}" {{ $selectedClassroom == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
          @endforeach
        </select>
        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-[#565d6d]">
          <iconify-icon icon="lucide:chevron-down" width="14"></iconify-icon>
        </div>
      </div>
    </div>
    <div>
      <label class="block text-xs font-semibold text-[#565d6d] mb-1.5">Cari Murid</label>
      <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Ketik nama murid..." class="w-full px-3 py-2.5 border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20">
    </div>
    <div>
      <button type="submit" class="w-full px-5 py-2.5 bg-[#2563EB] text-white rounded-xl text-sm font-medium shadow-md hover:bg-blue-600 flex items-center justify-center gap-2">
        <iconify-icon icon="lucide:filter" width="16"></iconify-icon>
        Filter
      </button>
    </div>
  </div>
</form>

<!-- Main Grid -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
  <!-- Chart Area -->
  <div class="xl:col-span-2 bg-white rounded-2xl border border-[#dee1e6] main-shadow overflow-hidden">
    <div class="p-6 border-b border-[#dee1e6]">
      <h2 class="text-lg font-semibold text-[#171a1f]">Distribusi Status Progres</h2>
      <p class="text-sm text-[#565d6d]">Visualisasi status K/B/P/T semua murid</p>
    </div>
    <div class="p-6">
      <div class="h-[350px]">
        <canvas id="grafikTrenChart"></canvas>
      </div>
      <div class="flex flex-wrap items-center gap-4 mt-4">
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#DC2626] inline-block"></span><span class="text-xs text-[#565d6d]">Terampil</span></div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#EAB308] inline-block"></span><span class="text-xs text-[#565d6d]">Paham</span></div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#E2E8F0] inline-block"></span><span class="text-xs text-[#565d6d]">Kenal</span></div>
      </div>
    </div>
  </div>

  <!-- Side Stats -->
  <div class="flex flex-col gap-6">
    <!-- Donut Chart -->
    <div class="bg-white rounded-2xl border border-[#dee1e6] main-shadow p-6">
      <h3 class="text-base font-semibold text-[#171a1f] mb-4">Distribusi Status Kelas</h3>
      <div class="flex items-center justify-center mb-5">
        <div class="relative w-28 h-28">
          <canvas id="grafikDonutChart"></canvas>
          <div class="absolute inset-0 flex items-center justify-center">
            <span class="text-xs font-bold text-[#171a1f] text-center leading-tight">{{ $students->count() }}<br><span class="text-[10px] text-[#565d6d]">Murid</span></span>
          </div>
        </div>
      </div>
      <div class="grid grid-cols-3 gap-2">
        <div class="bg-[#B0EC93]/20 border border-[#B0EC93] rounded-xl p-2 text-center">
          <p class="text-lg font-black text-[#991B1B]">{{ $statusPercent['T'] }}%</p>
          <p class="text-[10px] font-semibold text-[#991B1B]">Terampil</p>
        </div>
        <div class="bg-[#6EC9F7]/20 border border-[#6EC9F7] rounded-xl p-2 text-center">
          <p class="text-lg font-black text-[#A16207]">{{ $statusPercent['P'] }}%</p>
          <p class="text-[10px] font-semibold text-[#A16207]">Paham</p>
        </div>
        <div class="bg-[#C5CCD3]/20 border border-[#C5CCD3] rounded-xl p-2 text-center">
          <p class="text-lg font-black text-[#334155]">{{ $statusPercent['K'] }}%</p>
          <p class="text-[10px] font-semibold text-[#334155]">Kenal</p>
        </div>
      </div>
    </div>

    <!-- Mini Stat Cards -->
    <div class="grid grid-cols-1 gap-3">
      <div class="bg-white rounded-xl border border-[#dee1e6] p-4 text-center main-shadow">
        <p class="text-[10px] font-semibold text-[#565d6d] uppercase tracking-wider mb-1">Total Murid</p>
        <p class="text-2xl font-black text-[#171a1f]">{{ $students->count() }}</p>
      </div>
    </div>
  </div>
</div>

<!-- Student Table Card -->
<div class="bg-white rounded-2xl border border-[#dee1e6] main-shadow overflow-hidden mb-6">
  <div class="p-6 border-b border-[#dee1e6]">
    <h2 class="text-lg font-semibold text-[#171a1f]">Daftar Murid & Progres</h2>
    <p class="text-sm text-[#565d6d]">Detail perkembangan setiap murid</p>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-[#f3f4f6]/50 border-b border-[#dee1e6] text-sm font-semibold text-[#171a1f]">
          <th class="px-6 py-4">Nama Murid</th>
          <th class="px-6 py-4">Kelas</th>
          <th class="px-6 py-4 min-w-[220px]">Progres Terampil</th>
          <th class="px-6 py-4">Status</th>
          <th class="px-6 py-4 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#dee1e6]">
        @forelse($studentStats as $ss)
        @php
          $s = $ss['student'];
          $pct = $ss['progress_pct'];
          $latestStatus = $ss['latest_status'];
          $badge = match($latestStatus) {
            'T' => 'bg-[#FEE2E2] text-[#991B1B]',
            'P' => 'bg-[#FEF9C3] text-[#A16207]',
            default => 'bg-[#E2E8F0] text-[#334155]',
          };
          $initials = collect(explode(' ', $s->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
          $colors = ['#2563EB','#DC2626','#EAB308','#bf93ec','#D92626','#F2930D'];
          $color = $colors[$s->id % count($colors)];
          $barColor = match($latestStatus) { 'T' => '#DC2626', 'P' => '#EAB308', default => '#E2E8F0' };
        @endphp
        <tr class="hover:bg-gray-50/50">
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0" style="background-color: {{ $color }}">{{ $initials }}</div>
              <div><p class="text-sm font-semibold text-[#171a1f]">{{ $s->name }}</p><p class="text-xs text-[#565d6d] font-roboto">{{ preg_replace('/ - .*$/', '', $s->classroom?->level ?? '-') }}</p></div>
            </div>
          </td>
          <td class="px-6 py-4"><span class="px-3 py-1 bg-[#f3f4f6] rounded-full text-xs font-bold">{{ $s->classroom?->level ?? '-' }}</span></td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="flex-1 h-2 bg-[#f3f4f6] rounded-full overflow-hidden">
                <div class="h-full rounded-full" style="width: {{ $pct }}%; background-color: {{ $barColor }}"></div>
              </div>
              <span class="text-xs font-bold text-[#171a1f] font-roboto w-9">{{ $pct }}%</span>
            </div>
          </td>
          <td class="px-6 py-4"><span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $badge }}">{{ $latestStatus }}</span></td>
          <td class="px-6 py-4 text-right">
            <div class="flex justify-end gap-1">
              <a href="{{ route('guru.nilai', ['student_id' => $s->id]) }}" class="p-1.5 hover:bg-gray-100 rounded text-[#2563EB]" title="Input Nilai">
                <iconify-icon icon="lucide:pencil" width="14"></iconify-icon>
              </a>
              <a href="{{ route('guru.rapor', ['student_id' => $s->id]) }}" class="p-1.5 hover:bg-gray-100 rounded text-[#565d6d]" title="Lihat Rapor">
                <iconify-icon icon="lucide:eye" width="14"></iconify-icon>
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

  <div class="p-4 bg-[#f3f4f6]/30 border-t border-[#dee1e6]">
    <p class="text-sm text-[#565d6d] font-roboto">Menampilkan <span class="font-semibold text-[#171a1f]">{{ count($studentStats) }}</span> murid aktif</p>
  </div>
</div>

<!-- Footer -->
<footer class="py-4 border-t border-[#dee1e6] flex flex-wrap items-center justify-between gap-4">
  <p class="text-xs text-[#565d6d]">&copy; {{ date('Y') }} E-Rapor BiMBA AIUEO Smart Education Centre</p>
</footer>
@endsection

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
@endpush

@push('scripts')
<script>
// Bar Chart
const ctx1 = document.getElementById('grafikTrenChart').getContext('2d');
new Chart(ctx1, {
  type: 'bar',
  data: {
    labels: ['Terampil (T)', 'Paham (P)', 'Kenal (K)'],
    datasets: [{
      data: [{{ $statusCounts['T'] }}, {{ $statusCounts['P'] }}, {{ $statusCounts['K'] }}],
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
      x: { grid: { display: false }, ticks: { font: { size: 12, family: 'Inter' }, color: '#565d6d' } },
      y: { beginAtZero: true, grid: { color: 'rgba(222,225,230,0.5)' }, ticks: { stepSize: 1, font: { size: 11, family: 'Roboto' }, color: '#9095a0' } }
    }
  }
});

// Donut Chart
const ctx2 = document.getElementById('grafikDonutChart').getContext('2d');
new Chart(ctx2, {
  type: 'doughnut',
  data: {
    labels: ['Terampil', 'Paham', 'Kenal'],
    datasets: [{
      data: [{{ $statusPercent['T'] }}, {{ $statusPercent['P'] }}, {{ $statusPercent['K'] }}],
      backgroundColor: ['#DC2626', '#EAB308', '#E2E8F0'],
      borderWidth: 0,
      hoverOffset: 4
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: true,
    cutout: '65%',
    plugins: { legend: { display: false } }
  }
});
</script>
@endpush
