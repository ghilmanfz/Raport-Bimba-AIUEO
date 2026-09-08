@extends('layouts.wali')

@section('title', 'Kehadiran Anak - E-Rapor BiMBA')
@section('page-title', 'Kehadiran Anak')

@push('head')
<style>
@media print {
  .attendance-filter, .attendance-actions { display:none !important; }
  .attendance-card { box-shadow:none !important; border:0 !important; }
}
</style>
@endpush

@section('content')
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
  <div><h1 class="text-2xl lg:text-3xl font-bold tracking-tight">Kehadiran Anak</h1><p class="text-sm text-[#565d6d] mt-1">Pantau riwayat serta rasio hadir dari hari yang sudah dicatat.</p></div>
  @if($student)<button onclick="window.print()" class="attendance-actions inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#F97316] text-white rounded-xl text-sm font-semibold hover:bg-[#EA580C]"><iconify-icon icon="lucide:printer" width="17"></iconify-icon>Cetak Riwayat</button>@endif
</div>

<form method="GET" action="{{ route('wali.absensi.index') }}" class="attendance-filter bg-white rounded-2xl border border-[#dee1e6] custom-shadow p-5 mb-6 grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
  <div class="md:col-span-5"><label class="block text-xs font-semibold text-[#565d6d] mb-1.5">Pilih Anak</label><select name="student_id" class="w-full px-3 py-2.5 border border-[#dee1e6] rounded-xl text-sm bg-white"><option value="">Pilih anak</option>@foreach($children as $child)<option value="{{ $child->id }}" @selected($student?->id === $child->id)>{{ $child->name }} · {{ $child->status_label }} · {{ $child->classroom?->name ?? '-' }}</option>@endforeach</select></div>
  <div class="md:col-span-4"><label class="block text-xs font-semibold text-[#565d6d] mb-1.5">Bulan</label><input type="month" name="month" value="{{ $month }}" class="w-full px-3 py-2.5 border border-[#dee1e6] rounded-xl text-sm"></div>
  <div class="md:col-span-3"><button class="w-full px-4 py-2.5 bg-[#F97316] text-white rounded-xl text-sm font-semibold hover:bg-[#EA580C]">Tampilkan</button></div>
</form>

@if($student)
<div class="attendance-card bg-white rounded-2xl border border-[#dee1e6] custom-shadow overflow-hidden">
  <div class="hero-gradient px-6 py-6 border-b border-[#dee1e6] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div><p class="text-xs font-bold uppercase tracking-wider text-[#F97316]">Riwayat {{ $monthLabel }}</p><h2 class="text-2xl font-bold mt-1">{{ $student->name }}</h2><p class="text-sm text-[#565d6d]">{{ $student->nis }} · {{ $student->status_label }} · {{ $student->classroom?->name ?? '-' }}</p></div>
    <div class="bg-white rounded-2xl border border-green-200 px-5 py-3 text-center min-w-36"><p class="text-xs text-[#565d6d]">Hadir dari Tercatat</p><p class="text-3xl font-black text-green-600">{{ $summary['percentage'] === null ? '—' : $summary['percentage'].'%' }}</p></div>
  </div>

  @php $cards=[['Hadir',$summary['hadir'],'lucide:check-circle','#16A34A','#DCFCE7'],['Sakit',$summary['sakit'],'lucide:heart-pulse','#2563EB','#DBEAFE'],['Izin',$summary['izin'],'lucide:file-clock','#D97706','#FEF3C7'],['Alpa',$summary['alpa'],'lucide:x-circle','#DC2626','#FEE2E2']]; @endphp
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 p-6 border-b border-[#dee1e6]">@foreach($cards as $card)<div class="rounded-2xl p-4 flex items-center gap-3" style="background:{{ $card[4] }}"><div class="w-9 h-9 rounded-full bg-white flex items-center justify-center" style="color:{{ $card[3] }}"><iconify-icon icon="{{ $card[2] }}" width="17"></iconify-icon></div><div><p class="text-xs text-[#565d6d]">{{ $card[0] }}</p><p class="text-2xl font-bold" style="color:{{ $card[3] }}">{{ $card[1] }}</p></div></div>@endforeach</div>

  <div class="p-6">
    <div class="mb-4 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-xs text-blue-800">Rasio hadir hanya dihitung dari {{ $summary['total'] }} hari yang sudah dicatat. Hari tanpa catatan tidak otomatis dianggap Alpa.</div>
    <div class="flex items-center justify-between mb-4"><h3 class="text-lg font-bold">Riwayat Kehadiran</h3><span class="text-xs text-[#565d6d]">{{ $summary['total'] }} hari tercatat</span></div>
    <div class="overflow-x-auto"><table class="w-full text-left min-w-[650px]"><thead class="bg-[#fafafb] border-y border-[#dee1e6] text-xs uppercase text-[#565d6d]"><tr><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Kelas</th><th class="px-4 py-3">Catatan</th></tr></thead><tbody class="divide-y divide-[#edf0f3]">
      @forelse($attendances as $attendance)
        @php $styles=match($attendance->status){'hadir'=>'bg-green-100 text-green-700','sakit'=>'bg-blue-100 text-blue-700','izin'=>'bg-amber-100 text-amber-700',default=>'bg-red-100 text-red-700'}; @endphp
        <tr><td class="px-4 py-4"><p class="text-sm font-semibold">{{ $attendance->attendance_date->locale('id')->translatedFormat('l, d F Y') }}</p></td><td class="px-4 py-4"><span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $styles }}">{{ $attendance->status_code }} · {{ $attendance->status_label }}</span></td><td class="px-4 py-4 text-sm text-[#565d6d]">{{ $attendance->classroom?->name ?? $student->classroom?->name ?? '-' }}</td><td class="px-4 py-4 text-sm text-[#565d6d]">{{ $attendance->notes ?: '-' }}</td></tr>
      @empty <tr><td colspan="4" class="px-6 py-12 text-center"><iconify-icon icon="lucide:calendar-x" width="32" class="text-[#bdc1ca] mb-2"></iconify-icon><p class="text-sm text-[#565d6d]">Belum ada absensi tercatat pada {{ $monthLabel }}.</p></td></tr>@endforelse
    </tbody></table></div>
  </div>
</div>
@else
<div class="bg-white rounded-2xl border border-[#dee1e6] custom-shadow py-16 text-center"><iconify-icon icon="lucide:users" width="42" class="text-[#bdc1ca] mb-3"></iconify-icon><h2 class="text-lg font-bold">Belum Ada Anak Terhubung</h2><p class="text-sm text-[#565d6d] mt-1">Hubungi admin untuk menghubungkan akun dengan data anak.</p></div>
@endif
@endsection
