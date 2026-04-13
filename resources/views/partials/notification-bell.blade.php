<!-- Notification Bell Dropdown -->
<div x-data="notificationBell()" x-init="fetchNotifications()" class="relative">
  <button @click="toggle()" class="p-2 text-[#565d6d] hover:bg-gray-100 rounded-full relative">
    <iconify-icon icon="lucide:bell" width="20"></iconify-icon>
    <span x-show="unreadCount > 0" x-text="unreadCount > 9 ? '9+' : unreadCount"
          class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-[#D92626] text-white text-[10px] font-bold rounded-full flex items-center justify-center px-1"></span>
  </button>

  <!-- Dropdown Panel -->
  <div x-show="open" @click.away="open = false" x-transition
       class="absolute right-0 top-full mt-2 w-96 max-h-[480px] bg-white rounded-2xl border border-[#dee1e6] shadow-xl z-50 flex flex-col overflow-hidden">

    <!-- Header -->
    <div class="flex items-center justify-between px-5 py-4 border-b border-[#dee1e6]">
      <h3 class="text-base font-bold text-[#171a1f]">Notifikasi</h3>
      <button x-show="unreadCount > 0" @click="markAllRead()" class="text-xs font-medium text-[#3d8af5] hover:underline">
        Tandai semua dibaca
      </button>
    </div>

    <!-- Notification List -->
    <div class="flex-1 overflow-y-auto divide-y divide-[#dee1e6]">
      <template x-if="notifications.length === 0">
        <div class="py-12 text-center">
          <iconify-icon icon="lucide:bell-off" width="36" class="text-[#bdc1ca] mx-auto mb-3"></iconify-icon>
          <p class="text-sm text-[#565d6d]">Belum ada notifikasi</p>
        </div>
      </template>
      <template x-for="n in notifications" :key="n.id">
        <a :href="n.link || '#'" @click="markRead(n)"
           :class="n.read ? 'bg-white' : 'bg-[#F1F6FE]/40'"
           class="flex items-start gap-3 px-5 py-4 hover:bg-gray-50 cursor-pointer">
          <div class="flex-shrink-0 mt-0.5">
            <div :class="{
              'bg-green-100 text-green-600': n.type === 'success',
              'bg-blue-100 text-[#3d8af5]': n.type === 'info',
              'bg-amber-100 text-amber-600': n.type === 'warning',
              'bg-red-100 text-[#D92626]': n.type === 'error'
            }" class="w-9 h-9 rounded-full flex items-center justify-center">
              <iconify-icon :icon="n.icon" width="16"></iconify-icon>
            </div>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2">
              <p class="text-sm font-semibold text-[#171a1f] truncate" x-text="n.title"></p>
              <span x-show="!n.read" class="flex-shrink-0 w-2 h-2 bg-[#3d8af5] rounded-full mt-1.5"></span>
            </div>
            <p class="text-xs text-[#565d6d] mt-0.5 line-clamp-2" x-text="n.message"></p>
            <p class="text-[10px] text-[#9095a0] mt-1 font-roboto" x-text="n.time"></p>
          </div>
        </a>
      </template>
    </div>
  </div>
</div>

<script>
function notificationBell() {
  return {
    open: false,
    notifications: [],
    unreadCount: 0,
    toggle() {
      this.open = !this.open;
      if (this.open) this.fetchNotifications();
    },
    async fetchNotifications() {
      try {
        const res = await fetch('/notifications', {
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        this.notifications = data.notifications;
        this.unreadCount = data.unread_count;
      } catch (e) { console.error('Failed to fetch notifications', e); }
    },
    async markRead(n) {
      if (n.read) return;
      try {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        await fetch('/notifications/' + n.id + '/read', {
          method: 'POST',
          headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' }
        });
        n.read = true;
        this.unreadCount = Math.max(0, this.unreadCount - 1);
      } catch (e) { console.error('Failed to mark as read', e); }
    },
    async markAllRead() {
      try {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        await fetch('/notifications/read-all', {
          method: 'POST',
          headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' }
        });
        this.notifications.forEach(n => n.read = true);
        this.unreadCount = 0;
      } catch (e) { console.error('Failed to mark all as read', e); }
    }
  };
}
</script>
