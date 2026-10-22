@extends('layouts.wali')

@section('title', 'Riwayat Rapor - E-Rapor BiMBA AIUEO')
@section('page-title', 'Riwayat Rapor')

@push('head')
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
      <select name="student_id" class="w-full pl-9 pr-10 py-2.5 border border-[#dee1e6] rounded-xl text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-[#F97316]/20 bg-white">
        @foreach($children as $c)
          <option value="{{ $c->id }}" {{ $student?->id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
        @endforeach
      </select>
      <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-[#565d6d]">
        <iconify-icon icon="lucide:chevron-down" width="14"></iconify-icon>
      </div>
    </div>
  </div>
  <div class="md:col-span-4">
    <button type="submit" class="w-full py-2.5 bg-[#F97316] text-white rounded-xl text-sm font-medium shadow-md hover:bg-orange-600 flex items-center justify-center gap-2">
      <iconify-icon icon="lucide:search" width="16"></iconify-icon>
      Tampilkan
    </button>
  </div>
</form>

@if($student && count($riwayatData) > 0)
<!-- Student Info Card -->
<div class="bg-white rounded-2xl border border-[#dee1e6] p-6 mb-6 shadow-sm">
  <div class="flex items-center gap-4">
    <div class="w-16 h-16 shrink-0 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-xl font-bold">
      {{ collect(explode(' ', $student->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('') }}
    </div>
    <div class="min-w-0">
      <h2 class="text-xl font-bold text-[#171a1f] break-words">{{ $student->name }}</h2>
      <div class="flex flex-wrap gap-3 mt-2">
        <div class="flex items-center gap-2 text-xs text-[#565d6d]">
          <iconify-icon icon="lucide:calendar" width="14" class="text-[#F97316]"></iconify-icon>
          Bergabung: {{ \Carbon\Carbon::parse($student->join_date)->format('d M Y') }}
        </div>
        <div class="flex items-center gap-2 text-xs text-[#565d6d]">
          <iconify-icon icon="lucide:file-text" width="14" class="text-[#F97316]"></iconify-icon>
          {{ count($riwayatData) }} Periode Rapor
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Report History Timeline -->
<div class="space-y-4">
  @foreach($riwayatData as $index => $period)
  <div class="bg-white rounded-lg border border-[#dee1e6] shadow-sm overflow-hidden {{ $period['is_current'] ? 'ring-2 ring-[#F97316]/20' : '' }}">
    <!-- Period Header -->
    <div class="bg-[#FFF7ED] border-b border-[#dee1e6] p-4 text-[#171a1f]">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 shrink-0 bg-orange-100 text-orange-700 rounded-full flex items-center justify-center font-bold text-lg">
            {{ $period['period'] }}
          </div>
          <div>
            <h3 class="font-bold text-base">Periode {{ $period['period'] }}</h3>
            <p class="text-xs opacity-90">{{ $period['start_date'] }} - {{ $period['end_date'] }}</p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          @if($period['is_current'])
          <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-xs font-semibold">
            <iconify-icon icon="lucide:star" width="12" class="inline"></iconify-icon> Periode Aktif
          </span>
          @endif
          <a href="{{ route('wali.rapor.periode', ['student_id' => $student->id, 'period_end' => $period['end_date_raw'], 'period_number' => $period['period']]) }}" 
             target="_blank"
             class="bg-white border border-[#dee1e6] hover:bg-orange-50 px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition-colors">
            <iconify-icon icon="lucide:printer" width="14"></iconify-icon>
            Cetak
          </a>
        </div>
      </div>
    </div>

    <div class="p-5 sm:p-6">
      @include('rapor.skill-summary', ['skillReport' => $period['skills']])
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
