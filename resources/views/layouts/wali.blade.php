<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'E-Rapor BiMBA AIUEO - Wali Murid')</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@3.0.2/dist/iconify-icon.min.js"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    body { font-family: "Inter", ui-sans-serif, system-ui, sans-serif; -webkit-font-smoothing: antialiased; color: #1e293b; background-color: #f3f4f6; }
    h1, h2, h3, h4, h5, h6 { font-family: "Roboto", ui-sans-serif, system-ui, sans-serif; font-weight: 700; color: #171a1f; }
    * { transition: all 0.2s ease; }
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .sidebar-active-item { background: linear-gradient(135deg, #2563EB 0%, #EAB308 50%, #DC2626 100%); color: white; }
    .custom-shadow { box-shadow: 0px 1px 2.5px 0px rgba(23,26,31,0.07), 0px 0px 2px 0px rgba(23,26,31,0.08); }
    .hero-gradient { background: linear-gradient(135deg, #FEE2E2 0%, #FEF9C3 50%, #DBEAFE 100%); }
    .logo-gradient { background: linear-gradient(135deg, #2563EB 0%, #EAB308 50%, #DC2626 100%); }
    @media print {
      .no-print { display: none !important; }
      body { background: white !important; }
      main { margin-left: 0 !important; padding: 0 !important; }
    }
  </style>
  @stack('head')
</head>
<body class="min-h-screen">

  <!-- Header -->
  <header class="no-print fixed top-0 left-0 right-0 h-16 bg-white/60 backdrop-blur-md border-b border-[#dee1e6] z-50 flex items-center justify-between px-4 lg:px-12">
    <div class="flex items-center gap-3">
      <div class="w-8 h-8 logo-gradient rounded-full flex items-center justify-center">
        <img src="{{ asset('assets/IMG_1.svg') }}" alt="Logo" class="w-5 h-5">
      </div>
      <span class="font-bold text-lg lg:text-xl font-['Inter']" style="background: linear-gradient(135deg, #2563EB 0%, #EAB308 50%, #DC2626 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">E-Rapor BiMBA AIUEO</span>
    </div>
    <div class="flex items-center gap-4 lg:gap-6">
      @include('partials.notification-bell')
      <div class="h-8 w-px bg-[#dee1e6] hidden sm:block"></div>
      @include('partials.profile-dropdown')
      <button id="mobile-menu-btn" class="lg:hidden p-2">
        <iconify-icon icon="lucide:menu" width="22"></iconify-icon>
      </button>
    </div>
  </header>

  <!-- Profile Modal -->
  @include('partials.profile-modal')

  <div class="flex pt-16 min-h-screen print:pt-0">
    <!-- Sidebar -->
    <aside id="sidebar" class="no-print fixed inset-y-0 left-0 z-40 w-64 bg-[#fafafb] border-r border-[#dee1e6] transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out pt-16 flex flex-col">
      <nav class="flex-1 px-4 py-6 space-y-2">
        <a href="{{ route('wali.dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm {{ request()->routeIs('wali.dashboard') ? 'sidebar-active-item' : 'text-[#565d6d] hover:bg-gray-100' }}">
          <iconify-icon icon="lucide:layout-dashboard" width="18"></iconify-icon>
          Dashboard
        </a>
        <a href="{{ route('wali.rapor') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm {{ request()->routeIs('wali.rapor') ? 'sidebar-active-item' : 'text-[#565d6d] hover:bg-gray-100' }}">
          <iconify-icon icon="lucide:file-text" width="18"></iconify-icon>
          Laporan Rapor
        </a>
        <a href="{{ route('wali.riwayat') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm {{ request()->routeIs('wali.riwayat') ? 'sidebar-active-item' : 'text-[#565d6d] hover:bg-gray-100' }}">
          <iconify-icon icon="lucide:history" width="18"></iconify-icon>
          Riwayat Rapor
        </a>
      </nav>
      <div class="p-4 border-t border-[#dee1e6]">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="flex items-center gap-3 px-3 py-2.5 text-[#D92626] hover:bg-red-50 w-full rounded-lg font-medium text-sm">
            <iconify-icon icon="lucide:log-out" width="18"></iconify-icon>
            Logout
          </button>
        </form>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 lg:ml-64 p-4 lg:p-8 overflow-x-hidden">
      @yield('content')
    </main>
  </div>

  <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden"></div>
  <script>
    const btn = document.getElementById('mobile-menu-btn');
    const sb  = document.getElementById('sidebar');
    const ov  = document.getElementById('sidebar-overlay');
    if (btn) btn.addEventListener('click', () => { sb.classList.toggle('-translate-x-full'); ov.classList.toggle('hidden'); });
    if (ov)  ov.addEventListener('click',  () => { sb.classList.add('-translate-x-full'); ov.classList.add('hidden'); });
  </script>
  @stack('scripts')
</body>
</html>
