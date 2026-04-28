{{-- Profile Dropdown (Alpine.js) --}}
<div x-data="{ profileOpen: false }" class="relative">
  <button @click="profileOpen = !profileOpen" class="flex items-center gap-3 pl-4 border-l border-[#dee1e6] cursor-pointer hover:opacity-80">
    <div class="text-right hidden sm:block">
      <p class="text-sm font-semibold text-[#171a1f] font-roboto leading-none">{{ auth()->user()->name }}</p>
      <p class="text-xs text-[#565d6d] font-roboto mt-0.5">{{ ucfirst(auth()->user()->role) }}</p>
    </div>
    @php
      $avatarColors = ['admin' => '#2563EB', 'guru' => '#DC2626', 'wali' => '#EAB308'];
      $avatarColor = $avatarColors[auth()->user()->role] ?? '#2563EB';
    @endphp
    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-sm" style="background-color: {{ $avatarColor }}">
      {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
    </div>
    <iconify-icon icon="lucide:chevron-down" width="14" class="text-[#565d6d] hidden sm:block"></iconify-icon>
  </button>

  <!-- Dropdown -->
  <div x-show="profileOpen" @click.away="profileOpen = false" x-transition
    class="absolute right-0 top-full mt-2 w-72 bg-white rounded-xl border border-[#dee1e6] shadow-lg z-50 overflow-hidden">
    <!-- User Info -->
    <div class="p-4 bg-[#f3f4f6]/50 border-b border-[#dee1e6]">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0" style="background-color: {{ $avatarColor }}">
          {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
        </div>
        <div class="min-w-0">
          <p class="text-sm font-semibold text-[#171a1f] truncate">{{ auth()->user()->name }}</p>
          <p class="text-xs text-[#565d6d] truncate">{{ auth()->user()->email }}</p>
        </div>
      </div>
    </div>

    <!-- Menu Items -->
    <div class="py-2">
      <button @click="profileOpen = false; $dispatch('open-profile-modal')" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-[#171a1f] hover:bg-gray-50 cursor-pointer">
        <iconify-icon icon="lucide:settings" width="16" class="text-[#565d6d]"></iconify-icon>
        Pengaturan Profil
      </button>
    </div>

    <!-- Logout -->
    <div class="border-t border-[#dee1e6] py-2">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-[#D92626] hover:bg-red-50 cursor-pointer">
          <iconify-icon icon="lucide:log-out" width="16"></iconify-icon>
          Logout
        </button>
      </form>
    </div>
  </div>
</div>
