@extends('layouts.admin')

@section('title', 'Manajemen Data Murid - E-Rapor BiMBA')
@section('page-title', 'Manajemen Data Murid')

@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
  <div>
    <h1 class="text-2xl lg:text-3xl font-bold tracking-tight">Manajemen Data Murid</h1>
    <p class="text-[#565d6d] mt-1 font-roboto">Kelola data siswa, plotting guru pembimbing, dan relasi wali murid.</p>
  </div>
  <div class="flex flex-wrap items-center gap-3">
    <a href="{{ route('admin.murid.export') }}" class="flex items-center gap-2 px-4 py-2 bg-white border border-[#F97316]/30 rounded-xl text-[#F97316] font-medium text-sm hover:bg-blue-50">
      <iconify-icon icon="lucide:download" width="16"></iconify-icon>
      Export CSV
    </a>
    <button onclick="openMuridModal('modal-tambah-murid')" class="flex items-center gap-2 px-4 py-2 bg-[#F97316] text-white rounded-xl font-medium text-sm hover:bg-orange-600">
      <iconify-icon icon="lucide:plus" width="16"></iconify-icon>
      Tambah Murid
    </button>
  </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
  <div class="bg-white border border-[#dee1e6] rounded-xl p-6 main-shadow">
    <p class="text-sm font-medium text-[#565d6d]">Total Murid</p>
    <h3 class="text-3xl font-bold mt-2 text-[#171a1f]">{{ $totalMurid }}</h3>
  </div>
  <div class="bg-white border border-[#dee1e6] rounded-xl p-6 main-shadow">
    <p class="text-sm font-medium text-[#565d6d]">Murid Aktif</p>
    <h3 class="text-3xl font-bold mt-2 text-[#171a1f]">{{ $muridAktif }}</h3>
  </div>
  <div class="bg-white border border-[#dee1e6] rounded-xl p-6 main-shadow">
    <p class="text-sm font-medium text-[#565d6d]">Murid Lulus</p>
    <h3 class="text-3xl font-bold mt-2 text-[#171a1f]">{{ $muridLulus }}</h3>
  </div>
  <div class="bg-white border border-[#dee1e6] rounded-xl p-6 main-shadow">
    <p class="text-sm font-medium text-[#565d6d]">Murid Keluar</p>
    <h3 class="text-3xl font-bold mt-2 text-[#171a1f]">{{ $muridKeluar }}</h3>
  </div>
</div>

<div class="bg-white rounded-2xl border border-[#dee1e6] overflow-hidden main-shadow">
  <div class="p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-[#dee1e6]">
    <h2 class="text-xl font-semibold tracking-tight">Daftar Murid Terdaftar</h2>
    <form method="GET" action="{{ route('admin.murid') }}" class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/NIS" class="px-4 py-2 bg-white border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#F97316]/20">
      <select name="status" class="px-4 py-2 bg-white border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#F97316]/20">
        <option value="">Semua Status</option>
        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
        <option value="lulus" {{ request('status') === 'lulus' ? 'selected' : '' }}>Lulus</option>
        <option value="keluar" {{ request('status') === 'keluar' ? 'selected' : '' }}>Keluar</option>
        <option value="cuti" {{ request('status') === 'cuti' ? 'selected' : '' }}>Cuti</option>
      </select>
      <select name="teacher_id" class="px-4 py-2 bg-white border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#F97316]/20">
        <option value="">Semua Guru Pembimbing</option>
        @foreach($teachers as $teacher)
          <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->user->name }}</option>
        @endforeach
      </select>
      <button type="submit" class="px-4 py-2 bg-[#F97316] text-white rounded-xl text-sm hover:bg-orange-600">Filter</button>
    </form>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-[#f3f4f6]/30 text-[#565d6d] text-sm font-semibold border-b border-[#dee1e6]">
          <th class="px-6 py-4">NIS</th>
          <th class="px-6 py-4">Nama Murid</th>
          <th class="px-6 py-4">Tahapan</th>
          <th class="px-6 py-4">Guru Pembimbing</th>
          <th class="px-6 py-4">Wali Murid</th>
          <th class="px-6 py-4">Email Wali</th>
          <th class="px-6 py-4">Status</th>
          <th class="px-6 py-4 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#dee1e6]">
        @forelse($students as $student)
        <tr class="hover:bg-gray-50/50">
          <td class="px-6 py-4 text-sm text-[#565d6d]">{{ $student->nis }}</td>
          <td class="px-6 py-4 text-sm font-medium text-[#171a1f]">{{ $student->name }}</td>
          <td class="px-6 py-4 text-sm text-[#565d6d]">{{ $student->classroom?->name ?? '-' }}</td>
          <td class="px-6 py-4 text-sm text-[#565d6d]">{{ $student->teacher?->user->name ?? '-' }}</td>
          <td class="px-6 py-4 text-sm text-[#565d6d]">{{ $student->parent?->father_name && $student->parent?->mother_name ? $student->parent->father_name . ' & ' . $student->parent->mother_name : ($student->parent?->name ?? '-') }}</td>
          <td class="px-6 py-4 text-sm text-[#565d6d]">{{ $student->parent?->email ?? '-' }}</td>
          <td class="px-6 py-4 text-sm">
            <span class="status-pill {{ $student->status === 'aktif' ? 'status-active' : ($student->status === 'lulus' ? 'status-lulus' : ($student->status === 'cuti' ? 'status-cuti' : 'status-keluar')) }}">{{ ucfirst($student->status) }}</span>
          </td>
          <td class="px-6 py-4 text-right">
            <div class="flex items-center justify-end gap-1">
              <a href="{{ route('admin.murid.show', $student) }}" class="p-2 text-[#7c3aed] hover:bg-purple-50 rounded-lg" title="Lihat Detail">
                <iconify-icon icon="lucide:eye" width="14"></iconify-icon>
              </a>
              <button onclick="openEditMurid({{ $student->id }}, '{{ addslashes($student->name) }}', '{{ $student->nis }}', '{{ $student->classroom_id }}', '{{ $student->teacher_id }}', '{{ $student->join_date->format('Y-m-d') }}', '{{ $student->status }}', '{{ $student->parent_id ?? '' }}', '{{ $student->gender ?? '' }}', '{{ $student->birth_date?->format('Y-m-d') ?? '' }}')" class="p-2 text-[#F97316] hover:bg-orange-50 rounded-lg" title="Edit">
                <iconify-icon icon="lucide:pencil" width="14"></iconify-icon>
              </button>
              <form method="POST" action="{{ route('admin.murid.destroy', $student) }}" id="form-del-murid-{{ $student->id }}" class="inline">
                @csrf @method('DELETE')
                <button type="button" onclick="showDeleteModal('form-del-murid-{{ $student->id }}', 'Murid', '{{ addslashes($student->name) }}')" class="p-2 text-[#D92626] hover:bg-red-50 rounded-lg" title="Hapus"><iconify-icon icon="lucide:trash-2" width="14"></iconify-icon></button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="px-6 py-8 text-center text-sm text-[#565d6d]">Belum ada data murid.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="p-6 border-t border-[#dee1e6]">
    {{ $students->links() }}
  </div>
</div>

<div id="modal-tambah-murid" class="{{ $errors->any() ? '' : 'hidden' }} fixed inset-0 z-50 bg-black/50 overflow-y-auto">
  <div class="flex min-h-full items-center justify-center p-4">
  <div class="bg-white rounded-2xl p-6 w-full max-w-2xl shadow-xl">
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-xl font-bold text-[#171a1f]">Tambah Murid Baru</h3>
      <button onclick="closeMuridModal('modal-tambah-murid')" class="text-[#565d6d] hover:text-[#171a1f]">
        <iconify-icon icon="lucide:x" width="20"></iconify-icon>
      </button>
    </div>

    <form method="POST" action="{{ route('admin.murid.store') }}" class="space-y-4">
      @csrf
      @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
          <p class="font-semibold">Data belum lengkap</p>
          <ul class="mt-1 list-disc pl-4 space-y-1">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">NIS</label>
          <input type="text" name="nis" value="{{ old('nis', $nextNis) }}" readonly class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm bg-gray-50 text-[#565d6d] cursor-not-allowed">
          <p class="text-xs text-[#565d6d] mt-1">NIS dibuat otomatis</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Nama Murid</label>
          <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none" placeholder="Nama lengkap murid">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Tahapan</label>
          <select name="classroom_id" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none">
            @foreach($classrooms as $classroom)
              <option value="{{ $classroom->id }}" @selected(old('classroom_id') == $classroom->id)>{{ $classroom->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Tgl. Bergabung</label>
          <input type="date" name="join_date" required value="{{ old('join_date', date('Y-m-d')) }}" class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Jenis Kelamin</label>
          <select name="gender" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none">
            <option value="">Pilih Jenis Kelamin</option>
            <option value="L" @selected(old('gender') === 'L')>Laki-laki</option>
            <option value="P" @selected(old('gender') === 'P')>Perempuan</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Tanggal Lahir</label>
          <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Guru Pembimbing</label>
          <select id="teacher-select" name="teacher_id" class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none" onchange="checkTeacherCapacity(this)">
            <option value="">Tidak Ada</option>
            @foreach($teachers as $teacher)
              <option value="{{ $teacher->id }}" data-student-count="{{ $teacher->students()->count() }}">{{ $teacher->user->name }} ({{ $teacher->students()->count() }}/25)</option>
            @endforeach
          </select>
          <p id="capacity-warning" class="mt-2 text-xs text-red-600 font-medium hidden">⚠️ Guru ini sudah mencapai kapasitas maksimal 25 murid</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Status</label>
          <select name="status" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none">
            <option value="aktif" @selected(old('status', 'aktif') === 'aktif')>Aktif</option>
            <option value="lulus" @selected(old('status') === 'lulus')>Lulus</option>
            <option value="keluar" @selected(old('status') === 'keluar')>Keluar</option>
            <option value="cuti" @selected(old('status') === 'cuti')>Cuti</option>
          </select>
        </div>
      </div>

      <hr class="border-[#dee1e6] my-4">
      <h4 class="text-sm font-semibold text-[#171a1f]">Data Wali Murid</h4>

      <div class="flex gap-3 mb-4">
        <label id="wali-label-pilih" class="flex items-center gap-2 px-3 py-2 border rounded-xl cursor-pointer transition-colors has-[:checked]:bg-orange-50 has-[:checked]:border-orange-400 border-orange-400 bg-orange-50 hover:bg-orange-50">
          <input type="radio" name="wali_option" value="pilih" class="accent-[#F97316] w-4 h-4" checked onchange="toggleWaliOption('pilih')">
          <span class="text-sm font-medium text-[#171a1f]">Pilih wali yang sudah ada</span>
        </label>
        <label id="wali-label-buat" class="flex items-center gap-2 px-3 py-2 border border-[#dee1e6] rounded-xl cursor-pointer transition-colors has-[:checked]:bg-orange-50 has-[:checked]:border-orange-400 hover:bg-orange-50">
          <input type="radio" name="wali_option" value="buat" class="accent-[#F97316] w-4 h-4" onchange="toggleWaliOption('buat')">
          <span class="text-sm font-medium text-[#565d6d]">Buat akun wali baru</span>
        </label>
      </div>

      <div id="wali-pilih-section" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Pilih Wali Murid</label>
          <p class="text-xs text-[#565d6d] mb-2">Cari nama atau email wali</p>
          <select name="parent_id" id="parent_id_select" class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none">
            <option value="">Tanpa wali dulu</option>
            @foreach($guardians as $guardian)
              <option value="{{ $guardian->id }}">{{ $guardian->name }} ({{ $guardian->students_count }} anak) - {{ $guardian->email }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div id="wali-buat-section" class="hidden">
        <div class="bg-orange-50/50 border border-orange-100 rounded-xl p-4 space-y-3">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium text-[#565d6d] mb-1">Nama Ayah</label>
              <input type="text" name="father_name" id="father_name_input" class="w-full px-3 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none bg-white" placeholder="Nama Ayah">
            </div>
            <div>
              <label class="block text-sm font-medium text-[#565d6d] mb-1">Nama Ibu</label>
              <input type="text" name="mother_name" id="mother_name_input" class="w-full px-3 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none bg-white" placeholder="Nama Ibu">
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium text-[#565d6d] mb-1">Kontak Ayah <span class="text-[#9aa0ab] font-normal">(Opsional)</span></label>
              <input type="text" name="father_phone" id="father_phone_input" class="w-full px-3 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none bg-white" placeholder="08xxxxxxxxxx">
            </div>
            <div>
              <label class="block text-sm font-medium text-[#565d6d] mb-1">Kontak Ibu <span class="text-[#9aa0ab] font-normal">(Opsional)</span></label>
              <input type="text" name="mother_phone" id="mother_phone_input" class="w-full px-3 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none bg-white" placeholder="08xxxxxxxxxx">
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-[#565d6d] mb-1">Alamat <span class="text-[#9aa0ab] font-normal">(Opsional)</span></label>
            <textarea name="address" id="address_input" rows="2" class="w-full px-3 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none bg-white resize-none" placeholder="Alamat rumah"></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-[#565d6d] mb-1">Email Wali <span class="text-[#9aa0ab] font-normal">(Opsional)</span></label>
            <input type="email" name="parent_email" id="parent_email_input" class="w-full px-3 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none bg-white" placeholder="wali@email.com">
          </div>

          <div class="flex items-center gap-2 rounded-lg bg-white border border-orange-200 px-3 py-2">
            <iconify-icon icon="lucide:key-round" width="14" class="text-orange-400 shrink-0"></iconify-icon>
            <p class="text-xs text-[#565d6d]">Password default akun wali: <span class="font-semibold text-[#171a1f]">{{ config('app.default_wali_password', 'password123') }}</span></p>
          </div>
        </div>
      </div>

      <div class="flex gap-3 pt-2">
        <button type="button" onclick="closeMuridModal('modal-tambah-murid')" class="flex-1 py-2.5 border border-[#dee1e6] rounded-xl text-sm font-medium text-[#565d6d] hover:bg-gray-50">Batal</button>
        <button type="submit" class="flex-1 py-2.5 bg-[#F97316] text-white rounded-xl text-sm font-medium hover:bg-orange-600">Simpan</button>
      </div>
    </form>
  </div>
  </div>
</div>

<div id="modal-edit-murid" class="hidden fixed inset-0 z-50 bg-black/50 overflow-y-auto">
  <div class="flex min-h-full items-center justify-center p-4">
  <div class="bg-white rounded-2xl p-6 w-full max-w-lg shadow-xl">
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-xl font-bold text-[#171a1f]">Edit Data Murid</h3>
      <button onclick="closeMuridModal('modal-edit-murid')" class="text-[#565d6d] hover:text-[#171a1f]">
        <iconify-icon icon="lucide:x" width="20"></iconify-icon>
      </button>
    </div>
    <form id="form-edit-murid" method="POST" class="space-y-4">
      @csrf @method('PUT')
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">NIS</label>
          <input type="text" id="edit-murid-nis" readonly class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm bg-gray-50 text-[#565d6d] cursor-not-allowed">
          <p class="text-xs text-[#565d6d] mt-1">NIS tidak dapat diubah</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Nama Murid</label>
          <input type="text" name="name" id="edit-murid-name" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Jenis Kelamin</label>
          <select name="gender" id="edit-murid-gender" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none">
            <option value="">Pilih Jenis Kelamin</option>
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Tanggal Lahir</label>
          <input type="date" name="birth_date" id="edit-murid-birth-date" class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none">
        </div>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Tahapan</label>
          <select name="classroom_id" id="edit-murid-classroom" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none">
            @foreach($classrooms as $classroom)
              <option value="{{ $classroom->id }}">{{ $classroom->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Guru Pembimbing</label>
          <select name="teacher_id" id="edit-murid-teacher" class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none" onchange="checkEditTeacherCapacity(this)">
            <option value="">Tidak Ada</option>
            @foreach($teachers as $teacher)
              <option value="{{ $teacher->id }}" data-student-count="{{ $teacher->students()->count() }}">{{ $teacher->user->name }} ({{ $teacher->students()->count() }}/25)</option>
            @endforeach
          </select>
          <p id="edit-capacity-warning" class="mt-2 text-xs text-red-600 font-medium hidden">⚠️ Guru ini sudah mencapai kapasitas maksimal 25 murid</p>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Tgl. Bergabung</label>
          <input type="date" name="join_date" id="edit-murid-joindate" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Status</label>
          <select name="status" id="edit-murid-status" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm">
            <option value="aktif">Aktif</option>
            <option value="lulus">Lulus</option>
            <option value="keluar">Keluar</option>
            <option value="cuti">Cuti</option>
          </select>
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-[#565d6d] mb-1">Wali Murid</label>
        <select name="parent_id" id="edit-murid-parent" class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#F97316]/20 focus:outline-none">
          <option value="">— Tidak Ada —</option>
          @foreach($guardians as $guardian)
            <option value="{{ $guardian->id }}">
              {{ $guardian->father_name ? $guardian->father_name : '' }}{{ ($guardian->father_name && $guardian->mother_name) ? ' & ' : '' }}{{ $guardian->mother_name ? $guardian->mother_name : '' }}
              {{ (!$guardian->father_name && !$guardian->mother_name) ? $guardian->name : '' }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="closeMuridModal('modal-edit-murid')" class="flex-1 py-2.5 border border-[#dee1e6] rounded-xl text-sm font-medium text-[#565d6d] hover:bg-gray-50">Batal</button>
        <button type="submit" class="flex-1 py-2.5 bg-[#F97316] text-white rounded-xl text-sm font-medium hover:bg-orange-600">Perbarui</button>
      </div>
    </form>
  </div>
  </div>
</div>

@if(session('success'))
<div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg text-sm font-medium z-50" x-data x-init="setTimeout(() => $el.remove(), 3000)">
  {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-xl shadow-lg text-sm font-medium z-50" x-data x-init="setTimeout(() => $el.remove(), 5000)">
  @foreach($errors->all() as $error)
    <p>{{ $error }}</p>
  @endforeach
</div>
@endif

<script>
const updateMuridUrlTemplate = @json(route('admin.murid.update', ['student' => '__MURID_ID__']));

function openMuridModal(id) {
  const modal = document.getElementById(id);
  modal.classList.remove('hidden');
  if (id === 'modal-tambah-murid') {
    const pilihRadio = document.querySelector('input[name="wali_option"][value="pilih"]');
    if (pilihRadio) { pilihRadio.checked = true; }
    toggleWaliOption('pilih');
  }
}

function closeMuridModal(id) {
  const modal = document.getElementById(id);
  modal.classList.add('hidden');
}

function toggleWaliOption(option) {
  const pilihSection = document.getElementById('wali-pilih-section');
  const buatSection  = document.getElementById('wali-buat-section');
  const labelPilih   = document.getElementById('wali-label-pilih');
  const labelBuat    = document.getElementById('wali-label-buat');

  const activeClasses   = ['bg-orange-50', 'border-orange-400'];
  const inactiveClasses = ['bg-white', 'border-[#dee1e6]'];

  if (option === 'pilih') {
    pilihSection.classList.remove('hidden');
    buatSection.classList.add('hidden');
    labelPilih.classList.add(...activeClasses);
    labelPilih.classList.remove(...inactiveClasses);
    labelBuat.classList.remove(...activeClasses);
    labelBuat.classList.add(...inactiveClasses);
    // Clear buat fields
    document.getElementById('father_name_input').value = '';
    document.getElementById('mother_name_input').value = '';
    document.getElementById('father_phone_input').value = '';
    document.getElementById('mother_phone_input').value = '';
    document.getElementById('address_input').value = '';
    document.getElementById('parent_email_input').value = '';
  } else {
    pilihSection.classList.add('hidden');
    buatSection.classList.remove('hidden');
    labelBuat.classList.add(...activeClasses);
    labelBuat.classList.remove(...inactiveClasses);
    labelPilih.classList.remove(...activeClasses);
    labelPilih.classList.add(...inactiveClasses);
    // Clear select
    document.getElementById('parent_id_select').value = '';
  }
}

function openEditMurid(id, name, nis, classroomId, teacherId, joinDate, status, parentId, gender, birthDate) {
  document.getElementById('form-edit-murid').action = updateMuridUrlTemplate.replace('__MURID_ID__', id);
  document.getElementById('edit-murid-name').value = name;
  document.getElementById('edit-murid-nis').value = nis;
  document.getElementById('edit-murid-gender').value = gender || '';
  document.getElementById('edit-murid-birth-date').value = birthDate || '';
  document.getElementById('edit-murid-classroom').value = classroomId;
  document.getElementById('edit-murid-teacher').value = teacherId || '';
  document.getElementById('edit-murid-joindate').value = joinDate;
  document.getElementById('edit-murid-status').value = status;
  document.getElementById('edit-murid-parent').value = parentId || '';
  openMuridModal('modal-edit-murid');
}

function checkTeacherCapacity(select) {
  const selectedOption = select.options[select.selectedIndex];
  const studentCount = selectedOption.getAttribute('data-student-count');
  const warningDiv = document.getElementById('capacity-warning');
  const submitBtn = document.querySelector('#modal-tambah-murid button[type="submit"]');
  
  if (studentCount && parseInt(studentCount) >= 25) {
    warningDiv.classList.remove('hidden');
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
  } else {
    warningDiv.classList.add('hidden');
    submitBtn.disabled = false;
    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
  }
}

function checkEditTeacherCapacity(select) {
  const selectedOption = select.options[select.selectedIndex];
  const studentCount = selectedOption.getAttribute('data-student-count');
  const warningDiv = document.getElementById('edit-capacity-warning');
  const submitBtn = document.querySelector('#modal-edit-murid button[type="submit"]');
  
  if (studentCount && parseInt(studentCount) >= 25) {
    warningDiv.classList.remove('hidden');
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
  } else {
    warningDiv.classList.add('hidden');
    submitBtn.disabled = false;
    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
  }
}

let _deleteFormId = null;
function showDeleteModal(formId, type, name) {
  _deleteFormId = formId;
  document.getElementById('del-modal-type').textContent = type;
  document.getElementById('del-modal-name').textContent = name;
  const m = document.getElementById('modal-delete-list');
  m.classList.remove('hidden'); m.classList.add('flex');
}
function closeDeleteModal() {
  document.getElementById('modal-delete-list').classList.add('hidden');
  document.getElementById('modal-delete-list').classList.remove('flex');
  _deleteFormId = null;
}
function doDelete() {
  if (_deleteFormId) document.getElementById(_deleteFormId).submit();
}
</script>

<!-- Delete Confirmation Modal -->
<div id="modal-delete-list" class="hidden fixed inset-0 z-50 bg-black/50 items-center justify-center">
  <div class="bg-white rounded-2xl p-8 w-full max-w-md mx-4 shadow-xl">
    <div class="flex items-center gap-3 mb-4">
      <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
        <iconify-icon icon="lucide:alert-triangle" width="24" class="text-red-600"></iconify-icon>
      </div>
      <div>
        <h3 class="text-lg font-bold text-[#171a1f]">Konfirmasi Hapus</h3>
        <p class="text-sm text-[#565d6d]">Tindakan ini tidak dapat dibatalkan</p>
      </div>
    </div>
    <p class="text-sm text-[#565d6d] mb-6">Anda yakin ingin menghapus <span class="font-semibold text-[#171a1f]" id="del-modal-type"></span> <span class="font-semibold text-[#171a1f]" id="del-modal-name"></span>? Data tidak dapat dipulihkan.</p>
    <div class="flex gap-3">
      <button type="button" onclick="closeDeleteModal()" class="flex-1 py-2.5 border border-[#dee1e6] rounded-xl text-sm font-medium text-[#565d6d] hover:bg-gray-50">Batal</button>
      <button type="button" onclick="doDelete()" class="flex-1 py-2.5 bg-red-600 text-white rounded-xl text-sm font-medium hover:bg-red-700">OK, Hapus</button>
    </div>
  </div>
</div>

@endsection
