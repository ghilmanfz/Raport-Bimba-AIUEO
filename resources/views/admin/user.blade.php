@extends('layouts.admin')

@section('title', 'Manajemen User - E-Rapor BiMBA')
@section('page-title', 'Manajemen User')

@section('content')
<!-- Page Title & Actions -->
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-8">
  <div>
    <h1 class="text-2xl lg:text-3xl font-bold tracking-tight">Manajemen User</h1>
    <p class="text-[#565d6d] mt-1 font-roboto">Kelola akun guru dan wali murid beserta password. Tambah/hapus dilakukan di menu Data Guru & Data Murid.</p>
  </div>

</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
  <div class="bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow flex items-center justify-between">
    <div>
      <p class="text-sm font-medium text-[#565d6d]">Total User</p>
      <h3 class="text-3xl font-bold mt-1 text-[#171a1f]">{{ $totalUser }}</h3>
    </div>
    <div class="w-14 h-14 bg-[#F1F6FE] rounded-2xl flex items-center justify-center">
      <iconify-icon icon="lucide:users" width="24" class="text-[#3d8af5]"></iconify-icon>
    </div>
  </div>
  <div class="bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow flex items-center justify-between">
    <div>
      <p class="text-sm font-medium text-[#565d6d]">Akun Guru</p>
      <h3 class="text-3xl font-bold mt-1 text-[#171a1f]">{{ $totalGuru }}</h3>
    </div>
    <div class="w-14 h-14 bg-[#DBEAFE] rounded-2xl flex items-center justify-center">
      <iconify-icon icon="lucide:graduation-cap" width="24" class="text-[#3d8af5]"></iconify-icon>
    </div>
  </div>
  <div class="bg-white p-6 rounded-xl border border-[#dee1e6] main-shadow flex items-center justify-between">
    <div>
      <p class="text-sm font-medium text-[#565d6d]">Akun Wali Murid</p>
      <h3 class="text-3xl font-bold mt-1 text-[#171a1f]">{{ $totalWali }}</h3>
    </div>
    <div class="w-14 h-14 bg-[#FEF9C3] rounded-2xl flex items-center justify-center">
      <iconify-icon icon="lucide:user" width="24" class="text-[#EAB308]"></iconify-icon>
    </div>
  </div>
</div>

<!-- Table Section -->
<div class="bg-white rounded-2xl border border-[#dee1e6] overflow-hidden main-shadow mb-8">
  <div class="p-4 border-b border-[#dee1e6] flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex flex-col sm:flex-row items-center gap-3">
      <form method="GET" action="{{ route('admin.user') }}" class="relative w-full sm:w-80">
        <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-[#565d6d]">
          <iconify-icon icon="lucide:search" width="16"></iconify-icon>
        </div>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." class="w-full pl-10 pr-4 py-2 bg-[#fafafb] border border-transparent rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20">
        @if(request('role'))
          <input type="hidden" name="role" value="{{ request('role') }}">
        @endif
      </form>
      <div class="relative" x-data="{ open: false }">
        <button @click="open = !open" class="flex items-center gap-2 px-4 py-2 border border-[#dee1e6] rounded-xl text-sm font-medium text-[#171a1f] hover:bg-gray-50">
          <iconify-icon icon="lucide:filter" width="16"></iconify-icon>
          {{ request('role') ? ucfirst(request('role')) : 'Semua Role' }}
        </button>
        <div x-show="open" @click.away="open = false" class="absolute top-full mt-1 left-0 bg-white border border-[#dee1e6] rounded-xl shadow-lg z-10 py-1 w-40">
          <a href="{{ route('admin.user', ['search' => request('search')]) }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Semua</a>
          <a href="{{ route('admin.user', ['role' => 'guru', 'search' => request('search')]) }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Guru</a>
          <a href="{{ route('admin.user', ['role' => 'wali', 'search' => request('search')]) }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Wali Murid</a>
        </div>
      </div>
    </div>
    <p class="text-sm text-[#565d6d] font-roboto">Menampilkan <span class="font-bold">{{ $users->count() }}</span> dari <span class="font-bold">{{ $users->total() }}</span> user</p>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead class="bg-[#fafafb] border-b border-[#dee1e6]">
        <tr class="text-[#565d6d] text-sm font-semibold">
          <th class="px-6 py-4">No</th>
          <th class="px-6 py-4">Nama</th>
          <th class="px-6 py-4">Email</th>
          <th class="px-6 py-4">Role</th>
          <th class="px-6 py-4">Password</th>
          <th class="px-6 py-4 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#dee1e6]">
        @forelse($users as $idx => $u)
        <tr class="hover:bg-gray-50/50 {{ $loop->even ? 'bg-[#fafafb]/30' : '' }}">
          <td class="px-6 py-4 text-sm text-[#565d6d]">{{ $users->firstItem() + $idx }}</td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-xs {{ $u->role === 'guru' ? 'bg-[#3d8af5]' : 'bg-[#EAB308]' }}">{{ strtoupper(substr($u->name, 0, 1)) }}</div>
              <span class="text-sm font-semibold text-[#171a1f]">{{ $u->name }}</span>
            </div>
          </td>
          <td class="px-6 py-4 text-sm text-[#565d6d] font-roboto">{{ $u->email }}</td>
          <td class="px-6 py-4">
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold {{ $u->role === 'guru' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700' }}">
              <iconify-icon icon="{{ $u->role === 'guru' ? 'lucide:graduation-cap' : 'lucide:users' }}" width="12"></iconify-icon>
              {{ ucfirst($u->role) }}
            </span>
          </td>
          <td class="px-6 py-4" x-data="{ show: false }">
            <div class="flex items-center gap-2">
              <code class="text-sm font-mono bg-[#f3f4f6] px-2 py-1 rounded" x-text="show ? '{{ $u->plain_password ?? '***' }}' : '••••••••'"></code>
              <button @click="show = !show" class="p-1 text-[#565d6d] hover:text-[#3d8af5] rounded" :title="show ? 'Sembunyikan' : 'Lihat Password'">
                <iconify-icon :icon="show ? 'lucide:eye-off' : 'lucide:eye'" width="14"></iconify-icon>
              </button>
            </div>
          </td>
          <td class="px-6 py-4 text-right">
            <div class="flex items-center justify-end gap-1">
              <button onclick="openEditUser({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ $u->email }}', '{{ $u->role }}')" class="p-2 text-[#3d8af5] hover:bg-blue-50 rounded-lg" title="Edit">
                <iconify-icon icon="lucide:pencil" width="14"></iconify-icon>
              </button>
              <form method="POST" action="{{ route('admin.user.resetPassword', $u) }}" class="inline">
                @csrf
                <button type="submit" class="p-2 text-[#EAB308] hover:bg-yellow-50 rounded-lg" title="Reset Password" onclick="return confirm('Reset password {{ addslashes($u->name) }} ke default (password123)?')">
                  <iconify-icon icon="lucide:refresh-cw" width="14"></iconify-icon>
                </button>
              </form>

            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="px-6 py-8 text-center text-sm text-[#565d6d]">Belum ada data user.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div class="p-4 border-t border-[#dee1e6] flex flex-col sm:flex-row items-center justify-between gap-4">
    <p class="text-sm text-[#565d6d] font-roboto">Menampilkan <span class="font-bold">{{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }}</span> dari <span class="font-bold">{{ $users->total() }}</span> User</p>
    {{ $users->withQueryString()->links() }}
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

<!-- Modal Edit User -->
<div id="modal-edit-user" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
  <div class="bg-white rounded-2xl p-8 w-full max-w-lg mx-4 shadow-xl max-h-[90vh] overflow-y-auto">
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-xl font-bold text-[#171a1f]">Edit Data User</h3>
      <button onclick="document.getElementById('modal-edit-user').classList.add('hidden')" class="text-[#565d6d] hover:text-[#171a1f]">
        <iconify-icon icon="lucide:x" width="20"></iconify-icon>
      </button>
    </div>
    <form id="form-edit-user" method="POST" class="space-y-4">
      @csrf @method('PUT')
      <div>
        <label class="block text-sm font-medium text-[#565d6d] mb-1">Nama Lengkap</label>
        <input type="text" name="name" id="edit-user-name" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#3d8af5]/20 focus:outline-none">
      </div>
      <div>
        <label class="block text-sm font-medium text-[#565d6d] mb-1">Email</label>
        <input type="email" name="email" id="edit-user-email" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#3d8af5]/20 focus:outline-none">
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Role</label>
          <select name="role" id="edit-user-role" required class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#3d8af5]/20 focus:outline-none">
            <option value="guru">Guru</option>
            <option value="wali">Wali Murid</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-[#565d6d] mb-1">Password Baru</label>
          <input type="text" name="password" class="w-full px-4 py-2 border border-[#dee1e6] rounded-xl text-sm focus:ring-2 focus:ring-[#3d8af5]/20 focus:outline-none" placeholder="Kosongkan jika tidak ganti">
        </div>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="document.getElementById('modal-edit-user').classList.add('hidden')" class="flex-1 py-2.5 border border-[#dee1e6] rounded-xl text-sm font-medium text-[#565d6d] hover:bg-gray-50">Batal</button>
        <button type="submit" class="flex-1 py-2.5 bg-[#3d8af5] text-white rounded-xl text-sm font-medium hover:bg-blue-600">Perbarui</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEditUser(id, name, email, role) {
  document.getElementById('form-edit-user').action = '/admin/user/' + id;
  document.getElementById('edit-user-name').value = name;
  document.getElementById('edit-user-email').value = email;
  document.getElementById('edit-user-role').value = role;
  document.getElementById('modal-edit-user').classList.remove('hidden');
}
</script>
@endsection
