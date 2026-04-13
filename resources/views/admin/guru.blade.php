@extends('layouts.admin')

@section('title', 'Manajemen Data Guru - E-Rapor BiMBA')
@section('page-title', 'Manajemen Data Guru')

@section('content')
<!-- Page Title & Actions -->
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-8">
  <div>
    <h1 class="text-2xl lg:text-3xl font-bold tracking-tight">Manajemen Data Guru/Motivator</h1>
    <p class="text-[#565d6d] mt-1 font-roboto">Kelola informasi motivator, beban kelas, dan spesialisasi pengajaran.</p>
  </div>
  <div class="flex flex-wrap items-center gap-3">
    <button onclick="document.getElementById('modal-import-guru').classList.remove('hidden')" class="flex items-center gap-2 px-4 py-2 bg-white border border-[#dee1e6] rounded-xl text-sm font-medium text-[#171a1f] hover:bg-gray-50">
      <iconify-icon icon="lucide:upload" width="16"></iconify-icon>
      Impor
    </button>
    <a href="{{ route('admin.guru.export') }}" class="flex items-center gap-2 px-4 py-2 bg-white border border-[#dee1e6] rounded-xl text-sm font-medium text-[#171a1f] hover:bg-gray-50">
      <iconify-icon icon="lucide:download" width="16"></iconify-icon>
      Ekspor
    </a>
    <button onclick="document.getElementById('modal-tambah-guru').classList.remove('hidden')" class="flex items-center gap-2 px-4 py-2 bg-[#3d8af5] text-white rounded-xl text-sm font-medium hover:bg-blue-600">
      <iconify-icon icon="lucide:plus" width="16"></iconify-icon>
      Tambah Guru
    </button>
  </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
  <div class="bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow flex items-center justify-between">
    <div>
      <p class="text-sm font-medium text-[#565d6d]">Total Guru</p>
      <h3 class="text-3xl font-bold mt-1 text-[#171a1f]">{{ $totalGuru }}</h3>
    </div>
    <div class="w-14 h-14 bg-[#F1F6FE] rounded-2xl flex items-center justify-center">
      <iconify-icon icon="lucide:users" width="24" class="text-[#3d8af5]"></iconify-icon>
    </div>
  </div>
  <div class="bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow flex items-center justify-between">
    <div>
      <p class="text-sm font-medium text-[#565d6d]">Guru Aktif</p>
      <h3 class="text-3xl font-bold mt-1 text-[#171a1f]">{{ $guruAktif }}</h3>
    </div>
    <div class="w-14 h-14 bg-[#DCFAE6] rounded-2xl flex items-center justify-center">
      <iconify-icon icon="lucide:user-check" width="24" class="text-[#16a34a]"></iconify-icon>
    </div>
  </div>
  <div class="bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow flex items-center justify-between">
    <div>
      <p class="text-sm font-medium text-[#565d6d]">Spesialisasi Baca</p>
      <h3 class="text-3xl font-bold mt-1 text-[#171a1f]">{{ $specBaca }}</h3>
    </div>
    <div class="w-14 h-14 bg-[#FCF0E3] rounded-2xl flex items-center justify-center">
      <iconify-icon icon="lucide:book-open" width="24" class="text-orange-500"></iconify-icon>
    </div>
  </div>
  <div class="bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow flex items-center justify-between">
    <div>
      <p class="text-sm font-medium text-[#565d6d]">Beban Rata-rata</p>
      <h3 class="text-3xl font-bold mt-1 text-[#171a1f]">{{ $avgBeban }}</h3>
    </div>
    <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center">
      <iconify-icon icon="lucide:calculator" width="24" class="text-[#565d6d]"></iconify-icon>
    </div>
  </div>
</div>

<!-- Table Section -->
<div class="bg-white rounded-2xl border border-[#dee1e6] overflow-hidden main-shadow mb-8">
  <div class="p-4 border-b border-[#dee1e6] flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex flex-col sm:flex-row items-center gap-3">
      <form method="GET" action="{{ route('admin.guru') }}" class="relative w-full sm:w-80">
        <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-[#565d6d]">
          <iconify-icon icon="lucide:search" width="16"></iconify-icon>
        </div>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan nama atau ID..." class="w-full pl-10 pr-4 py-2 bg-[#fafafb] border border-transparent rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20">
      </form>
      <div class="relative" x-data="{ open: false }">
        <button @click="open = !open" class="flex items-center gap-2 px-4 py-2 border border-[#dee1e6] rounded-xl text-sm font-medium text-[#171a1f] hover:bg-gray-50">
          <iconify-icon icon="lucide:filter" width="16"></iconify-icon>
          Spesialisasi
        </button>
        <div x-show="open" @click.away="open = false" class="absolute top-full mt-1 left-0 bg-white border border-[#dee1e6] rounded-xl shadow-lg z-10 py-1 w-48">
          <a href="{{ route('admin.guru') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Semua</a>
          <a href="{{ route('admin.guru', ['specialization' => 'Baca-Tulis']) }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Baca-Tulis</a>
          <a href="{{ route('admin.guru', ['specialization' => 'Matematika']) }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Matematika</a>
          <a href="{{ route('admin.guru', ['specialization' => 'Bahasa Inggris']) }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Bahasa Inggris</a>
        </div>
      </div>
    </div>
    <p class="text-sm text-[#565d6d] font-roboto">Menampilkan <span class="font-bold">{{ $teachers->count() }}</span> dari <span class="font-bold">{{ $totalGuru }}</span> guru</p>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead class="bg-[#fafafb] border-b border-[#dee1e6]">
        <tr class="text-[#565d6d] text-sm font-semibold">
          <th class="px-6 py-4">NIP</th>
          <th class="px-6 py-4">Nama Guru</th>
          <th class="px-6 py-4">Spesialisasi</th>
          <th class="px-6 py-4">Email</th>
          <th class="px-6 py-4 text-center">Beban Kelas</th>
          <th class="px-6 py-4">Status</th>
          <th class="px-6 py-4 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#dee1e6]">
        @forelse($teachers as $teacher)
        <tr class="hover:bg-gray-50/50 {{ $loop->even ? 'bg-[#fafafb]/30' : '' }}">
          <td class="px-6 py-4 text-sm font-medium text-[#565d6d] font-roboto">{{ $teacher->nip ?? '-' }}</td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-[#3d8af5] flex items-center justify-center text-white font-bold text-sm">{{ strtoupper(substr($teacher->user->name, 0, 1)) }}</div>
              <span class="text-sm font-semibold text-[#171a1f]">{{ $teacher->user->name }}</span>
            </div>
          </td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-2 text-sm text-[#565d6d]">
              <iconify-icon icon="lucide:book-open" width="14" class="text-[#3d8af5]"></iconify-icon>
              {{ $teacher->specialization ?? '-' }}
            </div>
          </td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">{{ $teacher->user->email }}</td>
          <td class="px-6 py-4 text-center">
            <span class="px-2 py-1 bg-[#3d8af5]/10 text-[#3d8af5] text-xs font-bold rounded-md">{{ $teacher->classrooms->count() }} Kelas</span>
          </td>
          <td class="px-6 py-4">
            <span class="status-pill {{ $teacher->status === 'aktif' ? 'status-active' : ($teacher->status === 'cuti' ? 'status-cuti' : 'status-nonaktif') }}">{{ ucfirst($teacher->status) }}</span>
          </td>
          <td class="px-6 py-4 text-right">
            <div class="flex items-center justify-end gap-1">
              <button onclick="openEditGuru({{ $teacher->id }}, '{{ addslashes($teacher->user->name) }}', '{{ $teacher->user->email }}', '{{ $teacher->nip }}', '{{ addslashes($teacher->specialization) }}', '{{ $teacher->status }}', {{ json_encode($teacher->classrooms->pluck('id')) }})" class="p-2 text-[#3d8af5] hover:bg-blue-50 rounded-lg" title="Edit">
                <iconify-icon icon="lucide:pencil" width="14"></iconify-icon>
              </button>
              <form method="POST" action="{{ route('admin.guru.destroy', $teacher) }}" onsubmit="return confirm('Yakin hapus data guru ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="p-2 text-[#D92626] hover:bg-red-50 rounded-lg" title="Hapus">
                  <iconify-icon icon="lucide:trash-2" width="14"></iconify-icon>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="px-6 py-8 text-center text-sm text-[#565d6d]">Belum ada data guru.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div class="p-4 border-t border-[#dee1e6] flex flex-col sm:flex-row items-center justify-between gap-4">
    <p class="text-sm text-[#565d6d] font-roboto">Menampilkan <span class="font-bold">{{ $teachers->firstItem() ?? 0 }}-{{ $teachers->lastItem() ?? 0 }}</span> dari <span class="font-bold">{{ $teachers->total() }}</span> Motivator</p>
    {{ $teachers->links() }}
  </div>
</div>

<!-- Bottom Panels -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
  <div class="xl:col-span-2 bg-[#0D7FF2] p-6 lg:p-8 rounded-2xl main-shadow flex flex-col sm:flex-row gap-6">
    <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex-shrink-0 flex items-center justify-center">
      <iconify-icon icon="lucide:lightbulb" width="22" class="text-[#3d8af5]"></iconify-icon>
    </div>
    <div>
      <h4 class="text-lg font-bold text-white mb-2">Tips Manajemen Motivator</h4>
      <p class="text-white/80 text-sm leading-relaxed">
        Pastikan beban kelas setiap guru tidak melebihi 5 kelas per minggu untuk menjaga kualitas pengajaran BiMBA AIUEO yang optimal bagi anak-anak. Gunakan filter spesialisasi untuk mencari guru pengganti yang tepat.
      </p>
    </div>
  </div>
  <div class="bg-white p-6 rounded-2xl border border-[#dee1e6] main-shadow flex flex-col items-center text-center">
    <div class="w-12 h-12 bg-[#63e98f]/10 rounded-full flex items-center justify-center mb-4">
      <iconify-icon icon="lucide:help-circle" width="22" class="text-[#16a34a]"></iconify-icon>
    </div>
    <h4 class="text-base font-bold text-[#171a1f] mb-1">Butuh Bantuan?</h4>
    <p class="text-xs text-[#565d6d] mb-6">Hubungi tim IT Pusat untuk bantuan teknis.</p>
    <a href="mailto:support@bimba-aiueo.com" class="w-full py-2 border border-[#63e98f] text-[#16a34a] text-sm font-medium rounded-xl hover:bg-[#63e98f]/5 block text-center">
      Pusat Bantuan
    </a>
  </div>
</div>

<!-- Success Message -->
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

<!-- Modal Tambah Guru -->
<div id="modal-tambah-guru" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
  <div class="bg-white rounded-2xl p-8 w-full max-w-lg mx-4 shadow-xl max-h-[90vh] overflow-y-auto">
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-xl font-bold text-[#171a1f]">Tambah Guru Baru</h3>
      <button onclick="document.getElementById('modal-tambah-guru').classList.add('hidden')" class="text-[#565d6d] hover:text-[#171a1f]">
        <iconify-icon icon="lucide:x" width="20"></iconify-icon>
      </button>
    </div>
    <form method="POST" action="{{ route('admin.guru.store') }}" class="space-y-4">
      @csrf
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Nama Lengkap</label>
          <input type="text" name="name" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#3d8af5]/20 focus:outline-none" placeholder="Nama Guru">
        </div>
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">NIP</label>
          <input type="text" name="nip" class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#3d8af5]/20 focus:outline-none" placeholder="T-006">
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-[#565d6d] mb-1">Email</label>
        <input type="email" name="email" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#3d8af5]/20 focus:outline-none" placeholder="guru@bimba.id">
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Spesialisasi</label>
          <input type="text" name="specialization" class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#3d8af5]/20 focus:outline-none" placeholder="Baca-Tulis">
        </div>
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Status</label>
          <select name="status" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#3d8af5]/20 focus:outline-none">
            <option value="aktif">Aktif</option>
            <option value="cuti">Cuti</option>
            <option value="nonaktif">Nonaktif</option>
          </select>
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-[#565d6d] mb-1">Kelas (opsional)</label>
        <div class="grid grid-cols-2 gap-2 max-h-32 overflow-y-auto border border-[#dee1e6] rounded-xl p-3">
          @foreach($classrooms as $classroom)
          <label class="flex items-center gap-2 text-sm text-[#565d6d]">
            <input type="checkbox" name="classroom_ids[]" value="{{ $classroom->id }}" class="rounded border-gray-300 text-[#3d8af5] focus:ring-[#3d8af5]">
            {{ $classroom->name }}
          </label>
          @endforeach
        </div>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="document.getElementById('modal-tambah-guru').classList.add('hidden')" class="flex-1 py-2.5 border border-[#dee1e6] rounded-xl text-sm font-medium text-[#565d6d] hover:bg-gray-50">Batal</button>
        <button type="submit" class="flex-1 py-2.5 bg-[#3d8af5] text-white rounded-xl text-sm font-medium hover:bg-blue-600">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Guru -->
<div id="modal-edit-guru" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
  <div class="bg-white rounded-2xl p-8 w-full max-w-lg mx-4 shadow-xl max-h-[90vh] overflow-y-auto">
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-xl font-bold text-[#171a1f]">Edit Data Guru</h3>
      <button onclick="document.getElementById('modal-edit-guru').classList.add('hidden')" class="text-[#565d6d] hover:text-[#171a1f]">
        <iconify-icon icon="lucide:x" width="20"></iconify-icon>
      </button>
    </div>
    <form id="form-edit-guru" method="POST" class="space-y-4">
      @csrf @method('PUT')
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Nama Lengkap</label>
          <input type="text" name="name" id="edit-name" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#3d8af5]/20 focus:outline-none">
        </div>
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">NIP</label>
          <input type="text" name="nip" id="edit-nip" class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#3d8af5]/20 focus:outline-none">
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-[#565d6d] mb-1">Email</label>
        <input type="email" name="email" id="edit-email" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#3d8af5]/20 focus:outline-none">
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Spesialisasi</label>
          <input type="text" name="specialization" id="edit-specialization" class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#3d8af5]/20 focus:outline-none">
        </div>
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Status</label>
          <select name="status" id="edit-status" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#3d8af5]/20 focus:outline-none">
            <option value="aktif">Aktif</option>
            <option value="cuti">Cuti</option>
            <option value="nonaktif">Nonaktif</option>
          </select>
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-[#565d6d] mb-1">Kelas</label>
        <div id="edit-classrooms" class="grid grid-cols-2 gap-2 max-h-32 overflow-y-auto border border-[#dee1e6] rounded-xl p-3">
          @foreach($classrooms as $classroom)
          <label class="flex items-center gap-2 text-sm text-[#565d6d]">
            <input type="checkbox" name="classroom_ids[]" value="{{ $classroom->id }}" class="edit-classroom-cb rounded border-gray-300 text-[#3d8af5] focus:ring-[#3d8af5]">
            {{ $classroom->name }}
          </label>
          @endforeach
        </div>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="document.getElementById('modal-edit-guru').classList.add('hidden')" class="flex-1 py-2.5 border border-[#dee1e6] rounded-xl text-sm font-medium text-[#565d6d] hover:bg-gray-50">Batal</button>
        <button type="submit" class="flex-1 py-2.5 bg-[#3d8af5] text-white rounded-xl text-sm font-medium hover:bg-blue-600">Perbarui</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Import Guru -->
<div id="modal-import-guru" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
  <div class="bg-white rounded-2xl p-8 w-full max-w-md mx-4 shadow-xl">
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-xl font-bold text-[#171a1f]">Impor Data Guru</h3>
      <button onclick="document.getElementById('modal-import-guru').classList.add('hidden')" class="text-[#565d6d] hover:text-[#171a1f]">
        <iconify-icon icon="lucide:x" width="20"></iconify-icon>
      </button>
    </div>
    <form method="POST" action="{{ route('admin.guru.import') }}" enctype="multipart/form-data" class="space-y-4">
      @csrf
      <div>
        <label class="block text-sm font-medium text-[#565d6d] mb-2">File CSV</label>
        <input type="file" name="file" accept=".csv,.txt" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#3d8af5]/20 focus:outline-none">
        <p class="text-xs text-[#565d6d] mt-2">Format: nama, email, nip, spesialisasi, status</p>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="document.getElementById('modal-import-guru').classList.add('hidden')" class="flex-1 py-2.5 border border-[#dee1e6] rounded-xl text-sm font-medium text-[#565d6d] hover:bg-gray-50">Batal</button>
        <button type="submit" class="flex-1 py-2.5 bg-[#3d8af5] text-white rounded-xl text-sm font-medium hover:bg-blue-600">Impor</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEditGuru(id, name, email, nip, specialization, status, classroomIds) {
  document.getElementById('form-edit-guru').action = '/admin/guru/' + id;
  document.getElementById('edit-name').value = name;
  document.getElementById('edit-email').value = email;
  document.getElementById('edit-nip').value = nip;
  document.getElementById('edit-specialization').value = specialization;
  document.getElementById('edit-status').value = status;
  document.querySelectorAll('.edit-classroom-cb').forEach(cb => {
    cb.checked = classroomIds.includes(parseInt(cb.value));
  });
  document.getElementById('modal-edit-guru').classList.remove('hidden');
}
</script>
@endsection
