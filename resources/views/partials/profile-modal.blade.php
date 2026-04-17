{{-- Profile Settings Modal (Alpine.js) --}}
<div x-data="{ showProfileModal: false, activeTab: 'info', saving: false }"
     @open-profile-modal.window="showProfileModal = true; activeTab = 'info'"
     x-show="showProfileModal" x-cloak
     class="fixed inset-0 z-[100] flex items-center justify-center p-4"
     style="display: none;">

  <!-- Backdrop -->
  <div class="absolute inset-0 bg-black/50" @click="showProfileModal = false"></div>

  <!-- Modal Content -->
  <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto" @click.stop>
    <!-- Header -->
    <div class="sticky top-0 bg-white border-b border-[#dee1e6] p-6 pb-4 rounded-t-2xl z-10">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-[#171a1f]">Pengaturan Profil</h2>
        <button @click="showProfileModal = false" class="p-1.5 hover:bg-gray-100 rounded-lg">
          <iconify-icon icon="lucide:x" width="20" class="text-[#565d6d]"></iconify-icon>
        </button>
      </div>
      <!-- Tabs -->
      <div class="flex gap-4 mt-4 border-b border-[#dee1e6] -mb-4">
        <button @click="activeTab = 'info'" :class="activeTab === 'info' ? 'border-[#3d8af5] text-[#3d8af5]' : 'border-transparent text-[#565d6d]'" class="pb-3 border-b-2 text-sm font-semibold">
          Informasi Akun
        </button>
        <button @click="activeTab = 'password'" :class="activeTab === 'password' ? 'border-[#3d8af5] text-[#3d8af5]' : 'border-transparent text-[#565d6d]'" class="pb-3 border-b-2 text-sm font-semibold">
          Ganti Password
        </button>
      </div>
    </div>

    <!-- Tab: Info -->
    <div x-show="activeTab === 'info'" class="p-6">
      <form method="POST" action="{{ route('profile.update') }}" id="profile-info-form">
        @csrf
        @method('PUT')
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-semibold text-[#171a1f] mb-1.5">Nama Lengkap</label>
            <input type="text" name="name" value="{{ auth()->user()->name }}" required
              class="w-full px-4 py-2.5 border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20">
          </div>
          <div>
            <label class="block text-sm font-semibold text-[#171a1f] mb-1.5">Email</label>
            <input type="email" name="email" value="{{ auth()->user()->email }}" required
              class="w-full px-4 py-2.5 border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20">
          </div>
          <div>
            <label class="block text-sm font-semibold text-[#171a1f] mb-1.5">No. Telepon</label>
            <input type="text" name="phone" value="{{ auth()->user()->phone }}"
              class="w-full px-4 py-2.5 border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20">
          </div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
          <button type="button" @click="showProfileModal = false" class="px-5 py-2.5 border border-[#dee1e6] rounded-xl text-sm font-medium text-[#565d6d] hover:bg-gray-50">Batal</button>
          <button type="submit" class="px-5 py-2.5 bg-[#3d8af5] text-white rounded-xl text-sm font-medium shadow-md hover:bg-blue-600">Simpan Perubahan</button>
        </div>
      </form>
    </div>

    <!-- Tab: Password -->
    <div x-show="activeTab === 'password'" class="p-6">
      <form method="POST" action="{{ route('profile.password') }}" id="profile-password-form">
        @csrf
        @method('PUT')
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-semibold text-[#171a1f] mb-1.5">Password Saat Ini</label>
            <input type="password" name="current_password" required
              class="w-full px-4 py-2.5 border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20">
          </div>
          <div>
            <label class="block text-sm font-semibold text-[#171a1f] mb-1.5">Password Baru</label>
            <input type="password" name="password" required minlength="8"
              class="w-full px-4 py-2.5 border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20">
          </div>
          <div>
            <label class="block text-sm font-semibold text-[#171a1f] mb-1.5">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" required minlength="8"
              class="w-full px-4 py-2.5 border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20">
          </div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
          <button type="button" @click="showProfileModal = false" class="px-5 py-2.5 border border-[#dee1e6] rounded-xl text-sm font-medium text-[#565d6d] hover:bg-gray-50">Batal</button>
          <button type="submit" class="px-5 py-2.5 bg-[#3d8af5] text-white rounded-xl text-sm font-medium shadow-md hover:bg-blue-600">Ganti Password</button>
        </div>
      </form>
    </div>
  </div>
</div>

@if(session('profile_success'))
<div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg text-sm font-medium z-[110]" x-data x-init="setTimeout(() => $el.remove(), 3000)">
  {{ session('profile_success') }}
</div>
@endif
@if(session('profile_error'))
<div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-xl shadow-lg text-sm font-medium z-[110]" x-data x-init="setTimeout(() => $el.remove(), 4000)">
  {{ session('profile_error') }}
</div>
@endif
