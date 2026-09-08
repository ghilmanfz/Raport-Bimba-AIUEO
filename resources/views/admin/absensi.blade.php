@extends('layouts.admin')

@section('title', 'Absensi Harian - E-Rapor BiMBA')
@section('page-title', 'Absensi Harian')

@section('content')
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
  <div>
    <h1 class="text-2xl lg:text-3xl font-bold tracking-tight">Absensi Harian Anak</h1>
    <p class="text-sm text-[#565d6d] mt-1">Catat dan koreksi kehadiran seluruh murid berdasarkan tanggal.</p>
  </div>
  <a href="{{ route('admin.absensi.report') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-[#F97316]/30 text-[#C2410C] rounded-xl text-sm font-semibold hover:bg-orange-50">
    <iconify-icon icon="lucide:bar-chart-3" width="18"></iconify-icon>
    Laporan Absensi
  </a>
</div>

@if(session('success'))
  <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">{{ session('success') }}</div>
@endif
@if($errors->any())
  <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
    <ul class="list-disc pl-5 space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
  </div>
@endif

<form method="GET" action="{{ route('admin.absensi.index') }}" class="bg-white rounded-2xl border border-[#dee1e6] main-shadow p-5 mb-6 grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
  <div class="md:col-span-3">
    <label class="block text-xs font-semibold text-[#565d6d] mb-1.5">Tanggal Absensi</label>
    <input type="date" name="attendance_date" value="{{ $date }}" max="{{ now()->toDateString() }}" class="w-full px-3 py-2.5 border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#F97316]/20">
  </div>
  <div class="md:col-span-3">
    <label class="block text-xs font-semibold text-[#565d6d] mb-1.5">Kelompok / Kelas</label>
    <select name="classroom_id" class="w-full px-3 py-2.5 border border-[#dee1e6] rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#F97316]/20">
      <option value="">Semua Kelas</option>
      @foreach($classrooms as $classroom)
        <option value="{{ $classroom->id }}" @selected((string) $selectedClassroom === (string) $classroom->id)>{{ $classroom->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="md:col-span-4">
    <label class="block text-xs font-semibold text-[#565d6d] mb-1.5">Cari Anak</label>
    <input type="text" name="search" value="{{ $search }}" placeholder="Nama atau NIS" class="w-full px-3 py-2.5 border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#F97316]/20">
  </div>
  <div class="md:col-span-2">
    <button class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#F97316] text-white rounded-xl text-sm font-semibold hover:bg-[#EA580C]">
      <iconify-icon icon="lucide:search" width="17"></iconify-icon> Tampilkan
    </button>
  </div>
</form>

@php
  $cards = [
    ['label' => 'Tercatat', 'value' => $dailyStats['total'], 'icon' => 'lucide:clipboard-check', 'color' => '#7C3AED', 'bg' => '#F3E8FF'],
    ['label' => 'Belum Dicatat', 'value' => $dailyStats['unrecorded'], 'icon' => 'lucide:circle-dashed', 'color' => '#64748B', 'bg' => '#F1F5F9'],
    ['label' => 'Hadir', 'value' => $dailyStats['hadir'], 'icon' => 'lucide:check-circle', 'color' => '#16A34A', 'bg' => '#DCFCE7'],
    ['label' => 'Sakit', 'value' => $dailyStats['sakit'], 'icon' => 'lucide:heart-pulse', 'color' => '#2563EB', 'bg' => '#DBEAFE'],
    ['label' => 'Izin', 'value' => $dailyStats['izin'], 'icon' => 'lucide:file-clock', 'color' => '#D97706', 'bg' => '#FEF3C7'],
    ['label' => 'Alpa', 'value' => $dailyStats['alpa'], 'icon' => 'lucide:x-circle', 'color' => '#DC2626', 'bg' => '#FEE2E2'],
  ];
@endphp
<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
  @foreach($cards as $card)
    <div class="bg-white border border-[#dee1e6] rounded-2xl p-4 main-shadow flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:{{ $card['bg'] }};color:{{ $card['color'] }}">
        <iconify-icon icon="{{ $card['icon'] }}" width="19"></iconify-icon>
      </div>
      <div><p class="text-xs text-[#565d6d]">{{ $card['label'] }}</p><p class="text-2xl font-bold text-[#171a1f]">{{ $card['value'] }}</p></div>
    </div>
  @endforeach
</div>

<form method="POST" action="{{ route('admin.absensi.store') }}">
  @csrf
  <input type="hidden" name="attendance_date" value="{{ $date }}">
  <input type="hidden" name="classroom_id" value="{{ $selectedClassroom }}">
  <input type="hidden" name="search" value="{{ $search }}">

  <div class="bg-white rounded-2xl border border-[#dee1e6] main-shadow overflow-hidden">
    <div class="px-5 py-4 border-b border-[#dee1e6] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
      <div>
        <h2 class="text-lg font-bold text-[#171a1f]">Daftar Kehadiran</h2>
        <p class="text-xs text-[#565d6d] mt-0.5">{{ \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y') }} · {{ $students->count() }} murid aktif</p>
      </div>
      @if($students->isNotEmpty())
        <button type="button" onclick="fillEmptyAttendance('hadir')" class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-green-50 text-green-700 border border-green-200 rounded-xl text-xs font-semibold hover:bg-green-100">
          <iconify-icon icon="lucide:check-check" width="15"></iconify-icon> Isi yang Kosong: Hadir
        </button>
      @endif
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left min-w-[900px]">
        <thead class="bg-[#fafafb] border-b border-[#dee1e6] text-xs uppercase tracking-wide text-[#565d6d]">
          <tr><th class="px-5 py-4">Anak</th><th class="px-5 py-4">Kelas</th><th class="px-5 py-4">Guru Pembimbing</th><th class="px-5 py-4 w-48">Kehadiran</th><th class="px-5 py-4">Catatan</th></tr>
        </thead>
        <tbody class="divide-y divide-[#edf0f3]">
          @forelse($students as $i => $student)
            @php $attendance = $existing->get($student->id); @endphp
            <tr class="hover:bg-[#fafafb]">
              <td class="px-5 py-4">
                <input type="hidden" name="attendances[{{ $i }}][student_id]" value="{{ $student->id }}">
                <p class="font-semibold text-sm text-[#171a1f]">{{ $student->name }}</p><p class="text-xs text-[#565d6d]">{{ $student->nis }}</p>
              </td>
              <td class="px-5 py-4 text-sm text-[#565d6d]">{{ $student->classroom?->name ?? '-' }}</td>
              <td class="px-5 py-4 text-sm text-[#565d6d]">{{ $student->teacher?->user?->name ?? '-' }}</td>
              <td class="px-5 py-4">
                <select name="attendances[{{ $i }}][status]" class="attendance-status w-full px-3 py-2 border border-[#dee1e6] rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#F97316]/20">
                  <option value="">{{ $attendance ? 'Hapus catatan absensi' : 'Belum dicatat' }}</option>
                  @foreach(\App\Models\Attendance::STATUS_LABELS as $value => $label)
                    <option value="{{ $value }}" @selected(old("attendances.$i.status", $attendance?->status) === $value)>{{ $label }}</option>
                  @endforeach
                </select>
              </td>
              <td class="px-5 py-4"><input type="text" name="attendances[{{ $i }}][notes]" value="{{ old("attendances.$i.notes", $attendance?->notes) }}" maxlength="500" placeholder="Opsional" class="w-full px-3 py-2 border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#F97316]/20"></td>
            </tr>
          @empty
            <tr><td colspan="5" class="px-6 py-12 text-center text-sm text-[#565d6d]">Tidak ada murid aktif yang sesuai dengan filter.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($students->isNotEmpty())
      <div class="px-5 py-4 border-t border-[#dee1e6] flex justify-end">
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#F97316] text-white rounded-xl text-sm font-semibold hover:bg-[#EA580C] shadow-sm">
          <iconify-icon icon="lucide:save" width="17"></iconify-icon> Simpan Absensi
        </button>
      </div>
    @endif
  </div>
</form>

<script>
function fillEmptyAttendance(status) {
  document.querySelectorAll('.attendance-status').forEach(select => {
    if (!select.value) select.value = status;
  });
}
</script>
@endsection
