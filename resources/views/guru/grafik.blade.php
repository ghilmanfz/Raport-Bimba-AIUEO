@extends('layouts.guru')

@section('title', 'Grafik Perkembangan - E-Rapor BiMBA')
@section('page-title', 'Grafik Perkembangan')

@section('content')
<!-- Filter Section -->
<div class="bg-white rounded-2xl p-5 border border-[#dee1e6] main-shadow mb-6">
  <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4 items-end">
    <div>
      <label class="block text-xs font-semibold text-[#565d6d] mb-1.5">Kelas</label>
      <div class="relative">
        <select class="w-full pl-3 pr-8 py-2.5 border border-[#dee1e6] rounded-xl text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20 bg-white">
          <option>KB - AIUEO 1</option>
          <option>KB - AIUEO 2</option>
          <option>TK A - AIUEO 3</option>
          <option>TK B - AIUEO 4</option>
        </select>
        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-[#565d6d]">
          <iconify-icon icon="lucide:chevron-down" width="14"></iconify-icon>
        </div>
      </div>
    </div>
    <div>
      <label class="block text-xs font-semibold text-[#565d6d] mb-1.5">Aspek</label>
      <div class="relative">
        <select class="w-full pl-3 pr-8 py-2.5 border border-[#dee1e6] rounded-xl text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20 bg-white">
          <option>Membaca</option>
          <option>Menulis</option>
          <option>Berhitung</option>
        </select>
        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-[#565d6d]">
          <iconify-icon icon="lucide:chevron-down" width="14"></iconify-icon>
        </div>
      </div>
    </div>
    <div>
      <label class="block text-xs font-semibold text-[#565d6d] mb-1.5">Periode</label>
      <div class="relative">
        <select class="w-full pl-3 pr-8 py-2.5 border border-[#dee1e6] rounded-xl text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20 bg-white">
          <option>Semester Ini</option>
          <option>Semester Lalu</option>
          <option>Tahun Ini</option>
        </select>
        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-[#565d6d]">
          <iconify-icon icon="lucide:chevron-down" width="14"></iconify-icon>
        </div>
      </div>
    </div>
    <div>
      <label class="block text-xs font-semibold text-[#565d6d] mb-1.5">Rentang Tanggal</label>
      <div class="flex items-center gap-2">
        <input type="date" class="flex-1 px-3 py-2.5 border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20" value="2024-01-01">
        <span class="text-xs text-[#565d6d]">–</span>
        <input type="date" class="flex-1 px-3 py-2.5 border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20" value="2024-06-30">
      </div>
    </div>
    <div>
      <button class="w-full px-5 py-2.5 bg-[#3d8af5] text-white rounded-xl text-sm font-medium shadow-md hover:bg-blue-600 flex items-center justify-center gap-2">
        <iconify-icon icon="lucide:filter" width="16"></iconify-icon>
        Filter
      </button>
    </div>
  </div>
</div>

<!-- Main Grid -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
  <!-- Chart Area (xl: 2/3) -->
  <div class="xl:col-span-2 bg-white rounded-2xl border border-[#dee1e6] main-shadow overflow-hidden">
    <div class="p-6 border-b border-[#dee1e6] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h2 class="text-lg font-semibold text-[#171a1f]">Tren Kemajuan Level Siswa</h2>
        <p class="text-sm text-[#565d6d]">Visualisasi perkembangan level per periode</p>
      </div>
      <div class="flex bg-[#f3f4f6] rounded-full p-1">
        <button id="btnLine" class="flex items-center gap-1.5 px-4 py-1.5 bg-white rounded-full text-xs font-semibold text-[#171a1f] shadow-sm">
          <iconify-icon icon="lucide:trending-up" width="14"></iconify-icon> Line
        </button>
        <button id="btnBar" class="flex items-center gap-1.5 px-4 py-1.5 text-[#565d6d] text-xs font-semibold rounded-full">
          <iconify-icon icon="lucide:bar-chart-2" width="14"></iconify-icon> Bar
        </button>
      </div>
    </div>

    <!-- Chart Canvas -->
    <div class="p-6">
      <div class="h-[350px]">
        <canvas id="grafikTrenChart"></canvas>
      </div>

      <!-- Legend -->
      <div class="flex flex-wrap items-center gap-4 mt-4">
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#3d8af5] inline-block"></span><span class="text-xs text-[#565d6d]">Level 1</span></div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#63e98f] inline-block"></span><span class="text-xs text-[#565d6d]">Level 2</span></div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#f2bf8c] inline-block"></span><span class="text-xs text-[#565d6d]">Level 3</span></div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#bf93ec] inline-block"></span><span class="text-xs text-[#565d6d]">Level 4</span></div>
      </div>
    </div>
  </div>

  <!-- Side Stats (xl: 1/3) -->
  <div class="flex flex-col gap-6">
    <!-- Donut Chart + Status % -->
    <div class="bg-white rounded-2xl border border-[#dee1e6] main-shadow p-6">
      <h3 class="text-base font-semibold text-[#171a1f] mb-4">Distribusi Status Kelas</h3>
      <!-- Donut Chart -->
      <div class="flex items-center justify-center mb-5">
        <div class="relative w-28 h-28">
          <canvas id="grafikDonutChart"></canvas>
          <div class="absolute inset-0 flex items-center justify-center">
            <span class="text-xs font-bold text-[#171a1f] text-center leading-tight">24<br><span class="text-[10px] text-[#565d6d]">Murid</span></span>
          </div>
        </div>
      </div>
      <!-- Status % Cards -->
      <div class="grid grid-cols-2 gap-2">
        <div class="bg-[#B0EC93]/20 border border-[#B0EC93] rounded-xl p-2 text-center">
          <p class="text-lg font-black text-[#047857]">45%</p>
          <p class="text-[10px] font-semibold text-[#047857]">Terampil</p>
        </div>
        <div class="bg-[#6EC9F7]/20 border border-[#6EC9F7] rounded-xl p-2 text-center">
          <p class="text-lg font-black text-[#0369A1]">30%</p>
          <p class="text-[10px] font-semibold text-[#0369A1]">Paham</p>
        </div>
        <div class="bg-[#F7C96E]/20 border border-[#F7C96E] rounded-xl p-2 text-center">
          <p class="text-lg font-black text-[#B45309]">15%</p>
          <p class="text-[10px] font-semibold text-[#B45309]">Belajar</p>
        </div>
        <div class="bg-[#C5CCD3]/20 border border-[#C5CCD3] rounded-xl p-2 text-center">
          <p class="text-lg font-black text-[#334155]">10%</p>
          <p class="text-[10px] font-semibold text-[#334155]">Kenal</p>
        </div>
      </div>
    </div>

    <!-- Mini Stat Cards 2x2 -->
    <div class="grid grid-cols-2 gap-3">
      <div class="bg-white rounded-xl border border-[#dee1e6] p-4 text-center main-shadow">
        <p class="text-[10px] font-semibold text-[#565d6d] uppercase tracking-wider mb-1">Total Murid</p>
        <p class="text-2xl font-black text-[#171a1f]">24</p>
      </div>
      <div class="bg-white rounded-xl border border-[#dee1e6] p-4 text-center main-shadow">
        <p class="text-[10px] font-semibold text-[#565d6d] uppercase tracking-wider mb-1">Rata-rata</p>
        <p class="text-2xl font-black text-[#3d8af5]">Lv2.4</p>
      </div>
      <div class="bg-white rounded-xl border border-[#dee1e6] p-4 text-center main-shadow">
        <p class="text-[10px] font-semibold text-[#565d6d] uppercase tracking-wider mb-1">Terampil</p>
        <p class="text-2xl font-black text-[#047857]">18</p>
      </div>
      <div class="bg-[#D92626] rounded-xl p-4 text-center main-shadow">
        <p class="text-[10px] font-semibold text-white/80 uppercase tracking-wider mb-1">Perhatian</p>
        <p class="text-2xl font-black text-white">3</p>
      </div>
    </div>
  </div>
</div>

<!-- Student Table Card -->
<div class="bg-white rounded-2xl border border-[#dee1e6] main-shadow overflow-hidden mb-6">
  <div class="p-6 border-b border-[#dee1e6] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h2 class="text-lg font-semibold text-[#171a1f]">Daftar Murid & Progres</h2>
      <p class="text-sm text-[#565d6d]">Detail perkembangan setiap murid</p>
    </div>
    <div class="relative w-full sm:w-64">
      <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-[#565d6d]">
        <iconify-icon icon="lucide:search" width="14"></iconify-icon>
      </div>
      <input type="text" placeholder="Cari murid..." class="w-full pl-9 pr-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20">
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-[#f3f4f6]/50 border-b border-[#dee1e6] text-sm font-semibold text-[#171a1f]">
          <th class="px-6 py-4">Nama Murid</th>
          <th class="px-6 py-4">Level</th>
          <th class="px-6 py-4 min-w-[220px]">Progres Belajar</th>
          <th class="px-6 py-4">Status</th>
          <th class="px-6 py-4">Terakhir Aktif</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#dee1e6]">
        <tr class="hover:bg-gray-50/50">
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full bg-[#3d8af5] flex items-center justify-center text-white text-sm font-bold flex-shrink-0">AW</div>
              <div><p class="text-sm font-semibold text-[#171a1f]">Andi Wijaya</p><p class="text-xs text-[#565d6d] font-roboto">KB - AIUEO 1</p></div>
            </div>
          </td>
          <td class="px-6 py-4"><span class="px-3 py-1 bg-[#3d8af5]/10 text-[#3d8af5] rounded-full text-xs font-bold">Level 1</span></td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="flex-1 h-2 bg-[#f3f4f6] rounded-full overflow-hidden">
                <div class="h-full bg-[#3d8af5] rounded-full" style="width: 85%"></div>
              </div>
              <span class="text-xs font-bold text-[#171a1f] font-roboto w-9">85%</span>
            </div>
          </td>
          <td class="px-6 py-4"><span class="px-2.5 py-0.5 bg-[#A7F3D0] text-[#047857] rounded-full text-xs font-bold">T</span></td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">Hari ini</td>
        </tr>
        <tr class="hover:bg-gray-50/50">
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full bg-[#63e98f] flex items-center justify-center text-white text-sm font-bold flex-shrink-0">BS</div>
              <div><p class="text-sm font-semibold text-[#171a1f]">Budi Santoso</p><p class="text-xs text-[#565d6d] font-roboto">KB - AIUEO 1</p></div>
            </div>
          </td>
          <td class="px-6 py-4"><span class="px-3 py-1 bg-[#63e98f]/10 text-[#047857] rounded-full text-xs font-bold">Level 2</span></td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="flex-1 h-2 bg-[#f3f4f6] rounded-full overflow-hidden">
                <div class="h-full bg-[#63e98f] rounded-full" style="width: 72%"></div>
              </div>
              <span class="text-xs font-bold text-[#171a1f] font-roboto w-9">72%</span>
            </div>
          </td>
          <td class="px-6 py-4"><span class="px-2.5 py-0.5 bg-[#BAE6FD] text-[#0369A1] rounded-full text-xs font-bold">P</span></td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">Kemarin</td>
        </tr>
        <tr class="hover:bg-gray-50/50">
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full bg-[#f2bf8c] flex items-center justify-center text-white text-sm font-bold flex-shrink-0">CL</div>
              <div><p class="text-sm font-semibold text-[#171a1f]">Citra Lestari</p><p class="text-xs text-[#565d6d] font-roboto">KB - AIUEO 1</p></div>
            </div>
          </td>
          <td class="px-6 py-4"><span class="px-3 py-1 bg-[#f2bf8c]/20 text-[#B45309] rounded-full text-xs font-bold">Level 1</span></td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="flex-1 h-2 bg-[#f3f4f6] rounded-full overflow-hidden">
                <div class="h-full bg-[#f2bf8c] rounded-full" style="width: 60%"></div>
              </div>
              <span class="text-xs font-bold text-[#171a1f] font-roboto w-9">60%</span>
            </div>
          </td>
          <td class="px-6 py-4"><span class="px-2.5 py-0.5 bg-[#FDE68A] text-[#B45309] rounded-full text-xs font-bold">B</span></td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">2 hari lalu</td>
        </tr>
        <tr class="hover:bg-gray-50/50">
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full bg-[#bf93ec] flex items-center justify-center text-white text-sm font-bold flex-shrink-0">DP</div>
              <div><p class="text-sm font-semibold text-[#171a1f]">Deni Pratama</p><p class="text-xs text-[#565d6d] font-roboto">KB - AIUEO 1</p></div>
            </div>
          </td>
          <td class="px-6 py-4"><span class="px-3 py-1 bg-[#bf93ec]/10 text-[#6b21a8] rounded-full text-xs font-bold">Level 3</span></td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="flex-1 h-2 bg-[#f3f4f6] rounded-full overflow-hidden">
                <div class="h-full bg-[#bf93ec] rounded-full" style="width: 91%"></div>
              </div>
              <span class="text-xs font-bold text-[#171a1f] font-roboto w-9">91%</span>
            </div>
          </td>
          <td class="px-6 py-4"><span class="px-2.5 py-0.5 bg-[#A7F3D0] text-[#047857] rounded-full text-xs font-bold">T</span></td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">Hari ini</td>
        </tr>
        <tr class="hover:bg-gray-50/50">
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full bg-[#D92626] flex items-center justify-center text-white text-sm font-bold flex-shrink-0">EK</div>
              <div><p class="text-sm font-semibold text-[#171a1f]">Eka Kusuma</p><p class="text-xs text-[#565d6d] font-roboto">KB - AIUEO 1</p></div>
            </div>
          </td>
          <td class="px-6 py-4"><span class="px-3 py-1 bg-gray-100 text-[#565d6d] rounded-full text-xs font-bold">Level 1</span></td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="flex-1 h-2 bg-[#f3f4f6] rounded-full overflow-hidden">
                <div class="h-full bg-[#D92626] rounded-full" style="width: 30%"></div>
              </div>
              <span class="text-xs font-bold text-[#171a1f] font-roboto w-9">30%</span>
            </div>
          </td>
          <td class="px-6 py-4"><span class="px-2.5 py-0.5 bg-[#E2E8F0] text-[#334155] rounded-full text-xs font-bold">K</span></td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">4 hari lalu</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="p-4 bg-[#f3f4f6]/30 border-t border-[#dee1e6] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <p class="text-sm text-[#565d6d] font-roboto">Menampilkan <span class="font-semibold text-[#171a1f]">5</span> dari <span class="font-semibold text-[#171a1f]">24</span> murid aktif</p>
    <div class="flex items-center gap-2">
      <button class="w-8 h-8 flex items-center justify-center bg-white border border-[#dee1e6] rounded-lg hover:bg-gray-50">
        <iconify-icon icon="lucide:chevron-left" width="14"></iconify-icon>
      </button>
      <button class="w-8 h-8 flex items-center justify-center bg-[#3d8af5] text-white rounded-lg text-sm font-bold">1</button>
      <button class="w-8 h-8 flex items-center justify-center border border-[#dee1e6] rounded-lg text-sm">2</button>
      <button class="w-8 h-8 flex items-center justify-center border border-[#dee1e6] rounded-lg text-sm">3</button>
      <button class="w-8 h-8 flex items-center justify-center bg-white border border-[#dee1e6] rounded-lg hover:bg-gray-50">
        <iconify-icon icon="lucide:chevron-right" width="14"></iconify-icon>
      </button>
    </div>
  </div>
</div>

<!-- Footer Links -->
<footer class="py-4 border-t border-[#dee1e6] flex flex-wrap items-center justify-between gap-4">
  <p class="text-xs text-[#565d6d]">© 2026 E-Rapor BiMBA AIUEO Smart Education Centre</p>
  <div class="flex gap-6 text-xs text-[#565d6d]">
    <a href="#" class="hover:text-[#3d8af5]">Pusat Bantuan</a>
    <a href="#" class="hover:text-[#3d8af5]">Kebijakan Privasi</a>
    <a href="#" class="hover:text-[#3d8af5]">Syarat &amp; Ketentuan</a>
  </div>
</footer>
@endsection

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
@endpush

@push('scripts')
<script>
// --- Tren Kemajuan Level Siswa (4 levels, area chart) ---
const trendCtx = document.getElementById('grafikTrenChart').getContext('2d');
let currentChartType = 'line';

const trendLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'];
const trendDatasets = [
  {
    label: 'Level 1',
    data: [10, 9, 7, 5, 3, 1],
    borderColor: '#3d8af5',
    backgroundColor: 'rgba(61,138,245,0.10)',
    borderWidth: 2.5,
    fill: true,
    tension: 0.4,
    pointRadius: 3,
    pointBackgroundColor: '#3d8af5'
  },
  {
    label: 'Level 2',
    data: [5, 7, 8, 11, 9, 7],
    borderColor: '#63e98f',
    backgroundColor: 'rgba(99,233,143,0.10)',
    borderWidth: 2.5,
    fill: true,
    tension: 0.4,
    pointRadius: 3,
    pointBackgroundColor: '#63e98f'
  },
  {
    label: 'Level 3',
    data: [2, 4, 6, 9, 10, 15],
    borderColor: '#f2bf8c',
    backgroundColor: 'rgba(242,191,140,0.10)',
    borderWidth: 2.5,
    fill: true,
    tension: 0.4,
    pointRadius: 3,
    pointBackgroundColor: '#f2bf8c'
  },
  {
    label: 'Level 4',
    data: [0, 1, 3, 4, 7, 9],
    borderColor: '#bf93ec',
    backgroundColor: 'rgba(191,147,236,0.10)',
    borderWidth: 2.5,
    fill: true,
    tension: 0.4,
    pointRadius: 3,
    pointBackgroundColor: '#bf93ec'
  }
];

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false } },
  scales: {
    x: { grid: { display: false }, ticks: { font: { size: 11, family: 'Roboto' }, color: '#9095a0' } },
    y: { beginAtZero: true, max: 16, grid: { color: 'rgba(222,225,230,0.5)' }, ticks: { font: { size: 11, family: 'Roboto' }, color: '#9095a0', callback: v => v + ' M' } }
  }
};

let trendChart = new Chart(trendCtx, {
  type: 'line',
  data: { labels: trendLabels, datasets: trendDatasets },
  options: chartOptions
});

// Toggle Line / Bar
document.getElementById('btnLine').addEventListener('click', function() {
  if (currentChartType === 'line') return;
  currentChartType = 'line';
  trendChart.destroy();
  trendChart = new Chart(trendCtx, {
    type: 'line',
    data: { labels: trendLabels, datasets: trendDatasets.map(ds => ({...ds, fill: true})) },
    options: chartOptions
  });
  this.classList.add('bg-white', 'shadow-sm', 'text-[#171a1f]');
  this.classList.remove('text-[#565d6d]');
  document.getElementById('btnBar').classList.remove('bg-white', 'shadow-sm', 'text-[#171a1f]');
  document.getElementById('btnBar').classList.add('text-[#565d6d]');
});

document.getElementById('btnBar').addEventListener('click', function() {
  if (currentChartType === 'bar') return;
  currentChartType = 'bar';
  trendChart.destroy();
  trendChart = new Chart(trendCtx, {
    type: 'bar',
    data: { labels: trendLabels, datasets: trendDatasets.map(ds => ({...ds, fill: false, borderWidth: 0, borderRadius: 6, barPercentage: 0.7})) },
    options: {...chartOptions, scales: {...chartOptions.scales, x: {...chartOptions.scales.x}}}
  });
  this.classList.add('bg-white', 'shadow-sm', 'text-[#171a1f]');
  this.classList.remove('text-[#565d6d]');
  document.getElementById('btnLine').classList.remove('bg-white', 'shadow-sm', 'text-[#171a1f]');
  document.getElementById('btnLine').classList.add('text-[#565d6d]');
});

// --- Donut Chart: Distribusi Status ---
const donutCtx = document.getElementById('grafikDonutChart').getContext('2d');
new Chart(donutCtx, {
  type: 'doughnut',
  data: {
    labels: ['Terampil', 'Paham', 'Belajar', 'Kenal'],
    datasets: [{
      data: [45, 30, 15, 10],
      backgroundColor: ['#B0EC93', '#6EC9F7', '#F7C96E', '#C5CCD3'],
      borderWidth: 0,
      hoverOffset: 4
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: true,
    cutout: '58%',
    plugins: { legend: { display: false } }
  }
});
</script>
@endpush
