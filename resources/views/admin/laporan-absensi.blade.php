@extends('layouts.admin')

@section('title', 'Laporan Absensi - E-Rapor BiMBA')
@section('page-title', 'Laporan Absensi')

@section('styles')
@media print {
  aside, header, footer, .no-print { display: none !important; }
  body, main { background: white !important; margin: 0 !important; padding: 0 !important; }
  .print-card { box-shadow: none !important; border: 0 !important; }
}
@endsection

@section('content')
@php
  $reportQuery = array_filter(['month' => $month, 'classroom_id' => $selectedClassroom, 'student_id' => $selectedStudent, 'student_status' => $studentStatus]);
@endphp
<div class="no-print flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 mb-6">
  <div><h1 class="text-2xl lg:text-3xl font-bold tracking-tight">Laporan Absensi</h1><p class="text-sm text-[#565d6d] mt-1">Rekap kehadiran harian dan bulanan setiap anak.</p></div>
  <div class="flex flex-wrap gap-2">
    <a href="{{ route('admin.absensi.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 border border-[#dee1e6] bg-white rounded-xl text-sm font-semibold hover:bg-gray-50"><iconify-icon icon="lucide:clipboard-check" width="17"></iconify-icon>Input Harian</a>
    <a href="{{ route('admin.absensi.export', $reportQuery) }}" class="inline-flex items-center gap-2 px-4 py-2.5 border border-green-200 bg-green-50 text-green-700 rounded-xl text-sm font-semibold hover:bg-green-100"><iconify-icon icon="lucide:file-spreadsheet" width="17"></iconify-icon>CSV</a>
    <a href="{{ route('admin.absensi.pdf', $reportQuery) }}" class="inline-flex items-center gap-2 px-4 py-2.5 border border-red-200 bg-red-50 text-red-700 rounded-xl text-sm font-semibold hover:bg-red-100"><iconify-icon icon="lucide:file-text" width="17"></iconify-icon>PDF</a>
    <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#F97316] text-white rounded-xl text-sm font-semibold hover:bg-[#EA580C]"><iconify-icon icon="lucide:printer" width="17"></iconify-icon>Cetak</button>
  </div>
</div>

<form method="GET" action="{{ route('admin.absensi.report') }}" class="no-print bg-white rounded-2xl border border-[#dee1e6] main-shadow p-5 mb-6 grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
  <div class="md:col-span-2"><label class="block text-xs font-semibold text-[#565d6d] mb-1.5">Bulan</label><input type="month" name="month" value="{{ $month }}" class="w-full px-3 py-2.5 border border-[#dee1e6] rounded-xl text-sm"></div>
  <div class="md:col-span-2"><label class="block text-xs font-semibold text-[#565d6d] mb-1.5">Kelas Saat Ini</label><select name="classroom_id" class="w-full px-3 py-2.5 border border-[#dee1e6] rounded-xl text-sm bg-white"><option value="">Semua Kelas</option>@foreach($classrooms as $classroom)<option value="{{ $classroom->id }}" @selected((string)$selectedClassroom === (string)$classroom->id)>{{ $classroom->name }}</option>@endforeach</select></div>
  <div class="md:col-span-2"><label class="block text-xs font-semibold text-[#565d6d] mb-1.5">Status Murid</label><select name="student_status" class="w-full px-3 py-2.5 border border-[#dee1e6] rounded-xl text-sm bg-white">@foreach($studentStatusOptions as $value => $label)<option value="{{ $value }}" @selected($studentStatus === $value)>{{ $label }}</option>@endforeach</select></div>
  <div class="md:col-span-4"><label class="block text-xs font-semibold text-[#565d6d] mb-1.5">Anak</label><select name="student_id" class="w-full px-3 py-2.5 border border-[#dee1e6] rounded-xl text-sm bg-white"><option value="">Semua Anak</option>@foreach($studentOptions as $option)<option value="{{ $option->id }}" @selected((string)$selectedStudent === (string)$option->id)>{{ $option->name }} · {{ $option->status_label }} · {{ $option->classroom?->name ?? '-' }}</option>@endforeach</select></div>
  <div class="md:col-span-2"><button class="w-full px-4 py-2.5 bg-[#F97316] text-white rounded-xl text-sm font-semibold hover:bg-[#EA580C]">Terapkan</button></div>
</form>

<div class="print-card bg-white rounded-2xl border border-[#dee1e6] main-shadow p-5 lg:p-7 mb-6">
  <div class="text-center mb-6">
    <p class="text-xs font-bold tracking-[0.2em] text-[#F97316] uppercase">{{ $institutionName }}</p>
    <h2 class="text-2xl font-bold mt-1">Rekap Kehadiran {{ $monthLabel }}</h2>
    <p class="text-sm text-[#565d6d] mt-1">{{ $summaryRows->count() }} murid · Filter status: {{ $studentStatusLabel }}</p>
    @if ($institutionAddress)
      <p class="text-xs text-[#565d6d] mt-1">{{ $institutionAddress }}</p>
    @endif
  </div>

  @php $cards=[['Total Tercatat',$overall['total'],'#7C3AED','#F3E8FF'],['Hadir',$overall['hadir'],'#16A34A','#DCFCE7'],['Sakit',$overall['sakit'],'#2563EB','#DBEAFE'],['Izin',$overall['izin'],'#D97706','#FEF3C7'],['Alpa',$overall['alpa'],'#DC2626','#FEE2E2']]; @endphp
  <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-7">@foreach($cards as $card)<div class="rounded-xl border border-[#edf0f3] p-4" style="background:{{ $card[3] }}55"><p class="text-xs text-[#565d6d]">{{ $card[0] }}</p><p class="text-2xl font-bold" style="color:{{ $card[2] }}">{{ $card[1] }}</p></div>@endforeach</div>

  <div class="mb-6 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-xs leading-relaxed text-blue-800">
    Rasio hadir dihitung dari <strong>Hadir ÷ total hari yang sudah dicatat (H/S/I/A)</strong>. Murid yang belum memiliki catatan pada periode ini ditampilkan sebagai “Belum ada data”, bukan 0%.
  </div>

  <h3 class="text-lg font-bold mb-3">Rekap Bulanan per Anak</h3>
  <div class="overflow-x-auto mb-8"><table class="w-full text-left min-w-[960px] border border-[#dee1e6]"><thead class="bg-[#fafafb] text-xs uppercase text-[#565d6d]"><tr><th class="px-4 py-3">Anak</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Kelas Saat Ini</th><th class="px-3 py-3 text-center">H</th><th class="px-3 py-3 text-center">S</th><th class="px-3 py-3 text-center">I</th><th class="px-3 py-3 text-center">A</th><th class="px-3 py-3 text-center">Hari Tercatat</th><th class="px-4 py-3">Hadir/Tercatat</th></tr></thead><tbody class="divide-y divide-[#edf0f3]">
    @forelse($summaryRows as $row)<tr><td class="px-4 py-3"><p class="font-semibold text-sm">{{ $row['student']->name }}</p><p class="text-xs text-[#565d6d]">{{ $row['student']->nis }}</p></td><td class="px-4 py-3 text-sm">{{ $row['student']->status_label }}</td><td class="px-4 py-3 text-sm">{{ $row['student']->classroom?->name ?? '-' }}</td><td class="px-3 py-3 text-center font-semibold text-green-700">{{ $row['hadir'] }}</td><td class="px-3 py-3 text-center text-blue-700">{{ $row['sakit'] }}</td><td class="px-3 py-3 text-center text-amber-700">{{ $row['izin'] }}</td><td class="px-3 py-3 text-center text-red-700">{{ $row['alpa'] }}</td><td class="px-3 py-3 text-center">{{ $row['total'] }}</td><td class="px-4 py-3">@if($row['percentage'] === null)<span class="text-xs font-semibold text-slate-500">Belum ada data</span>@else<div class="flex items-center gap-2"><div class="h-2 w-24 bg-gray-100 rounded-full overflow-hidden"><div class="h-full bg-green-500 rounded-full" style="width:{{ $row['percentage'] }}%"></div></div><span class="text-sm font-bold">{{ $row['percentage'] }}%</span></div>@endif</td></tr>
    @empty <tr><td colspan="9" class="px-6 py-10 text-center text-sm text-[#565d6d]">Tidak ada murid untuk filter ini.</td></tr>@endforelse
  </tbody></table></div>

  <h3 class="text-lg font-bold mb-3">Rekap Harian</h3>
  <div class="overflow-x-auto"><table class="w-full text-left min-w-[620px] border border-[#dee1e6]"><thead class="bg-[#fafafb] text-xs uppercase text-[#565d6d]"><tr><th class="px-4 py-3">Tanggal</th><th class="px-3 py-3 text-center">Hadir</th><th class="px-3 py-3 text-center">Sakit</th><th class="px-3 py-3 text-center">Izin</th><th class="px-3 py-3 text-center">Alpa</th><th class="px-3 py-3 text-center">Total Tercatat</th><th class="px-4 py-3">Hadir/Tercatat</th></tr></thead><tbody class="divide-y divide-[#edf0f3]">
    @forelse($dailySummary as $day)<tr><td class="px-4 py-3 text-sm font-semibold">{{ \Carbon\Carbon::parse($day['date'])->locale('id')->translatedFormat('l, d F Y') }}</td><td class="px-3 py-3 text-center text-green-700 font-semibold">{{ $day['hadir'] }}</td><td class="px-3 py-3 text-center text-blue-700">{{ $day['sakit'] }}</td><td class="px-3 py-3 text-center text-amber-700">{{ $day['izin'] }}</td><td class="px-3 py-3 text-center text-red-700">{{ $day['alpa'] }}</td><td class="px-3 py-3 text-center">{{ $day['total'] }}</td><td class="px-4 py-3 text-sm font-bold">{{ $day['percentage'] }}%</td></tr>
    @empty <tr><td colspan="7" class="px-6 py-10 text-center text-sm text-[#565d6d]">Belum ada absensi tercatat pada bulan ini.</td></tr>@endforelse
  </tbody></table></div>
</div>
@endsection
