<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'E-Rapor BiMBA AIUEO - Admin')</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@3.0.2/dist/iconify-icon.min.js"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: "Inter", ui-sans-serif, system-ui, sans-serif;
      -webkit-font-smoothing: antialiased;
      color: #1e293b;
      background-color: #fafafb;
    }
    h1, h2, h3, h4, h5, h6 {
      font-family: "Inter", ui-sans-serif, system-ui, sans-serif;
      font-weight: 700;
      color: #171a1f;
      letter-spacing: -0.025em;
    }
    * { transition: all 0.2s ease; }
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #f1f5f9; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    .font-roboto { font-family: "Roboto", ui-sans-serif, system-ui, sans-serif; }
    .sidebar-active { background: linear-gradient(135deg, #2563EB 0%, #EAB308 50%, #DC2626 100%); color: white; }
    .main-shadow { box-shadow: 0px 1px 2.5px 0px rgba(23, 26, 31, 0.07), 0px 0px 2px 0px rgba(23, 26, 31, 0.08); }
    .help-card-gradient { background: linear-gradient(135deg, #2563EB 0%, #EAB308 50%, #DC2626 100%); box-shadow: 0px 4px 7px 0px rgba(220, 38, 38, 0.2); }
    .table-row-hover:hover { background-color: #f9fafb; }
    .status-pill { padding: 2px 12px; border-radius: 10px; font-size: 12px; font-weight: 500; display: inline-flex; align-items: center; justify-content: center; }
    .status-active { background-color: rgba(99, 233, 143, 0.1); color: #16a34a; }
    .status-cuti { background-color: #f3f4f6; color: #565d6d; }
    .logo-gradient { background: linear-gradient(135deg, #2563EB 0%, #EAB308 50%, #DC2626 100%); }
    @yield('styles')
  </style>
  @stack('head')
</head>
<body class="min-h-screen flex flex-col lg:flex-row">

  <!-- Sidebar -->
  <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-[#fafafb] border-r border-[#dee1e6] transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col">
    <div class="p-6 flex items-center gap-3">
      <div class="w-8 h-8 logo-gradient rounded-full flex items-center justify-center">
        <img src="{{ asset('assets/IMG_1.svg') }}" class="w-5 h-5" alt="Logo">
      </div>
      <span class="font-bold text-lg leading-tight" style="background: linear-gradient(135deg, #2563EB 0%, #EAB308 50%, #DC2626 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">E-Rapor BiMBA</span>
    </div>

    <nav class="flex-1 px-4 space-y-1 mt-2">
      <a href="{{ route('admin.dashboard') }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm font-roboto {{ request()->routeIs('admin.dashboard') ? 'sidebar-active' : 'text-[#565d6d] hover:bg-gray-100' }}">
        <iconify-icon icon="lucide:layout-dashboard" width="18"></iconify-icon>
        Dashboard
      </a>
      <a href="{{ route('admin.murid') }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm font-roboto {{ request()->routeIs('admin.murid*') ? 'sidebar-active' : 'text-[#565d6d] hover:bg-gray-100' }}">
        <iconify-icon icon="lucide:users" width="18"></iconify-icon>
        Data Murid
      </a>
      <a href="{{ route('admin.guru') }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm font-roboto {{ request()->routeIs('admin.guru*') ? 'sidebar-active' : 'text-[#565d6d] hover:bg-gray-100' }}">
        <iconify-icon icon="lucide:user-check" width="18"></iconify-icon>
        Data Guru
      </a>
      <a href="{{ route('admin.user') }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm font-roboto {{ request()->routeIs('admin.user*') ? 'sidebar-active' : 'text-[#565d6d] hover:bg-gray-100' }}">
        <iconify-icon icon="lucide:shield" width="18"></iconify-icon>
        Manajemen User
      </a>
      <a href="{{ route('admin.pengaturan') }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm font-roboto {{ request()->routeIs('admin.pengaturan') ? 'sidebar-active' : 'text-[#565d6d] hover:bg-gray-100' }}">
        <iconify-icon icon="lucide:settings" width="18"></iconify-icon>
        Pengaturan
      </a>
    </nav>

    <div class="p-4 border-t border-[#dee1e6]">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="flex items-center gap-3 px-4 py-2.5 w-full text-[#D92626] hover:bg-red-50 rounded-lg font-medium text-sm font-roboto">
          <iconify-icon icon="lucide:log-out" width="18"></iconify-icon>
          Logout
        </button>
      </form>
    </div>
  </aside>

  <!-- Mobile Overlay -->
  <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden"></div>

  <!-- Main Content Area -->
  <div class="flex-1 flex flex-col lg:ml-64 min-w-0">

    <!-- Header -->
    <header class="sticky top-0 z-30 h-16 bg-white/80 backdrop-blur-md border-b border-[#dee1e6] flex items-center justify-between px-4 lg:px-8">
      <div class="flex items-center gap-4">
        <button id="mobile-menu-btn" class="lg:hidden p-2 text-[#565d6d] hover:bg-gray-100 rounded-lg">
          <iconify-icon icon="lucide:menu" width="22"></iconify-icon>
        </button>
        <div class="hidden lg:block">
          <h2 class="text-base font-semibold text-[#171a1f]">@yield('page-title', 'Dashboard')</h2>
        </div>
      </div>

      <div class="flex items-center gap-4">
        @include('partials.notification-bell')
        @include('partials.profile-dropdown')
      </div>
    </header>

    <!-- Profile Modal -->
    @include('partials.profile-modal')

    <!-- Page Content -->
    <main class="flex-1 p-4 lg:p-8">
      @yield('content')
    </main>

    <!-- Footer -->
    <footer class="px-8 py-4 border-t border-[#dee1e6] text-center">
      <p class="text-xs text-[#565d6d] font-roboto">© {{ date('Y') }} E-Rapor BiMBA AIUEO Smart Education Centre. All rights reserved.</p>
    </footer>
  </div>

  <script>
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    function toggleSidebar() {
      sidebar.classList.toggle('-translate-x-full');
      overlay.classList.toggle('hidden');
    }
    if (mobileMenuBtn) mobileMenuBtn.addEventListener('click', toggleSidebar);
    if (overlay) overlay.addEventListener('click', toggleSidebar);
  </script>
  @stack('scripts')
</body>
</html>
