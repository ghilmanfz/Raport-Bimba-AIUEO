@extends('layouts.guru')

@section('title', 'Absensi Anak - E-Rapor BiMBA')
@section('page-title', 'Absensi Anak')

@section('content')
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
  <div><h1 class="text-2xl lg:text-3xl font-bold tracking-tight">Absensi Anak</h1><p class="text-sm text-[#565d6d] mt-1">Catat kehadiran harian murid bimbingan Anda.</p></div>
  <div class="px-4 py-2.5 rounded-xl bg-orange-50 border border-orange-200 text-sm font-semibold text-[#C2410C]">
    {{ \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y') }}
  </div>
</div>

@if(session('success'))<div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">{{ session('success') }}</div>@endif
@if($errors->any())<div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"><ul class="list-disc pl-5 space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

<form method="GET" action="{{ route('guru.absensi.index') }}" class="bg-white rounded-2xl border border-[#dee1e6] main-shadow p-5 mb-6 grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
  <div class="md:col-span-3"><label class="block text-xs font-semibold text-[#565d6d] mb-1.5">Tanggal</label><input type="date" name="attendance_date" value="{{ $date }}" max="{{ now()->toDateString() }}" class="w-full px-3 py-2.5 border border-[#dee1e6] rounded-xl text-sm"></div>
  <div class="md:col-span-3"><label class="block text-xs font-semibold text-[#565d6d] mb-1.5">Kelas</label><select name="classroom_id" class="w-full px-3 py-2.5 border border-[#dee1e6] rounded-xl text-sm bg-white"><option value="">Semua Kelas</option>@foreach($classrooms as $classroom)<option value="{{ $classroom->id }}" @selected((string)$selectedClassroom === (string)$classroom->id)>{{ $classroom->name }}</option>@endforeach</select></div>
  <div class="md:col-span-4"><label class="block text-xs font-semibold text-[#565d6d] mb-1.5">Cari Anak</label><input type="text" name="search" value="{{ $search }}" placeholder="Nama atau NIS" class="w-full px-3 py-2.5 border border-[#dee1e6] rounded-xl text-sm"></div>
  <div class="md:col-span-2"><button class="w-full px-4 py-2.5 bg-[#F97316] text-white rounded-xl text-sm font-semibold hover:bg-[#EA580C]">Tampilkan</button></div>
</form>

@php $cards = [['Tercatat',$dailyStats['total'],'#7C3AED','#F3E8FF'],['Belum Dicatat',$dailyStats['unrecorded'],'#64748B','#F1F5F9'],['Hadir',$dailyStats['hadir'],'#16A34A','#DCFCE7'],['Sakit',$dailyStats['sakit'],'#2563EB','#DBEAFE'],['Izin',$dailyStats['izin'],'#D97706','#FEF3C7'],['Alpa',$dailyStats['alpa'],'#DC2626','#FEE2E2']]; @endphp
<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">@foreach($cards as $card)<div class="bg-white border border-[#dee1e6] rounded-2xl p-4 main-shadow"><p class="text-xs text-[#565d6d]">{{ $card[0] }}</p><p class="text-3xl font-bold mt-1" style="color:{{ $card[2] }}">{{ $card[1] }}</p></div>@endforeach</div>

<form method="POST" action="{{ route('guru.absensi.store') }}">
  @csrf
  <input type="hidden" name="attendance_date" value="{{ $date }}"><input type="hidden" name="classroom_id" value="{{ $selectedClassroom }}"><input type="hidden" name="search" value="{{ $search }}">
  <div class="bg-white rounded-2xl border border-[#dee1e6] main-shadow overflow-hidden">
    <div class="px-5 py-4 border-b border-[#dee1e6] flex items-center justify-between gap-3"><div><h2 class="text-lg font-bold">Murid Bimbingan</h2><p class="text-xs text-[#565d6d]">{{ $students->count() }} murid aktif</p></div>@if($students->isNotEmpty())<button type="button" onclick="document.querySelectorAll('.attendance-status').forEach(el => { if (!el.value) el.value='hadir' })" class="px-3 py-2 bg-green-50 border border-green-200 text-green-700 rounded-xl text-xs font-semibold">Isi yang Kosong: Hadir</button>@endif</div>
    <div class="overflow-x-auto"><table class="w-full text-left min-w-[760px]"><thead class="bg-[#fafafb] border-b border-[#dee1e6] text-xs uppercase text-[#565d6d]"><tr><th class="px-5 py-4">Anak</th><th class="px-5 py-4">Kelas</th><th class="px-5 py-4 w-52">Kehadiran</th><th class="px-5 py-4">Catatan</th></tr></thead><tbody class="divide-y divide-[#edf0f3]">
      @forelse($students as $i => $student) @php $attendance=$existing->get($student->id); @endphp
      <tr class="hover:bg-[#fafafb]"><td class="px-5 py-4"><input type="hidden" name="attendances[{{ $i }}][student_id]" value="{{ $student->id }}"><p class="font-semibold text-sm">{{ $student->name }}</p><p class="text-xs text-[#565d6d]">{{ $student->nis }}</p><a href="{{ route('guru.rapor', ['student_id' => $student->id]).'#hasil-absensi' }}" class="inline-block mt-2 text-xs font-semibold text-[#C2410C] hover:underline">Lihat Absensi di Rapor</a></td><td class="px-5 py-4 text-sm text-[#565d6d]">{{ $student->classroom?->name ?? '-' }}</td><td class="px-5 py-4"><select name="attendances[{{ $i }}][status]" class="attendance-status w-full px-3 py-2 border border-[#dee1e6] rounded-xl text-sm bg-white"><option value="">{{ $attendance ? 'Hapus catatan absensi' : 'Belum dicatat' }}</option>@foreach(\App\Models\Attendance::STATUS_LABELS as $value=>$label)<option value="{{ $value }}" @selected(old("attendances.$i.status",$attendance?->status)===$value)>{{ $label }}</option>@endforeach</select></td><td class="px-5 py-4"><input type="text" name="attendances[{{ $i }}][notes]" value="{{ old("attendances.$i.notes",$attendance?->notes) }}" maxlength="500" placeholder="Opsional" class="w-full px-3 py-2 border border-[#dee1e6] rounded-xl text-sm"></td></tr>
      @empty <tr><td colspan="4" class="px-6 py-12 text-center text-sm text-[#565d6d]">Tidak ada murid bimbingan yang sesuai.</td></tr> @endforelse
    </tbody></table></div>
    @if($students->isNotEmpty())<div class="px-5 py-4 border-t border-[#dee1e6] flex justify-end"><button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#F97316] text-white rounded-xl text-sm font-semibold hover:bg-[#EA580C]"><iconify-icon icon="lucide:save" width="17"></iconify-icon>Simpan Absensi</button></div>@endif
  </div>
</form>
@endsection
