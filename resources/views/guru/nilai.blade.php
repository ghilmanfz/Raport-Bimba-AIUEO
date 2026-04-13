@extends('layouts.guru')

@section('title', 'Pengolahan Nilai - E-Rapor BiMBA')
@section('page-title', 'Pengolahan Nilai')

@section('content')
<!-- Page Header Card -->
<div class="bg-white rounded-2xl p-6 border border-[#dee1e6] main-shadow flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
  <div class="flex items-start gap-4">
    <div class="w-12 h-12 bg-[#3d8af5]/10 rounded-xl flex items-center justify-center flex-shrink-0">
      <iconify-icon icon="lucide:file-text" width="22" class="text-[#3d8af5]"></iconify-icon>
    </div>
    <div>
      <h1 class="text-2xl font-bold text-[#171a1f]">Pengolahan Nilai Progres</h1>
      <p class="text-sm text-[#565d6d]">Input hasil belajar harian dan evaluasi materi murid.</p>
    </div>
  </div>
  <div class="flex flex-col sm:flex-row items-center gap-3">
    <div class="relative w-full sm:w-64">
      <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-[#565d6d]">
        <iconify-icon icon="lucide:user" width="16"></iconify-icon>
      </div>
      <form method="GET" action="{{ route('guru.nilai') }}" id="form-filter">
        <input type="hidden" name="skill" value="{{ $selectedSkill }}">
        <input type="hidden" name="level" value="{{ $selectedLevel }}">
        <select name="student_id" onchange="document.getElementById('form-filter').submit()" class="w-full pl-10 pr-10 py-2.5 bg-white border border-[#dee1e6] rounded-xl text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20">
          @foreach($students as $s)
            <option value="{{ $s->id }}" {{ $selectedStudent?->id == $s->id ? 'selected' : '' }}>{{ $s->name }} ({{ $s->classroom?->level ?? '-' }})</option>
          @endforeach
        </select>
      </form>
      <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-[#565d6d]">
        <iconify-icon icon="lucide:chevron-down" width="14"></iconify-icon>
      </div>
    </div>
  </div>
</div>

<!-- Level Tabs -->
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
  <div class="bg-[#f3f4f6] p-1 rounded-full flex w-fit">
    @foreach(['Level 1','Level 2','Level 3','Level 4'] as $lvl)
    <button type="button" onclick="document.querySelector('#form-filter input[name=level]').value='{{ $lvl }}'; document.getElementById('form-filter').submit();"
      class="px-6 py-1.5 rounded-full text-sm font-medium {{ $selectedLevel === $lvl ? 'bg-[#3d8af5] text-white shadow-sm' : 'text-[#565d6d] hover:text-[#171a1f]' }}">{{ $lvl }}</button>
    @endforeach
  </div>
  <div class="flex items-center gap-2 px-4 py-2 border border-[#dee1e6] rounded-xl bg-white/50 text-xs font-medium text-[#171a1f]">
    <iconify-icon icon="lucide:info" width="16" class="text-[#3d8af5]"></iconify-icon>
    Status penilaian dihitung otomatis berdasarkan kelengkapan tanggal progres.
  </div>
</div>

<!-- Subject Tabs -->
<div class="border-b border-[#dee1e6] flex gap-8 mb-6">
  @php $skillIcons = ['baca' => 'lucide:book-open', 'tulis' => 'lucide:pencil', 'hitung' => 'lucide:calculator']; @endphp
  @foreach(['baca' => 'Baca', 'tulis' => 'Tulis', 'hitung' => 'Hitung'] as $key => $label)
  <button type="button" onclick="document.querySelector('#form-filter input[name=skill]').value='{{ $key }}'; document.getElementById('form-filter').submit();"
    class="flex items-center gap-2 px-2 py-3 border-b-2 font-semibold text-base {{ $selectedSkill === $key ? 'border-[#3d8af5] text-[#3d8af5]' : 'border-transparent text-[#565d6d] hover:text-[#171a1f]' }}">
    <iconify-icon icon="{{ $skillIcons[$key] }}" width="18"></iconify-icon>
    {{ $label }}
  </button>
  @endforeach
</div>

<!-- Table Card -->
<form method="POST" action="{{ route('guru.nilai.store') }}">
@csrf
<input type="hidden" name="student_id" value="{{ $selectedStudent?->id }}">
<div class="bg-white rounded-2xl border border-[#dee1e6] main-shadow overflow-hidden mb-6">
  <div class="p-6 border-b border-[#dee1e6] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h2 class="text-lg font-semibold text-[#171a1f]">Modul Pembelajaran: {{ ucfirst($selectedSkill) }} - {{ $selectedLevel }}</h2>
      <p class="text-sm text-[#565d6d]">Detail progres materi untuk {{ $selectedStudent?->name ?? '-' }}</p>
    </div>
    <button type="submit" class="px-6 py-2.5 bg-[#3d8af5] text-white rounded-xl text-sm font-medium shadow-md hover:bg-blue-600 flex items-center gap-2">
      <iconify-icon icon="lucide:save" width="16"></iconify-icon>
      Simpan Semua
    </button>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-[#f3f4f6]/50 border-b border-[#dee1e6] text-sm font-semibold text-[#171a1f]">
          <th class="px-6 py-4 min-w-[280px]">Nama Materi</th>
          <th class="px-6 py-4 min-w-[160px]">Tanggal Mulai</th>
          <th class="px-6 py-4 min-w-[160px]">Tanggal Paham</th>
          <th class="px-6 py-4 min-w-[160px]">Tanggal Terampil</th>
          <th class="px-6 py-4 text-center min-w-[140px]">Status (K/B/P/T)</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#dee1e6]">
        @forelse($progress as $i => $p)
        <tr class="hover:bg-gray-50/50">
          <td class="px-6 py-4">
            <input type="hidden" name="progress[{{ $i }}][material_id]" value="{{ $p['material']->id }}">
            <div class="flex items-center gap-3">
              <div class="w-2 h-2 rounded-full bg-[#3d8af5] flex-shrink-0"></div>
              <span class="text-sm font-medium text-[#171a1f]">{{ $p['material']->name }}</span>
            </div>
          </td>
          <td class="px-6 py-4">
            <input type="date" name="progress[{{ $i }}][start_date]" value="{{ $p['start_date'] }}" class="w-full px-3 py-2 border border-[#dee1e6] rounded-lg text-sm focus:ring-1 focus:ring-[#3d8af5] outline-none">
          </td>
          <td class="px-6 py-4">
            <input type="date" name="progress[{{ $i }}][understand_date]" value="{{ $p['understand_date'] }}" class="w-full px-3 py-2 border border-[#dee1e6] rounded-lg text-sm focus:ring-1 focus:ring-[#3d8af5] outline-none">
          </td>
          <td class="px-6 py-4">
            <input type="date" name="progress[{{ $i }}][skilled_date]" value="{{ $p['skilled_date'] }}" class="w-full px-3 py-2 border border-[#dee1e6] rounded-lg text-sm focus:ring-1 focus:ring-[#3d8af5] outline-none">
          </td>
          <td class="px-6 py-4 text-center">
            @php
              $badge = match($p['status']) {
                'T' => 'bg-[#A7F3D0] border border-[#6EE7B7] text-[#047857]',
                'P' => 'bg-[#BAE6FD] border border-[#7DD3FC] text-[#0369A1]',
                'B' => 'bg-[#FDE68A] border border-[#FCD34D] text-[#B45309]',
                default => 'bg-[#E2E8F0] border border-[#CBD5E1] text-[#334155]',
              };
              $label = match($p['status']) {
                'T' => 'T - Terampil',
                'P' => 'P - Paham',
                'B' => 'B - Belum',
                default => 'K - Kenal',
              };
            @endphp
            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $badge }}">{{ $label }}</span>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="px-6 py-8 text-center text-sm text-[#565d6d]">Pilih murid untuk melihat progres materi.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="p-4 bg-[#f3f4f6]/30 border-t border-[#dee1e6] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <p class="text-sm text-[#565d6d] font-roboto">Menampilkan <span class="font-semibold text-[#171a1f]">{{ count($progress) }}</span> materi di aspek <span class="font-semibold text-[#171a1f]">{{ ucfirst($selectedSkill) }}</span></p>
  </div>
</div>
</form>

@if(session('success'))
<div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg text-sm font-medium z-50" x-data x-init="setTimeout(() => $el.remove(), 3000)">
  {{ session('success') }}
</div>
@endif

<!-- Status Legend Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
  <div class="bg-[#F1F6FE] p-6 rounded-xl border border-[#dee1e6]/30 text-center">
    <h3 class="text-2xl font-bold text-[#3d8af5] mb-1">K</h3>
    <p class="text-xs font-bold text-[#3d8af5]/70 tracking-widest uppercase mb-2">Kenal</p>
    <p class="text-sm text-[#3d8af5]/60">Belum ada progres</p>
  </div>
  <div class="bg-[#FCF0E3] p-6 rounded-xl border border-[#dee1e6]/30 text-center">
    <h3 class="text-2xl font-bold text-[#171a1f] mb-1">B</h3>
    <p class="text-xs font-bold text-[#171a1f]/70 tracking-widest uppercase mb-2">Belum</p>
    <p class="text-sm text-[#171a1f]/60">Baru mulai belajar</p>
  </div>
  <div class="bg-[#E1F4FE] p-6 rounded-xl border border-[#dee1e6]/30 text-center">
    <h3 class="text-2xl font-bold text-[#171a1f] mb-1">P</h3>
    <p class="text-xs font-bold text-[#171a1f]/70 tracking-widest uppercase mb-2">Paham</p>
    <p class="text-sm text-[#171a1f]/60">Materi dikuasai</p>
  </div>
  <div class="bg-[#DCFAE6] p-6 rounded-xl border border-[#dee1e6]/30 text-center">
    <h3 class="text-2xl font-bold text-[#171a1f] mb-1">T</h3>
    <p class="text-xs font-bold text-[#171a1f]/70 tracking-widest uppercase mb-2">Terampil</p>
    <p class="text-sm text-[#171a1f]/60">Dapat mengaplikasikan</p>
  </div>
</div>
@endsection
