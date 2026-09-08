@extends('layouts.admin')

@section('title', 'Data Kelas - E-Rapor BiMBA')
@section('page-title', 'Data Kelas')

@section('content')
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
  <div>
    <h1 class="text-2xl lg:text-3xl font-bold tracking-tight">Data Kelompok / Kelas</h1>
    <p class="text-sm text-[#565d6d] mt-1">Kelola kelas yang dipakai pada data murid dan pencatatan absensi.</p>
  </div>
  <a href="{{ route('admin.absensi.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-[#F97316]/30 text-[#C2410C] rounded-xl text-sm font-semibold hover:bg-orange-50">
    <iconify-icon icon="lucide:clipboard-check" width="17"></iconify-icon>
    Buka Absensi
  </a>
</div>

@if(session('success'))
  <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">{{ session('error') }}</div>
@endif
@if($errors->any())
  <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
    <ul class="list-disc pl-5 space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
  </div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
  <form method="POST" action="{{ route('admin.kelas.store') }}" class="bg-white rounded-2xl border border-[#dee1e6] main-shadow overflow-hidden xl:sticky xl:top-24">
    @csrf
    <div class="px-5 py-4 border-b border-[#dee1e6]">
      <h2 class="text-lg font-bold">Tambah Kelas</h2>
      <p class="text-xs text-[#565d6d] mt-0.5">Kelas baru langsung tersedia pada formulir murid dan absensi.</p>
    </div>
    <div class="p-5 space-y-4">
      <div>
        <label class="block text-xs font-semibold text-[#565d6d] mb-1.5">Nama Kelas</label>
        <input type="text" name="name" value="{{ old('name') }}" maxlength="100" required placeholder="Contoh: Kelas Bintang" class="w-full px-3 py-2.5 border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#F97316]/20">
      </div>
      <div>
        <label class="block text-xs font-semibold text-[#565d6d] mb-1.5">Kelompok / Level</label>
        <input type="text" name="level" value="{{ old('level') }}" maxlength="50" required placeholder="Contoh: Level 1" class="w-full px-3 py-2.5 border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#F97316]/20">
      </div>
      <div>
        <label class="block text-xs font-semibold text-[#565d6d] mb-1.5">Kapasitas</label>
        <input type="number" name="capacity" value="{{ old('capacity', 15) }}" min="1" max="100" required class="w-full px-3 py-2.5 border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#F97316]/20">
      </div>
      <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#F97316] text-white rounded-xl text-sm font-semibold hover:bg-[#EA580C]">
        <iconify-icon icon="lucide:plus" width="17"></iconify-icon>
        Tambah Kelas
      </button>
    </div>
  </form>

  <div class="xl:col-span-2 bg-white rounded-2xl border border-[#dee1e6] main-shadow overflow-hidden">
    <div class="px-5 py-4 border-b border-[#dee1e6] flex items-center justify-between">
      <div><h2 class="text-lg font-bold">Daftar Kelas</h2><p class="text-xs text-[#565d6d]">{{ $classrooms->count() }} kelas terdaftar</p></div>
      <div class="w-10 h-10 bg-orange-50 text-[#F97316] rounded-xl flex items-center justify-center"><iconify-icon icon="lucide:school" width="19"></iconify-icon></div>
    </div>

    <div class="divide-y divide-[#edf0f3]">
      @forelse($classrooms as $classroom)
        <div x-data="{ editing: false }" class="p-5">
          <div x-show="!editing" class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
              <div class="w-11 h-11 rounded-xl bg-orange-50 text-[#F97316] flex items-center justify-center flex-shrink-0"><iconify-icon icon="lucide:users-round" width="20"></iconify-icon></div>
              <div class="min-w-0">
                <h3 class="font-bold text-[#171a1f] truncate">{{ $classroom->name }}</h3>
                <p class="text-xs text-[#565d6d] mt-0.5">{{ $classroom->level }} · Kapasitas {{ $classroom->capacity }} anak</p>
              </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 md:justify-end">
              <span class="px-3 py-1.5 rounded-full bg-green-50 text-green-700 text-xs font-semibold">{{ $classroom->active_students_count }} aktif</span>
              <span class="px-3 py-1.5 rounded-full bg-gray-100 text-[#565d6d] text-xs font-semibold">{{ $classroom->students_count }} total</span>
              <span class="px-3 py-1.5 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">{{ $classroom->attendances_count }} absensi</span>
              <button type="button" @click="editing = true" class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-[#dee1e6] text-[#565d6d] hover:bg-gray-50" title="Ubah kelas"><iconify-icon icon="lucide:pencil" width="16"></iconify-icon></button>
              <form method="POST" action="{{ route('admin.kelas.destroy', $classroom) }}" onsubmit="return confirm(@js('Hapus kelas '.$classroom->name.'?'))">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-red-200 text-red-600 hover:bg-red-50 disabled:opacity-40 disabled:cursor-not-allowed" title="{{ ($classroom->students_count || $classroom->attendances_count) ? 'Kelas yang memiliki murid atau riwayat absensi tidak dapat dihapus' : 'Hapus kelas' }}" @disabled($classroom->students_count || $classroom->attendances_count)><iconify-icon icon="lucide:trash-2" width="16"></iconify-icon></button>
              </form>
            </div>
          </div>

          <form x-show="editing" x-cloak method="POST" action="{{ route('admin.kelas.update', $classroom) }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
            @csrf
            @method('PUT')
            <div class="md:col-span-4"><label class="block text-xs font-semibold text-[#565d6d] mb-1">Nama Kelas</label><input type="text" name="name" value="{{ $classroom->name }}" maxlength="100" required class="w-full px-3 py-2 border border-[#dee1e6] rounded-xl text-sm"></div>
            <div class="md:col-span-3"><label class="block text-xs font-semibold text-[#565d6d] mb-1">Level</label><input type="text" name="level" value="{{ $classroom->level }}" maxlength="50" required class="w-full px-3 py-2 border border-[#dee1e6] rounded-xl text-sm"></div>
            <div class="md:col-span-2"><label class="block text-xs font-semibold text-[#565d6d] mb-1">Kapasitas</label><input type="number" name="capacity" value="{{ $classroom->capacity }}" min="1" max="100" required class="w-full px-3 py-2 border border-[#dee1e6] rounded-xl text-sm"></div>
            <div class="md:col-span-3 flex gap-2"><button type="submit" class="flex-1 px-3 py-2 bg-[#F97316] text-white rounded-xl text-sm font-semibold">Simpan</button><button type="button" @click="editing = false" class="px-3 py-2 border border-[#dee1e6] rounded-xl text-sm font-semibold">Batal</button></div>
          </form>
        </div>
      @empty
        <div class="py-14 px-6 text-center"><iconify-icon icon="lucide:school" width="36" class="text-[#bdc1ca] mb-2"></iconify-icon><p class="text-sm text-[#565d6d]">Belum ada kelas. Tambahkan kelas pertama melalui formulir.</p></div>
      @endforelse
    </div>
  </div>
</div>
@endsection
