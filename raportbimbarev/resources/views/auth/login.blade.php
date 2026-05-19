<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  @php
    $institutionName = \App\Models\Setting::get('institution_name', 'BiMBA AIUEO Smart Education Centre');
    $landingTitle = \App\Models\Setting::get('landing_title', 'E-Rapor');
    $landingHighlight = \App\Models\Setting::get('landing_highlight', 'BiMBA');
  @endphp
  <title>Login - {{ $landingTitle }} {{ $landingHighlight }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    body { font-family: "Inter", ui-sans-serif, system-ui, sans-serif; color: #1e293b; -webkit-font-smoothing: antialiased; }
    h1, h2, h3, h4, h5, h6 { font-family: "Roboto", ui-sans-serif, system-ui, sans-serif; font-weight: 700; color: #0f172a; }
    label { font-family: "Inter", ui-sans-serif, system-ui, sans-serif; font-weight: 600; font-size: 0.875rem; color: #334155; }
    input::placeholder { color: #94a3b8; }
    .soft-shadow { box-shadow: 0 20px 50px -12px rgba(0,0,0,0.08); }
    .bg-gradient-subtle { background: linear-gradient(135deg, #f8fafc 0%, #fff7ed 100%); }
    .logo-gradient { background: #F97316; }
    .btn-gradient { background: #F97316; }
    .bg-bimba-gradient { background: linear-gradient(135deg, #F97316 0%, #EA580C 100%); }
    .bg-bimba-soft { background: linear-gradient(135deg, rgba(249,115,22,0.14) 0 33.33%, rgba(253,186,116,0.14) 33.33% 66.66%, rgba(249,115,22,0.14) 66.66% 100%); }
    .bg-login-fresh { background: linear-gradient(135deg, #fffef7 0%, #fff7ed 50%, #fff7ed 100%); }
    .panel-shell { background: linear-gradient(135deg, rgba(255,255,255,0.74) 0%, rgba(255,255,255,0.62) 100%); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); border: 1px solid rgba(255,255,255,0.72); }
    .panel-form-bg { background: linear-gradient(160deg, rgba(255,255,255,0.92) 0%, rgba(248,250,252,0.82) 100%); }
    .photo-card-bg { background: linear-gradient(160deg, rgba(255,255,255,0.94) 0%, rgba(255,247,237,0.86) 55%, rgba(255,247,237,0.84) 100%); border: 1px solid rgba(222,225,230,0.65); }
    * { transition: all 0.2s ease; }
  </style>
</head>
<body class="bg-login-fresh min-h-screen flex flex-col items-center justify-center p-4 md:p-8 relative overflow-x-hidden">
  <a href="{{ url('/') }}" class="absolute left-4 top-4 md:left-8 md:top-8 z-10 inline-flex items-center gap-2 px-4 py-2 bg-white/80 border border-[#dee1e6] rounded-xl text-sm font-semibold text-[#171a1f] hover:bg-white soft-shadow">
    <iconify-icon icon="lucide:arrow-left" width="16"></iconify-icon>
    Kembali ke Beranda
  </a>

  <!-- Decorative Background -->
  <div class="absolute top-[8%] left-[3%] w-72 h-72 bg-[#F97316]/12 rounded-full blur-[64px] -z-10"></div>
  <div class="absolute bottom-[8%] right-[2%] w-[28rem] h-[28rem] bg-[#FB923C]/12 rounded-full blur-[64px] -z-10"></div>
  <div class="absolute top-[52%] right-[18%] w-56 h-56 bg-[#F97316]/10 rounded-full blur-[64px] -z-10"></div>
  <div class="absolute bottom-[20%] left-[18%] w-40 h-40 bg-[#22c55e]/10 rounded-full blur-[64px] -z-10"></div>

  <!-- Card Container -->
  <main class="w-full max-w-[1024px] panel-shell rounded-2xl shadow-[0px_8.5px_13.75px_0px_#171a1f38,_0px_0px_2px_0px_#171a1f14] overflow-hidden flex flex-col lg:flex-row min-h-[680px]">

    <!-- Left: Login Form -->
    <section class="w-full lg:w-[52%] p-8 md:p-16 flex flex-col justify-between panel-form-bg">
      <div>
        <!-- Logo -->
        <div class="flex items-center gap-3 mb-12">
          @php
            $logo = \App\Models\Setting::get('institution_logo');
          @endphp
          <div class="w-10 h-10 {{ $logo ? '' : 'logo-gradient' }} rounded-full flex items-center justify-center {{ $logo ? '' : 'bg-white' }}">
            @if($logo)
              <img src="{{ asset('storage/' . $logo) }}" alt="Logo" class="w-10 h-10 rounded-full object-cover">
            @else
              <img src="{{ asset('assets/IMG_1.svg') }}" alt="Logo" class="w-6 h-6">
            @endif
          </div>
          <span class="font-bold text-2xl tracking-tight text-[#0f172a]">{{ $landingTitle }} {{ $landingHighlight }}</span>
        </div>

        <!-- Welcome Text -->
        <div class="mb-10">
          <h1 class="text-3xl leading-tight mb-2">Selamat Datang</h1>
          <p class="text-[#565d6d] font-normal text-base">Masuk untuk memantau perkembangan belajar</p>
        </div>

        <!-- Session Error -->
        @if(session('error'))
          <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm">
            {{ session('error') }}
          </div>
        @endif

        @if($errors->any())
          <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm">
            <ul class="list-disc list-inside space-y-1">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
          @csrf

          <!-- Role Dropdown -->
          <div class="space-y-2">
            <label for="role">Login Sebagai</label>
            <div class="relative">
              <select id="role" name="role" class="w-full h-12 px-4 bg-white border border-[#dee1e6] rounded-xl appearance-none focus:outline-none focus:ring-2 focus:ring-[#F97316]/20 focus:border-[#F97316] transition-all cursor-pointer text-sm">
                <option value="" disabled selected>Pilih Peran Anda</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                <option value="wali" {{ old('role') == 'wali' ? 'selected' : '' }}>Orang Tua / Wali</option>
              </select>
              <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-[#565d6d]">
                <iconify-icon icon="lucide:chevron-down" width="16"></iconify-icon>
              </div>
            </div>
          </div>

          <!-- Email/Username Input -->
          <div class="space-y-2">
            <label for="email">Username / Email</label>
            <div class="relative flex items-center">
              <div class="absolute left-4 text-[#565d6d]/60">
                <iconify-icon icon="lucide:user" width="18"></iconify-icon>
              </div>
              <input type="text" id="email" name="email" value="{{ old('email') }}" placeholder="Masukkan username atau email"
                class="w-full h-12 pl-12 pr-4 bg-white border border-[#dee1e6] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#F97316]/20 focus:border-[#F97316] transition-all text-sm">
            </div>
          </div>

          <!-- Password Input -->
          <div class="space-y-2">
            <div class="flex justify-between items-center">
              <label for="password">Kata Sandi</label>
              <a href="#" class="text-xs font-medium text-[#F97316] hover:text-[#EA580C] hover:underline">Lupa Sandi?</a>
            </div>
            <div class="relative flex items-center">
              <div class="absolute left-4 text-[#565d6d]/60">
                <iconify-icon icon="lucide:lock" width="18"></iconify-icon>
              </div>
              <input type="password" id="password" name="password" placeholder="Masukkan kata sandi"
                class="w-full h-12 pl-12 pr-12 bg-white border border-[#dee1e6] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#F97316]/20 focus:border-[#F97316] transition-all text-sm">
              <button type="button" id="togglePassword" class="absolute right-4 hover:opacity-70 text-[#565d6d]/60">
                <iconify-icon icon="lucide:eye" width="18"></iconify-icon>
              </button>
            </div>
          </div>

          <!-- Remember Me -->
          <div class="flex items-center gap-2">
            <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded border-[#565d6d] text-[#F97316] focus:ring-[#F97316] cursor-pointer">
            <label for="remember" class="text-sm font-normal text-[#565d6d] cursor-pointer">Ingat saya di perangkat ini</label>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="w-full h-12 btn-gradient hover:bg-[#EA580C] text-white font-semibold rounded-xl shadow-md flex items-center justify-center gap-2 transition-all active:scale-[0.98]">
            <span>Masuk Sekarang</span>
            <iconify-icon icon="lucide:arrow-right" width="18"></iconify-icon>
          </button>
        </form>
      </div>

      <!-- Footer Links -->
      <footer class="mt-12 pt-6 border-t border-[#dee1e6]">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 text-[12px] text-[#565d6d]">
          <span>© {{ date('Y') }} {{ $institutionName }}</span>
          <div class="flex gap-6">
            <a href="#" class="text-[#F97316] hover:text-[#EA580C] transition-colors">Bantuan</a>
            <a href="#" class="text-[#F97316] hover:text-[#EA580C] transition-colors">Kebijakan Privasi</a>
          </div>
        </div>
      </footer>
    </section>

    <!-- Right: Illustration -->
    <section class="hidden lg:flex w-[48%] bg-bimba-soft relative flex-col items-center justify-center p-12 overflow-hidden">
      <div class="absolute top-[-20px] left-[-20px] w-32 h-32 bg-[#F97316]/20 rounded-full blur-[64px]"></div>
      <div class="absolute top-8 right-8 opacity-30">
        <iconify-icon icon="lucide:palette" width="32" class="text-[#FDBA74]"></iconify-icon>
      </div>
      <div class="absolute top-[25%] left-8 opacity-30">
        <iconify-icon icon="lucide:book-open" width="24" class="text-[#F97316]"></iconify-icon>
      </div>
      <div class="absolute bottom-12 left-12 opacity-40">
        <iconify-icon icon="lucide:calculator" width="40" class="text-[#FDBA74]"></iconify-icon>
      </div>
      <div class="absolute bottom-[-50px] right-[-50px] w-48 h-48 bg-[#F97316]/12 rounded-full blur-[64px]"></div>

      <!-- Illustration Card -->
      <div class="relative z-10 photo-card-bg p-6 rounded-2xl shadow-[0px_8.5px_13.75px_0px_#171a1f38,_0px_0px_2px_0px_#171a1f14] mb-10 w-full max-w-[304px]">
        @php
          $heroImage = \App\Models\Setting::get('hero_image');
        @endphp
        @if($heroImage)
          <img src="{{ asset('storage/' . $heroImage) }}" alt="BiMBA AIUEO School" class="w-full h-auto rounded-lg object-contain">
        @else
          <img src="{{ asset('assets/IMG_21.webp') }}" alt="BiMBA AIUEO School" class="w-full h-auto rounded-lg">
        @endif
      </div>

      <div class="text-center max-w-[320px] relative z-10">
        <h2 class="text-2xl font-bold mb-4 text-[#0f172a]">Pintar & Menyenangkan</h2>
        <p class="text-[#565d6d] text-sm leading-relaxed">Membangun Minat Baca dan Belajar Anak (BiMBA) dengan metode yang unik dan digital.</p>
      </div>
    </section>
  </main>

  <!-- Support text -->
  <div class="mt-8 text-[12px] text-[#565d6d]/60 text-center">
    Didukung oleh Smart Education Centre Technology
  </div>

  <script>
    const toggleBtn = document.getElementById('togglePassword');
    const pwdInput = document.getElementById('password');
    if (toggleBtn) {
      toggleBtn.addEventListener('click', () => {
        const isPassword = pwdInput.type === 'password';
        pwdInput.type = isPassword ? 'text' : 'password';
        toggleBtn.querySelector('iconify-icon').setAttribute('icon', isPassword ? 'lucide:eye-off' : 'lucide:eye');
      });
    }
  </script>
</body>
</html>
