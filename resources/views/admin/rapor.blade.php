@extends('layouts.admin')

@section('title', 'Laporan Rapor - E-Rapor BiMBA')
@section('page-title', 'Laporan Rapor')

@section('content')
<div class="mb-6">
  <h1 class="text-2xl lg:text-3xl font-bold">Laporan Rapor</h1>
  <p class="text-sm text-[#565d6d] mt-1">Periode rapor yang berakhir pada tahun {{ $year }}.</p>
</div>

<form method="GET" action="{{ route('admin.rapor') }}" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-12 gap-4 items-end mb-6">
  <div class="xl:col-span-2">
    <label for="report-year" class="block text-xs font-semibold text-[#565d6d] mb-1.5">Tahun</label>
    <select id="report-year" name="year" class="w-full px-3 py-2.5 border border-[#dee1e6] rounded-lg text-sm bg-white">
      @foreach($years as $option)<option value="{{ $option }}" @selected($year === $option)>{{ $option }}</option>@endforeach
    </select>
  </div>
  <div class="xl:col-span-3">
    <label for="report-teacher" class="block text-xs font-semibold text-[#565d6d] mb-1.5">Kelompok / Kelas Saat Ini</label>
    <select id="report-teacher" name="teacher_id" class="w-full px-3 py-2.5 border border-[#dee1e6] rounded-lg text-sm bg-white">
      <option value="">Semua Kelas</option>
      @foreach($teachers as $teacher)<option value="{{ $teacher->id }}" @selected((string)$selectedTeacher === (string)$teacher->id)>Kelas {{ $teacher->user?->name ?? $teacher->nip }}</option>@endforeach
    </select>
  </div>
  <div class="xl:col-span-2">
    <label for="report-status" class="block text-xs font-semibold text-[#565d6d] mb-1.5">Status Murid Saat Ini</label>
    <select id="report-status" name="student_status" class="w-full px-3 py-2.5 border border-[#dee1e6] rounded-lg text-sm bg-white">
      @foreach($studentStatusOptions as $value => $label)<option value="{{ $value }}" @selected($studentStatus === $value)>{{ $label }}</option>@endforeach
    </select>
  </div>
  <div class="xl:col-span-3">
    <label for="report-search" class="block text-xs font-semibold text-[#565d6d] mb-1.5">Cari Anak</label>
    <input id="report-search" type="search" name="search" value="{{ $search }}" placeholder="Nama atau NIS" maxlength="100" class="w-full px-3 py-2.5 border border-[#dee1e6] rounded-lg text-sm bg-white">
  </div>
  <button type="submit" class="xl:col-span-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#F97316] text-white rounded-lg text-sm font-semibold hover:bg-[#EA580C]">
    <iconify-icon icon="lucide:filter" width="17"></iconify-icon> Terapkan
  </button>
</form>

<div class="border-y border-[#dee1e6] bg-white">
  <div class="py-4 px-4 flex items-center justify-between gap-3">
    <h2 class="text-lg font-semibold">Daftar Rapor {{ $year }}</h2>
    <span class="text-sm text-[#565d6d]">{{ $raporRows->total() }} rapor</span>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-left min-w-[880px] text-sm">
      <thead class="bg-[#fafafb] text-xs text-[#565d6d] border-y border-[#dee1e6]">
        <tr><th class="px-4 py-3">Anak</th><th class="px-4 py-3">Guru Pembimbing Saat Ini</th><th class="px-4 py-3">Periode</th><th class="px-4 py-3">Penilaian Modul</th><th class="px-4 py-3">Hari Tercatat</th><th class="px-4 py-3">Status Periode</th><th class="px-4 py-3 text-right">Aksi</th></tr>
      </thead>
      <tbody class="divide-y divide-[#edf0f3]">
        @forelse($raporRows as $row)
          @php
            $student = $row['student'];
            $period = $row['period'];
            $params = ['student' => $student->id, 'year' => $year, 'period_number' => $period['number'], 'period_end' => $period['cutoff']->toDateString()];
          @endphp
          <tr class="hover:bg-[#fafafb]">
            <td class="px-4 py-4"><p class="font-semibold">{{ $student->name }}</p><p class="text-xs text-[#565d6d] mt-1">{{ $student->nis }} &middot; {{ $student->status_label }}</p></td>
            <td class="px-4 py-4">{{ $student->teacher?->user?->name ?? '-' }}</td>
            <td class="px-4 py-4"><p class="font-medium">Periode {{ $period['number'] }}</p><p class="text-xs text-[#565d6d] mt-1">{{ $period['start']->translatedFormat('d M Y') }} - {{ $period['end']->translatedFormat('d M Y') }}</p></td>
            <td class="px-4 py-4">{{ $row['assessmentCount'] }}</td>
            <td class="px-4 py-4">{{ $row['attendanceCount'] }}</td>
            <td class="px-4 py-4"><span class="text-xs font-semibold {{ $period['end']->lt(today()) ? 'text-green-700' : 'text-[#C2410C]' }}">{{ $period['end']->lt(today()) ? 'Selesai' : 'Berjalan' }}</span></td>
            <td class="px-4 py-4"><div class="flex justify-end gap-1">
              <a href="{{ route('admin.rapor.show', $params) }}" title="Lihat rapor" aria-label="Lihat rapor {{ $student->name }}, periode {{ $period['number'] }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-[#C2410C] hover:bg-orange-50"><iconify-icon icon="lucide:eye" width="18"></iconify-icon></a>
              <a href="{{ route('admin.rapor.pdf', $params) }}" title="Unduh PDF" aria-label="Unduh PDF rapor {{ $student->name }}, periode {{ $period['number'] }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-[#565d6d] hover:bg-gray-100"><iconify-icon icon="lucide:download" width="18"></iconify-icon></a>
            </div></td>
          </tr>
        @empty
          <tr><td colspan="7" class="px-4 py-12 text-center text-[#565d6d]">Belum ada data rapor untuk filter ini.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
<div class="mt-5">{{ $raporRows->links() }}</div>
@endsection
