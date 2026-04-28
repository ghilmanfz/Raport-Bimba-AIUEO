@extends('layouts.wali')

@section('title', 'Riwayat Rapor - E-Rapor BiMBA AIUEO')
@section('page-title', 'Riwayat Rapor')

@push('head')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<style>
  .font-poppins { font-family: 'Poppins', sans-serif; }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
  <div>
    <h1 class="text-2xl font-bold text-[#171a1f] font-poppins">Riwayat Rapor</h1>
    <p class="text-sm text-[#565d6d]">Lihat perkembangan rapor dari periode pertama hingga terakhir</p>
  </div>
</div>

<!-- Child Selector -->
<form method="GET" action="{{ route('wali.riwayat') }}" class="bg-white rounded-2xl border border-[#dee1e6] main-shadow p-5 mb-6 grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
  <div class="md:col-span-8 space-y-1.5">
    <label class="text-xs font-semibold text-[#565d6d] uppercase tracking-wider">Pilih Anak</label>
    <div class="relative">
      <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-[#565d6d]">
        <iconify-icon icon="lucide:user" width="16"></iconify-icon>
      </div>
      <select name="student_id" class="w-full pl-9 pr-10 py-2.5 border border-[#dee1e6] rounded-xl text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20 bg-white">
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
    <button type="submit" class="w-full py-2.5 bg-[#2563EB] text-white rounded-xl text-sm font-medium shadow-md hover:bg-blue-600 flex items-center justify-center gap-2">
      <iconify-icon icon="lucide:search" width="16"></iconify-icon>
      Tampilkan
    </button>
  </div>
</form>

@if($student && count($riwayatData) > 0)
<!-- Student Info Card -->
<div class="bg-white rounded-2xl border border-[#dee1e6] p-6 mb-6 shadow-sm">
  <div class="flex items-center gap-4">
    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-xl font-bold">
      {{ collect(explode(' ', $student->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('') }}
    </div>
    <div>
      <h2 class="text-xl font-bold text-[#171a1f]">{{ $student->name }}</h2>
      <div class="flex flex-wrap gap-3 mt-2">
        <div class="flex items-center gap-2 text-xs text-[#565d6d]">
          <iconify-icon icon="lucide:layers" width="14" class="text-[#2563EB]"></iconify-icon>
          {{ $student->classroom?->name ?? '-' }}
        </div>
        <div class="flex items-center gap-2 text-xs text-[#565d6d]">
          <iconify-icon icon="lucide:calendar" width="14" class="text-[#2563EB]"></iconify-icon>
          Bergabung: {{ \Carbon\Carbon::parse($student->join_date)->format('d M Y') }}
        </div>
        <div class="flex items-center gap-2 text-xs text-[#565d6d]">
          <iconify-icon icon="lucide:file-text" width="14" class="text-[#2563EB]"></iconify-icon>
          {{ count($riwayatData) }} Periode Rapor
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Progress Chart Overview -->
<div class="bg-white rounded-2xl border border-[#dee1e6] p-6 mb-6 shadow-sm">
  <div class="flex items-center gap-2 mb-4">
    <iconify-icon icon="lucide:trending-up" width="20" class="text-[#2563EB]"></iconify-icon>
    <h3 class="text-lg font-bold text-[#171a1f]">Grafik Perkembangan</h3>
  </div>
  <div class="h-64">
    <canvas id="riwayatChart"></canvas>
  </div>
</div>

<!-- Report History Timeline -->
<div class="space-y-4">
  @foreach($riwayatData as $index => $period)
  <div class="bg-white rounded-2xl border border-[#dee1e6] shadow-sm overflow-hidden {{ $period['is_current'] ? 'ring-2 ring-[#2563EB]/20' : '' }}">
    <!-- Period Header -->
    <div class="bg-gradient-to-r from-blue-500 via-yellow-500 to-red-500 p-4 text-white">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center font-bold text-lg">
            {{ $period['period'] }}
          </div>
          <div>
            <h3 class="font-bold text-base">Periode {{ $period['period'] }}</h3>
            <p class="text-xs opacity-90">{{ $period['start_date'] }} - {{ $period['end_date'] }}</p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          @if($period['is_current'])
          <span class="bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-semibold">
            <iconify-icon icon="lucide:star" width="12" class="inline"></iconify-icon> Periode Aktif
          </span>
          @endif
          <a href="{{ route('wali.rapor.periode', ['student_id' => $student->id, 'period_end' => $period['end_date_raw'], 'period_number' => $period['period']]) }}" 
             target="_blank"
             class="bg-white/20 hover:bg-white/30 backdrop-blur-sm px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition-colors">
            <iconify-icon icon="lucide:printer" width="14"></iconify-icon>
            Cetak
          </a>
        </div>
      </div>
    </div>

    <!-- Period Stats -->
    <div class="p-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
        <!-- Average Card -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 border border-purple-200">
          <div class="flex items-center gap-2 mb-2">
            <iconify-icon icon="lucide:award" width="18" class="text-purple-600"></iconify-icon>
            <span class="text-xs font-semibold text-purple-900">Rata-rata</span>
          </div>
          <div class="text-2xl font-bold text-purple-700">{{ $period['average'] }}%</div>
          <div class="mt-2 w-full h-2 bg-purple-200 rounded-full overflow-hidden">
            <div class="h-full bg-purple-600" style="width: {{ $period['average'] }}%"></div>
          </div>
        </div>

        <!-- Baca Card -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 border border-green-200">
          <div class="flex items-center gap-2 mb-2">
            <iconify-icon icon="lucide:book-open" width="18" class="text-green-600"></iconify-icon>
            <span class="text-xs font-semibold text-green-900">Membaca</span>
          </div>
          <div class="text-2xl font-bold text-green-700">{{ $period['skills']['baca']['percentage'] }}%</div>
          <div class="mt-2 w-full h-2 bg-green-200 rounded-full overflow-hidden">
            <div class="h-full bg-green-600" style="width: {{ $period['skills']['baca']['percentage'] }}%"></div>
          </div>
          <p class="text-[10px] text-green-700 mt-1">{{ $period['skills']['baca']['skilled'] }}/{{ $period['skills']['baca']['total'] }} materi terampil</p>
        </div>

        <!-- Tulis Card -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
          <div class="flex items-center gap-2 mb-2">
            <iconify-icon icon="lucide:pencil" width="18" class="text-blue-600"></iconify-icon>
            <span class="text-xs font-semibold text-blue-900">Menulis</span>
          </div>
          <div class="text-2xl font-bold text-blue-700">{{ $period['skills']['tulis']['percentage'] }}%</div>
          <div class="mt-2 w-full h-2 bg-blue-200 rounded-full overflow-hidden">
            <div class="h-full bg-blue-600" style="width: {{ $period['skills']['tulis']['percentage'] }}%"></div>
          </div>
          <p class="text-[10px] text-blue-700 mt-1">{{ $period['skills']['tulis']['skilled'] }}/{{ $period['skills']['tulis']['total'] }} materi terampil</p>
        </div>

        <!-- Hitung Card -->
        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-4 border border-yellow-200">
          <div class="flex items-center gap-2 mb-2">
            <iconify-icon icon="lucide:calculator" width="18" class="text-yellow-600"></iconify-icon>
            <span class="text-xs font-semibold text-yellow-900">Berhitung</span>
          </div>
          <div class="text-2xl font-bold text-yellow-700">{{ $period['skills']['hitung']['percentage'] }}%</div>
          <div class="mt-2 w-full h-2 bg-yellow-200 rounded-full overflow-hidden">
            <div class="h-full bg-yellow-600" style="width: {{ $period['skills']['hitung']['percentage'] }}%"></div>
          </div>
          <p class="text-[10px] text-yellow-700 mt-1">{{ $period['skills']['hitung']['skilled'] }}/{{ $period['skills']['hitung']['total'] }} materi terampil</p>
        </div>
      </div>
    </div>
  </div>
  @endforeach
</div>

@elseif($student && count($riwayatData) == 0)
<!-- No Data -->
<div class="bg-white rounded-2xl border border-[#dee1e6] p-12 text-center shadow-sm">
  <iconify-icon icon="lucide:calendar-x" width="48" class="text-[#dee1e6] mx-auto mb-4"></iconify-icon>
  <h3 class="text-lg font-bold text-[#171a1f] mb-2">Belum Ada Riwayat</h3>
  <p class="text-sm text-[#565d6d]">{{ $student->name }} belum memiliki riwayat rapor.</p>
</div>

@else
<!-- Select Student First -->
<div class="bg-white rounded-2xl border border-[#dee1e6] p-12 text-center shadow-sm">
  <iconify-icon icon="lucide:file-text" width="48" class="text-[#dee1e6] mx-auto mb-4"></iconify-icon>
  <h3 class="text-lg font-bold text-[#171a1f] mb-2">Pilih Anak</h3>
  <p class="text-sm text-[#565d6d]">Silakan pilih anak di atas untuk melihat riwayat rapor.</p>
</div>
@endif

@endsection

@if($student && count($riwayatData) > 0)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const ctx = document.getElementById('riwayatChart');
  if (!ctx) return;

  const periods = @json(array_column($riwayatData, 'period'));
  const bacaData = @json(array_column(array_column($riwayatData, 'skills'), 'baca'));
  const tulisData = @json(array_column(array_column($riwayatData, 'skills'), 'tulis'));
  const hitungData = @json(array_column(array_column($riwayatData, 'skills'), 'hitung'));
  const avgData = @json(array_column($riwayatData, 'average'));

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: periods.map(p => 'Periode ' + p),
      datasets: [
        {
          label: 'Rata-rata',
          data: avgData,
          borderColor: '#9333EA',
          backgroundColor: 'rgba(147, 51, 234, 0.1)',
          borderWidth: 3,
          tension: 0.4,
          fill: true
        },
        {
          label: 'Membaca',
          data: bacaData.map(d => d.percentage),
          borderColor: '#DC2626',
          backgroundColor: 'rgba(34, 197, 94, 0.1)',
          borderWidth: 2,
          tension: 0.4,
          fill: false
        },
        {
          label: 'Menulis',
          data: tulisData.map(d => d.percentage),
          borderColor: '#3B82F6',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          borderWidth: 2,
          tension: 0.4,
          fill: false
        },
        {
          label: 'Berhitung',
          data: hitungData.map(d => d.percentage),
          borderColor: '#EAB308',
          backgroundColor: 'rgba(234, 179, 8, 0.1)',
          borderWidth: 2,
          tension: 0.4,
          fill: false
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
          position: 'bottom',
          labels: {
            usePointStyle: true,
            padding: 15,
            font: { size: 11, weight: '600' }
          }
        },
        tooltip: {
          backgroundColor: 'rgba(0, 0, 0, 0.8)',
          padding: 12,
          titleFont: { size: 13, weight: 'bold' },
          bodyFont: { size: 12 },
          callbacks: {
            label: function(context) {
              return context.dataset.label + ': ' + context.parsed.y + '%';
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          max: 100,
          ticks: {
            callback: function(value) {
              return value + '%';
            },
            font: { size: 11 }
          },
          grid: {
            color: 'rgba(0, 0, 0, 0.05)'
          }
        },
        x: {
          grid: {
            display: false
          },
          ticks: {
            font: { size: 11 }
          }
        }
      }
    }
  });
});
</script>
@endpush
@endif
