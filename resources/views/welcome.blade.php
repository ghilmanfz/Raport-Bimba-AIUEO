<!DOCTYPE html>

<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  @php
    $institutionName = \App\Models\Setting::get('institution_name', 'BiMBA AIUEO Smart Education Centre');
    $logo = \App\Models\Setting::get('institution_logo');
    $supportWhatsapp = preg_replace('/\D+/', '', \App\Models\Setting::get('support_whatsapp', '6281234567890'));
    $supportEmail = \App\Models\Setting::get('support_email', 'info@bimba-aiueo.com');
    $landingBadge = \App\Models\Setting::get('landing_badge', 'Masa Depan Belajar Anak');
    $landingTitle = \App\Models\Setting::get('landing_title', 'E-Rapor');
    $landingHighlight = \App\Models\Setting::get('landing_highlight', 'BiMBA AIUEO');
    $landingDescription = \App\Models\Setting::get('landing_description', 'Pantau Perkembangan Belajar Anak Secara Digital. Solusi cerdas untuk pendidikan masa kini yang lebih transparan dan efisien.');
    $landingCtaTitle = \App\Models\Setting::get('landing_cta_title', 'Siap Mencoba Era Baru Pelaporan Pendidikan?');
    $landingCtaDescription = \App\Models\Setting::get('landing_cta_description', 'Bergabunglah dengan orang tua dan guru yang telah menggunakan E-Rapor BiMBA AIUEO untuk masa depan pendidikan yang lebih baik.');
    $supportWhatsappUrl = $supportWhatsapp ? 'https://wa.me/' . $supportWhatsapp . '?text=Halo%20BiMBA%2C%20saya%20ingin%20bertanya.' : '#';
  @endphp
  <title>{{ $landingTitle }} {{ $landingHighlight }} - Digital Learning Progress</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    body { font-family: "Inter", ui-sans-serif, system-ui, sans-serif; color: #171a1f; -webkit-font-smoothing: antialiased; background-color: #ffffff; }
    h1, h2, h3, h4, h5, h6 { font-family: "Poppins", ui-sans-serif, system-ui, sans-serif; font-weight: 700; letter-spacing: -0.02em; line-height: 1.2; }
    p { font-family: "Roboto", ui-sans-serif, system-ui, sans-serif; line-height: 1.6; color: #565d6d; }
    .glass-card { background: rgba(255,255,255,0.6); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.5); }
    .hero-gradient { background: linear-gradient(135deg, #FFF7ED 0%, #EFF6FF 52%, #F0FDF4 100%); }
    .cta-gradient { background: linear-gradient(135deg, #F97316 0%, #FB923C 30%, #3B82F6 72%, #16A34A 100%); }
    .soft-shadow { box-shadow: 0px 1px 2.5px 0px rgba(23,26,31,0.07), 0px 0px 2px 0px rgba(23,26,31,0.08); }
    .nav-blur { backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); background: rgba(255,255,255,0.7); }
    .logo-gradient { background: #F97316; }
    .btn-gradient { background: #F97316; }
    * { transition: all 0.2s ease; }
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #f1f5f9; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .floating-badge { box-shadow: 0px 8.5px 13.75px 0px rgba(23,26,31,0.22), 0px 0px 2px 0px rgba(23,26,31,0.08); }
  </style>
</head>
<body class="overflow-x-hidden">
  @php
    $sharedBanner = \App\Models\Setting::get('institution_banner') ? asset('storage/' . \App\Models\Setting::get('institution_banner')) : asset('assets/IMG_2.webp');
  @endphp

  <!-- Navigation -->
  <header class="fixed top-0 left-0 right-0 z-50 nav-blur border-b border-[#dee1e6] h-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 h-full flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 {{ $logo ? '' : 'logo-gradient' }} rounded-full flex items-center justify-center {{ $logo ? '' : 'bg-white' }}">
          @if($logo)
            <img src="{{ asset('storage/' . $logo) }}" class="w-8 h-8 rounded-full object-cover" alt="Logo">
          @else
            <img src="{{ asset('assets/IMG_1.svg') }}" class="w-5 h-5" alt="Logo">
          @endif
        </div>
        <span class="font-bold text-lg sm:text-xl font-['Inter'] text-[#171a1f]">{{ $landingTitle }} {{ $landingHighlight }}</span>
      </div>
      <div class="flex items-center gap-2 sm:gap-4">
        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-[#171a1f] hover:bg-gray-100 rounded-md">Login</a>
        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-white btn-gradient hover:bg-[#EA580C] rounded-md">Lihat Demo</a>
      </div>
    </div>
  </header>

  <main class="pt-16">
    <!-- Hero Section -->
    <section class="hero-gradient relative overflow-x-hidden min-h-[700px] lg:min-h-[820px] flex items-center">
      <div class="absolute top-0 right-0 w-[520px] h-full bg-[#3B82F6]/5 rounded-l-[9999px] hidden lg:block"></div>
      <div class="absolute bottom-10 left-10 w-24 h-24 bg-[#22C55E]/15 rounded-full blur-[40px]"></div>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 py-12 lg:py-20 grid lg:grid-cols-2 gap-12 lg:gap-16 items-center relative z-10">
        <!-- Left: Text content -->
        <div class="flex flex-col items-start">
          <div class="inline-flex items-center gap-2 px-4 py-1 bg-white/50 backdrop-blur-sm border border-[#F97316]/20 rounded-full mb-8">
            <img src="{{ asset('assets/IMG_3.svg') }}" class="w-3.5 h-3.5" alt="Smile">
            <span class="text-[12px] font-bold tracking-widest uppercase text-[#F97316]">{{ $landingBadge }}</span>
          </div>
          <h1 class="text-5xl sm:text-6xl lg:text-[68px] leading-tight mb-6">
            <span class="text-[#171a1f]">{{ $landingTitle }} </span>
            <span class="text-[#F97316]">{{ $landingHighlight }}</span>
          </h1>
          <p class="text-lg sm:text-xl mb-10 max-w-[520px]">
            {{ $landingDescription }}
          </p>
          <div class="flex flex-wrap gap-4 mb-12">
            <a href="{{ route('login') }}" class="px-10 py-4 btn-gradient text-white font-semibold rounded-full shadow-lg hover:bg-[#EA580C] text-lg">Mulai Sekarang</a>
            <a href="{{ route('login') }}" class="px-10 py-4 bg-white border-2 border-[#dee1e6] text-[#171a1f] font-semibold rounded-full hover:bg-gray-50 text-lg">Lihat Demo</a>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="glass-card soft-shadow px-5 py-4 rounded-full flex items-center gap-3">
              <div class="w-8 h-8 bg-[#F97316] rounded-full flex items-center justify-center flex-shrink-0">
                <img src="{{ asset('assets/IMG_4.svg') }}" class="w-4 h-4 brightness-0 invert" alt="Activity">
              </div>
              <span class="text-sm font-medium">Digital rapor berbasis progres</span>
            </div>
            <div class="glass-card soft-shadow px-5 py-4 rounded-full flex items-center gap-3">
              <div class="w-8 h-8 bg-[#3B82F6] rounded-full flex items-center justify-center flex-shrink-0">
                <img src="{{ asset('assets/IMG_5.svg') }}" class="w-4 h-4 brightness-0 invert" alt="Shield">
              </div>
              <span class="text-sm font-medium">Penilaian unik (K, B, P, T)</span>
            </div>
            <div class="glass-card soft-shadow px-5 py-4 rounded-full flex items-center gap-3">
              <div class="w-8 h-8 bg-[#22C55E] rounded-full flex items-center justify-center flex-shrink-0">
                <img src="{{ asset('assets/IMG_6.svg') }}" class="w-4 h-4 brightness-0 invert" alt="Clock">
              </div>
              <span class="text-sm font-medium">Monitoring real-time</span>
            </div>
          </div>
        </div>

        <!-- Right: Image card (matching login page style) -->
        <div class="flex justify-center lg:justify-end lg:pr-2">
          <div class="relative w-full max-w-[430px] lg:max-w-[460px] mt-8 mb-4 lg:mt-10 lg:mb-8">
            <!-- Main image card -->
            <div class="bg-white p-6 lg:p-7 rounded-2xl shadow-[0px_8.5px_13.75px_0px_#171a1f38,_0px_0px_2px_0px_#171a1f14] border border-[#e5e7eb]">
                @php $heroImage = \App\Models\Setting::get('hero_image'); $bannerImage = \App\Models\Setting::get('institution_banner'); @endphp
                @if($heroImage)
                  <img src="{{ asset('storage/' . $heroImage) }}" class="w-full aspect-[4/5] rounded-xl object-contain bg-[#FFF7ED]" alt="BiMBA AIUEO School">
                @elseif($bannerImage)
                  <img src="{{ asset('storage/' . $bannerImage) }}" class="w-full aspect-[4/5] rounded-xl object-cover bg-[#FFF7ED]" alt="BiMBA AIUEO School">
                @else
                  <img src="{{ asset('assets/IMG_21.webp') }}" class="w-full aspect-[4/5] rounded-xl object-contain bg-[#FFF7ED]" alt="BiMBA AIUEO School">
                @endif
            </div>

            <!-- Top badge (level info) -->
            <div class="absolute -top-5 -left-5 floating-badge bg-white rounded-2xl p-3 flex items-center gap-3 w-[172px] z-20 border border-[#DBEAFE]">
              <div class="w-9 h-9 bg-[#DBEAFE] rounded-full flex items-center justify-center flex-shrink-0">
                <img src="{{ asset('assets/IMG_1.svg') }}" class="w-4 h-4" alt="Graduation">
              </div>
              <div>
                <p class="text-[10px] font-bold uppercase text-[#565d6d] leading-none mb-1">Level Tercapai</p>
                <p class="text-sm font-bold text-[#171a1f]">Modul Baca 1A</p>
              </div>
            </div>

            <!-- Bottom badge (progress) -->
            <div class="absolute -bottom-5 -right-5 floating-badge bg-white rounded-2xl p-3 flex items-center gap-3 w-[168px] z-20 border border-[#DCFCE7]">
              <div class="w-9 h-9 bg-[#DCFCE7] rounded-full flex items-center justify-center flex-shrink-0">
                <img src="{{ asset('assets/IMG_7.svg') }}" class="w-4 h-4" alt="Heart">
              </div>
              <div class="flex-1">
                <p class="text-sm font-bold text-[#166534] mb-1.5">98% Paham</p>
                <div class="w-full h-1.5 bg-[#f3f4f6] rounded-full overflow-hidden">
                  <div class="h-full bg-[#22C55E] w-[95%] rounded-full"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-24 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 text-center mb-16">
        <h2 class="text-3xl sm:text-4xl mb-4">Satu Platform, Multi Manfaat</h2>
        <p class="text-lg max-w-3xl mx-auto">Dirancang khusus untuk memenuhi kebutuhan ekosistem BiMBA AIUEO, menghubungkan semua pihak dalam satu alur kerja yang harmonis.</p>
      </div>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 grid md:grid-cols-3 gap-8">
        <div class="bg-[#FFF7ED] p-8 rounded-2xl soft-shadow flex flex-col h-full">
          <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center soft-shadow mb-8">
            <img src="{{ asset('assets/IMG_8.svg') }}" class="w-6 h-6" alt="Admin">
          </div>
          <h3 class="text-xl mb-4">Panel Admin</h3>
          <p class="text-sm opacity-80">Kelola data murid, guru, dan kelas dengan kendali penuh. Sistem basis data yang aman memudahkan administrasi sekolah secara menyeluruh.</p>
        </div>
        <div class="bg-[#EDFDF1] p-8 rounded-2xl soft-shadow flex flex-col h-full">
          <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center soft-shadow mb-8">
            <iconify-icon icon="lucide:graduation-cap" width="22" class="text-[#22C55E]"></iconify-icon>
          </div>
          <h3 class="text-xl mb-4">Dashboard Guru</h3>
          <p class="text-sm opacity-80">Input nilai progres harian dengan metode penilaian unik BiMBA (K, B, P, T) secara cepat dan akurat melalui antarmuka yang intuitif.</p>
        </div>
        <div class="bg-[#EFF6FF] p-8 rounded-2xl soft-shadow flex flex-col h-full">
          <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center soft-shadow mb-8">
            <img src="{{ asset('assets/IMG_7.svg') }}" class="w-6 h-6" alt="Parent">
          </div>
          <h3 class="text-xl mb-4">Portal Wali Murid</h3>
          <p class="text-sm opacity-80">Pantau grafik perkembangan belajar anak secara langsung. Dapatkan notifikasi pencapaian dan cetak rapor digital kapan saja.</p>
        </div>
      </div>
    </section>

    <!-- Why Choose Section -->
    <section class="py-24 bg-[#F8FAFC]">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 grid lg:grid-cols-2 gap-16 items-center">
        <div>
          <h2 class="text-3xl sm:text-4xl mb-6">Mengapa Memilih E-Rapor Digital?</h2>
          <p class="text-lg mb-10">Kami menggabungkan kemudahan teknologi dengan metodologi pendidikan yang teruji untuk memberikan pengalaman terbaik bagi masa depan buah hati Anda.</p>
          <div class="grid sm:grid-cols-2 gap-8">
            <div class="flex gap-4">
              <div class="flex-shrink-0 w-9 h-9 bg-white rounded-xl flex items-center justify-center soft-shadow">
                <img src="{{ asset('assets/IMG_9.svg') }}" class="w-5 h-5" alt="Zap">
              </div>
              <div>
                <h4 class="text-base font-bold mb-1">Akses Instan</h4>
                <p class="text-sm">Informasi tersedia 24/7 di mana saja.</p>
              </div>
            </div>
            <div class="flex gap-4">
              <div class="flex-shrink-0 w-9 h-9 bg-white rounded-xl flex items-center justify-center soft-shadow">
                <img src="{{ asset('assets/IMG_10.svg') }}" class="w-5 h-5" alt="Database">
              </div>
              <div>
                <h4 class="text-base font-bold mb-1">Data Terpusat</h4>
                <p class="text-sm">Satu database untuk seluruh cabang.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
          <div class="bg-white p-6 rounded-2xl border border-[#dee1e6] text-center flex flex-col items-center">
            <div class="w-16 h-16 bg-[#F97316]/10 rounded-2xl flex items-center justify-center mb-6">
              <img src="{{ asset('assets/IMG_3.svg') }}" class="w-8 h-8" alt="Easy">
            </div>
            <h4 class="text-lg font-semibold mb-2">Mudah</h4>
            <p class="text-sm">Antarmuka ramah pengguna untuk semua kalangan.</p>
          </div>
          <div class="bg-white p-6 rounded-2xl border border-[#dee1e6] text-center flex flex-col items-center">
            <div class="w-16 h-16 bg-[#DBEAFE] rounded-2xl flex items-center justify-center mb-6">
              <img src="{{ asset('assets/IMG_5.svg') }}" class="w-8 h-8" alt="Safe">
            </div>
            <h4 class="text-lg font-semibold mb-2">Aman</h4>
            <p class="text-sm">Keamanan data terjamin dengan enkripsi modern.</p>
          </div>
          <div class="bg-white p-6 rounded-2xl border border-[#dee1e6] text-center flex flex-col items-center">
            <div class="w-16 h-16 bg-[#DCFCE7] rounded-2xl flex items-center justify-center mb-6">
              <img src="{{ asset('assets/IMG_9.svg') }}" class="w-8 h-8" alt="Efficient">
            </div>
            <h4 class="text-lg font-semibold mb-2">Efisien</h4>
            <p class="text-sm">Hemat waktu dan kertas dengan rapor digital.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
        <div class="cta-gradient rounded-[40px] p-12 lg:p-24 text-center relative overflow-hidden shadow-2xl">
          <div class="absolute -top-32 -left-32 w-64 h-64 bg-white/10 rounded-full blur-[64px]"></div>
          <div class="absolute top-36 -right-48 w-96 h-96 bg-black/5 rounded-full blur-[64px]"></div>
          <div class="relative z-10 max-w-3xl mx-auto">
            <h2 class="text-white text-4xl sm:text-5xl mb-8">{{ $landingCtaTitle }}</h2>
            <p class="text-white/80 text-lg mb-12">{{ $landingCtaDescription }}</p>
            <div class="flex flex-wrap justify-center gap-4">
              <a href="{{ route('login') }}" class="px-12 py-4 bg-[#f3f4f6] text-[#1e2128] font-bold rounded-full shadow-lg hover:bg-white text-lg">Masuk Sekarang</a>
              <a href="{{ route('login') }}" class="px-12 py-4 bg-transparent border border-white text-white font-bold rounded-full hover:bg-white/10 text-lg">Lihat Demo</a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="bg-[#fafafb] pt-20 pb-10 border-t border-[#dee1e6]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
        <div class="lg:col-span-2">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-9 h-9 logo-gradient rounded-full flex items-center justify-center">
              <img src="{{ asset('assets/IMG_1.svg') }}" class="w-6 h-6" alt="Logo">
            </div>
            <span class="font-bold text-2xl font-['Inter'] text-[#171a1f]">{{ $landingTitle }} {{ $landingHighlight }}</span>
          </div>
          <p class="max-w-sm text-sm">{{ $landingDescription }}</p>
        </div>
        <div>
          <h4 class="text-base font-bold mb-6">Fitur Utama</h4>
          <ul class="space-y-3 text-sm text-[#565d6d]">
            <li>Monitoring Real-time</li>
            <li>Penilaian K, B, P, T</li>
            <li>Dashboard Grafik</li>
            <li>Cetak Rapor Digital</li>
          </ul>
        </div>
        <div>
          <h4 class="text-base font-bold mb-6">Hubungi Kami</h4>
          <ul class="space-y-3 text-sm text-[#565d6d]">
            <li><a href="mailto:{{ $supportEmail }}" class="hover:text-[#F97316]">{{ $supportEmail }}</a></li>
            <li><a href="{{ $supportWhatsappUrl }}" target="_blank" class="hover:text-[#F97316]">Support Center (WhatsApp)</a></li>
            <li><a href="{{ $supportWhatsappUrl }}" target="_blank" class="hover:text-[#F97316]">{{ $supportWhatsapp }}</a></li>
          </ul>
        </div>
      </div>
      <div class="pt-8 border-t border-[#dee1e6] text-center">
        <p class="text-sm">© {{ date('Y') }} {{ $institutionName }}. Modern child learning solutions.</p>
      </div>
    </div>
  </footer>

</body>
</html>
