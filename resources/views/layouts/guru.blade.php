<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'E-Rapor BiMBA AIUEO - Guru')</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@3.0.2/dist/iconify-icon.min.js"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: "Inter", ui-sans-serif, system-ui, sans-serif; -webkit-font-smoothing: antialiased; color: #1e293b; background-color: #f8fafc; }
    h1, h2, h3, h4, h5, h6 { font-family: "Inter", sans-serif; font-weight: 700; color: #0f172a; letter-spacing: -0.025em; }
    * { transition: all 0.2s ease; }
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #f1f5f9; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .font-roboto { font-family: "Roboto", ui-sans-serif, system-ui, sans-serif; }
    .sidebar-active-gradient { background: linear-gradient(135deg, #2563EB 0%, #EAB308 50%, #DC2626 100%); border-radius: 10px; color: white; }
    .main-shadow { box-shadow: 0px 1px 2.5px 0px rgba(23,26,31,0.07), 0px 0px 2px 0px rgba(23,26,31,0.08); }
    .table-row-hover:hover { background-color: #f8fafc; }
    .logo-gradient { background: linear-gradient(135deg, #2563EB 0%, #EAB308 50%, #DC2626 100%); }
    @media print {
      .no-print { display: none !important; }
      body { background: white !important; }
      main { margin-left: 0 !important; padding: 0 !important; }
    }
    @yield('styles')
  </style>
  @stack('head')
</head>
<body class="min-h-screen">

  <!-- Header -->
  <header class="no-print fixed top-0 left-0 right-0 h-16 bg-white/80 backdrop-blur-md border-b border-[#dee1e6] z-50 flex items-center justify-between px-4 lg:px-8">
    <div class="flex items-center gap-4">
      <button id="mobile-menu-btn" class="lg:hidden p-2 hover:bg-gray-100 rounded-lg">
        <iconify-icon icon="lucide:menu" width="22"></iconify-icon>
      </button>
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 logo-gradient rounded-full flex items-center justify-center">
          <img src="{{ asset('assets/IMG_1.svg') }}" class="w-5 h-5" alt="Logo">
        </div>
        <span class="font-bold text-lg" style="background: linear-gradient(135deg, #2563EB 0%, #EAB308 50%, #DC2626 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">E-Rapor BiMBA AIUEO</span>
      </div>
    </div>
    <div class="flex items-center gap-4">
      @include('partials.notification-bell')
      @include('partials.profile-dropdown')
    </div>
  </header>

  <!-- Profile Modal -->
  @include('partials.profile-modal')

  <div class="flex pt-16 min-h-screen print:pt-0">
    <!-- Sidebar -->
    <aside id="sidebar" class="no-print fixed inset-y-0 left-0 z-40 w-64 bg-[#fafafb] border-r border-[#dee1e6] transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out pt-16 flex flex-col">
      <nav class="flex-1 px-4 py-6 space-y-1">
        <a href="{{ route('guru.dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm {{ request()->routeIs('guru.dashboard') ? 'sidebar-active-gradient' : 'text-[#565d6d] hover:bg-gray-100' }}">
          <iconify-icon icon="lucide:layout-dashboard" width="18"></iconify-icon>
          Dashboard
        </a>
        <a href="{{ route('guru.nilai') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm {{ request()->routeIs('guru.nilai*') ? 'sidebar-active-gradient' : 'text-[#565d6d] hover:bg-gray-100' }}">
          <iconify-icon icon="lucide:clipboard-list" width="18"></iconify-icon>
          Pengolahan Nilai
        </a>
        <a href="{{ route('guru.grafik') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm {{ request()->routeIs('guru.grafik') ? 'sidebar-active-gradient' : 'text-[#565d6d] hover:bg-gray-100' }}">
          <iconify-icon icon="lucide:bar-chart-2" width="18"></iconify-icon>
          Grafik Perkembangan
        </a>
        <a href="{{ route('guru.rapor') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm {{ request()->routeIs('guru.rapor') ? 'sidebar-active-gradient' : 'text-[#565d6d] hover:bg-gray-100' }}">
          <iconify-icon icon="lucide:printer" width="18"></iconify-icon>
          Cetak Rapor
        </a>
      </nav>
      <div class="p-4 border-t border-[#dee1e6]">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="flex items-center gap-3 px-4 py-2.5 w-full rounded-lg hover:bg-red-50 text-[#D92626] font-medium text-sm">
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
