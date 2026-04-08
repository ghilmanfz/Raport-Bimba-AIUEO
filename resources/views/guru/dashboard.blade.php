@extends('layouts.guru')

@section('title', 'Dashboard Motivator - E-Rapor BiMBA')
@section('page-title', 'Dashboard Motivator')

@section('content')
<!-- Welcome Section -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
  <div>
    <h1 class="text-2xl lg:text-3xl font-bold text-[#171a1f] tracking-tight">Dashboard Motivator</h1>
    <p class="text-[#565d6d] mt-1">Selamat datang kembali, Ibu Maya! Berikut adalah ringkasan progres kelas hari ini.</p>
  </div>
  <div class="flex items-center gap-3">
    <button class="flex items-center gap-2 px-4 py-2 bg-white border border-[#dee1e6] rounded-xl text-sm font-medium text-[#171a1f] hover:bg-gray-50">
      <iconify-icon icon="lucide:filter" width="16"></iconify-icon>
      Filter Kelas
    </button>
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
      <span class="px-2 py-1 bg-[#f3f4f6] rounded-full text-xs font-semibold text-[#171a1f]">+2 bulan ini</span>
    </div>
    <h3 class="text-3xl font-bold text-[#171a1f]">24</h3>
    <p class="text-sm font-medium text-[#171a1f] mt-1">Total Murid</p>
    <p class="text-xs text-[#565d6d] mt-1">Siswa aktif di kelas ini</p>
  </div>
  <div class="bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow">
    <div class="flex justify-between items-start mb-4">
      <div class="w-10 h-10 bg-[#F1F6FE] rounded-2xl flex items-center justify-center">
        <iconify-icon icon="lucide:trending-up" width="18" class="text-[#3d8af5]"></iconify-icon>
      </div>
      <span class="px-2 py-1 bg-[#f3f4f6] rounded-full text-xs font-semibold text-[#171a1f]">+12% bln lalu</span>
    </div>
    <h3 class="text-3xl font-bold text-[#171a1f]">Lv 2.4</h3>
    <p class="text-sm font-medium text-[#171a1f] mt-1">Rata-rata Level</p>
    <p class="text-xs text-[#565d6d] mt-1">Kemajuan rata-rata kelas</p>
  </div>
  <div class="bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow">
    <div class="flex justify-between items-start mb-4">
      <div class="w-10 h-10 bg-[#F1F6FE] rounded-2xl flex items-center justify-center">
        <iconify-icon icon="lucide:check-circle-2" width="18" class="text-[#3d8af5]"></iconify-icon>
      </div>
      <span class="px-2 py-1 bg-[#f3f4f6] rounded-full text-xs font-semibold text-[#3d8af5]">75% efektivitas</span>
    </div>
    <h3 class="text-3xl font-bold text-[#171a1f]">18</h3>
    <p class="text-sm font-medium text-[#171a1f] mt-1">Siswa Terampil</p>
    <p class="text-xs text-[#565d6d] mt-1">Mencapai target (T)</p>
  </div>
  <div class="bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow">
    <div class="flex justify-between items-start mb-4">
      <div class="w-10 h-10 bg-[#F1F6FE] rounded-2xl flex items-center justify-center">
        <iconify-icon icon="lucide:alert-circle" width="18" class="text-[#3d8af5]"></iconify-icon>
      </div>
      <span class="px-2 py-1 bg-[#f3f4f6] rounded-full text-xs font-semibold text-[#D92626]">Tindak lanjut</span>
    </div>
    <h3 class="text-3xl font-bold text-[#171a1f]">3</h3>
    <p class="text-sm font-medium text-[#171a1f] mt-1">Butuh Perhatian</p>
    <p class="text-xs text-[#565d6d] mt-1">Masih di tahap (K/B)</p>
  </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">
  <!-- Kemajuan Level Chart (placeholder) -->
  <div class="xl:col-span-2 bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow">
    <div class="flex justify-between items-start mb-6">
      <div>
        <h2 class="text-lg font-bold text-[#171a1f]">Kemajuan Level Siswa</h2>
        <p class="text-sm text-[#565d6d]">Tren distribusi level siswa per bulan (Jan - Jun)</p>
      </div>
      <a href="{{ route('guru.grafik') }}" class="text-[#3d8af5] text-sm font-medium flex items-center gap-1 hover:underline">
        Detail Lengkap
        <iconify-icon icon="lucide:arrow-right" width="14"></iconify-icon>
      </a>
    </div>
    <!-- Line Chart -->
    <div class="h-56">
      <canvas id="dashboardLineChart"></canvas>
    </div>
    <div class="flex justify-center gap-6 mt-4">
      <div class="flex items-center gap-2"><div class="w-3 h-3 bg-[#3d8af5] rounded-sm"></div><span class="text-xs text-[#171a1f]">Level 1</span></div>
      <div class="flex items-center gap-2"><div class="w-3 h-3 bg-[#F2930D] rounded-sm"></div><span class="text-xs text-[#171a1f]">Level 4</span></div>
    </div>
  </div>

  <!-- Status Kompetensi -->
  <div class="bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow">
    <h2 class="text-lg font-bold text-[#171a1f]">Status Kompetensi</h2>
    <p class="text-sm text-[#565d6d] mb-6">Berdasarkan penilaian terbaru (K, B, P, T)</p>
    <!-- Donut Chart -->
    <div class="relative w-40 h-40 mx-auto mb-6">
      <canvas id="dashboardDonutChart"></canvas>
      <div class="absolute inset-0 flex items-center justify-center">
        <div class="text-center"><p class="text-lg font-bold text-[#171a1f]">24</p><p class="text-xs text-[#565d6d]">murid</p></div>
      </div>
    </div>
    <div class="grid grid-cols-2 gap-y-3 gap-x-2">
      <div class="flex items-center gap-2">
        <div class="w-3 h-3 bg-[#63e98f] rounded-full flex-shrink-0"></div>
        <span class="text-xs text-[#565d6d]">Terampil (T)</span>
        <span class="text-xs font-bold ml-auto">45%</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-3 h-3 bg-[#86d2f9] rounded-full flex-shrink-0"></div>
        <span class="text-xs text-[#565d6d]">Paham (P)</span>
        <span class="text-xs font-bold ml-auto">30%</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-3 h-3 bg-[#F2930D] rounded-full flex-shrink-0"></div>
        <span class="text-xs text-[#565d6d]">Belajar (B)</span>
        <span class="text-xs font-bold ml-auto">15%</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-3 h-3 bg-[#E2E8F0] rounded-full flex-shrink-0"></div>
        <span class="text-xs text-[#565d6d]">Kenal (K)</span>
        <span class="text-xs font-bold ml-auto">10%</span>
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
    <div class="relative w-full md:w-80">
      <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-[#565d6d]">
        <iconify-icon icon="lucide:search" width="16"></iconify-icon>
      </div>
      <input type="text" placeholder="Cari nama murid..." class="w-full pl-10 pr-4 py-2 bg-white border border-[#dee1e6]/60 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20">
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-[#f3f4f6]/30 text-[#565d6d] text-sm font-semibold border-b border-[#dee1e6]">
          <th class="px-6 py-4">Nama Murid</th>
          <th class="px-6 py-4">Level Saat Ini</th>
          <th class="px-6 py-4">Status Belajar</th>
          <th class="px-6 py-4">Progres Materi</th>
          <th class="px-6 py-4">Terakhir Update</th>
          <th class="px-6 py-4 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#dee1e6]">
        @php
          $murid = [
            ['nama' => 'Ananda Rizky',   'id' => 'BIMBA-1001', 'img' => 'IMG_18.webp', 'level' => 3, 'status' => 'T - Terampil',  'status_bg' => 'border border-[#dee1e6]', 'progres' => 85, 'update' => '2 Jam lalu'],
            ['nama' => 'Budi Santoso',   'id' => 'BIMBA-2002', 'img' => 'IMG_19.webp', 'level' => 2, 'status' => 'P - Paham',     'status_bg' => 'border border-[#dee1e6]', 'progres' => 45, 'update' => '1 Hari lalu'],
            ['nama' => 'Citra Lestari',  'id' => 'BIMBA-3003', 'img' => 'IMG_20.webp', 'level' => 4, 'status' => 'T - Terampil',  'status_bg' => 'border border-[#dee1e6]', 'progres' => 92, 'update' => '5 Jam lalu'],
            ['nama' => 'Deni Pratama',   'id' => 'BIMBA-4004', 'img' => 'IMG_21.webp', 'level' => 1, 'status' => 'B - Belajar',   'status_bg' => 'border border-[#dee1e6]', 'progres' => 20, 'update' => '3 Hari lalu'],
            ['nama' => 'Eka Wijaya',     'id' => 'BIMBA-5005', 'img' => 'IMG_22.webp', 'level' => 2, 'status' => 'K - Kenal',    'status_bg' => 'bg-[#f3f4f6]',            'progres' => 10, 'update' => 'Baru saja'],
          ];
        @endphp
        @foreach($murid as $m)
        <tr class="hover:bg-gray-50/50">
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <img src="{{ asset('assets/' . $m['img']) }}" class="w-10 h-10 rounded-full object-cover" alt="{{ $m['nama'] }}">
              <div>
                <p class="text-sm font-semibold text-[#171a1f]">{{ $m['nama'] }}</p>
                <p class="text-xs text-[#565d6d]">ID: {{ $m['id'] }}</p>
              </div>
            </div>
          </td>
          <td class="px-6 py-4">
            <span class="px-3 py-1 bg-[#f3f4f6] rounded-full text-xs font-medium text-[#1e2128]">Level {{ $m['level'] }}</span>
          </td>
          <td class="px-6 py-4">
            <span class="px-3 py-1 {{ $m['status_bg'] }} rounded-full text-xs font-bold text-[#171a1f]">{{ $m['status'] }}</span>
          </td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="w-24 h-2 bg-[#f3f4f6] rounded-full overflow-hidden">
                <div class="h-full bg-[#3d8af5]" style="width: {{ $m['progres'] }}%"></div>
              </div>
              <span class="text-xs font-bold text-[#565d6d]">{{ $m['progres'] }}%</span>
            </div>
          </td>
          <td class="px-6 py-4 text-sm text-[#565d6d]">{{ $m['update'] }}</td>
          <td class="px-6 py-4 text-right">
            <div class="flex justify-end gap-1">
              <button class="p-1.5 hover:bg-gray-100 rounded text-[#3d8af5]"><iconify-icon icon="lucide:pencil" width="14"></iconify-icon></button>
              <button class="p-1.5 hover:bg-gray-100 rounded text-[#565d6d]"><iconify-icon icon="lucide:eye" width="14"></iconify-icon></button>
              <button class="p-1.5 hover:bg-gray-100 rounded text-[#565d6d]"><iconify-icon icon="lucide:more-vertical" width="14"></iconify-icon></button>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="p-4 bg-[#fafafb]/30 border-t border-[#dee1e6] flex flex-col sm:flex-row items-center justify-between gap-4">
    <span class="text-xs text-[#565d6d] font-roboto">Menampilkan 5 dari 24 murid aktif</span>
    <div class="flex gap-2">
      <button class="px-4 py-1.5 bg-white border border-[#dee1e6] rounded-lg text-sm font-medium text-[#171a1f] opacity-50 cursor-not-allowed">Sebelumnya</button>
      <button class="px-4 py-1.5 bg-white border border-[#dee1e6] rounded-lg text-sm font-medium text-[#171a1f] hover:bg-gray-50">Berikutnya</button>
    </div>
  </div>
</div>
@endsection

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
@endpush

@push('scripts')
<script>
// --- Line Chart: Kemajuan Level Siswa (Level 1 turun, Level 4 naik) ---
const ctxLine = document.getElementById('dashboardLineChart').getContext('2d');
new Chart(ctxLine, {
  type: 'line',
  data: {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
    datasets: [
      {
        label: 'Level 1',
        data: [10, 9, 7, 5, 3, 1],
        borderColor: '#3d8af5',
        backgroundColor: 'rgba(61,138,245,0.08)',
        borderWidth: 2.5,
        fill: true,
        tension: 0.4,
        pointRadius: 3,
        pointHoverRadius: 5,
        pointBackgroundColor: '#3d8af5'
      },
      {
        label: 'Level 4',
        data: [0, 1, 2, 4, 7, 12],
        borderColor: '#F2930D',
        backgroundColor: 'rgba(242,147,13,0.08)',
        borderWidth: 2.5,
        fill: true,
        tension: 0.4,
        pointRadius: 3,
        pointHoverRadius: 5,
        pointBackgroundColor: '#F2930D'
      }
    ]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { display: false }, ticks: { font: { size: 11, family: 'Roboto' }, color: '#9095a0' } },
      y: { beginAtZero: true, max: 12, grid: { color: 'rgba(222,225,230,0.5)' }, ticks: { font: { size: 11, family: 'Roboto' }, color: '#9095a0' } }
    }
  }
});

// --- Donut Chart: Status Kompetensi ---
const ctxDonut = document.getElementById('dashboardDonutChart').getContext('2d');
new Chart(ctxDonut, {
  type: 'doughnut',
  data: {
    labels: ['Terampil (T)', 'Paham (P)', 'Belajar (B)', 'Kenal (K)'],
    datasets: [{
      data: [45, 30, 15, 10],
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
