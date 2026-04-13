@extends('layouts.admin')

@section('title', 'Pengaturan Sistem - E-Rapor BiMBA')
@section('page-title', 'Pengaturan Sistem')

@section('content')
<!-- Page Title -->
<div class="mb-10">
  <h1 class="text-2xl lg:text-3xl font-bold tracking-tight">Pengaturan Sistem</h1>
  <p class="text-[#565d6d] mt-1 font-roboto">Konfigurasi identitas institusi, parameter penilaian, dan kontrol keamanan akses.</p>
</div>

<div class="max-w-5xl space-y-8">

  <!-- Section: Pengaturan Akun -->
  <form method="POST" action="{{ route('admin.pengaturan.update') }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <section class="bg-white rounded-xl border border-[#dee1e6] overflow-hidden main-shadow">
      <div class="bg-[#F1F6FE]/30 p-6 border-b border-[#dee1e6] flex items-start gap-4">
        <div class="w-9 h-9 bg-[#3d8af5]/20 rounded-lg flex items-center justify-center flex-shrink-0">
          <iconify-icon icon="lucide:building-2" width="18" class="text-[#3d8af5]"></iconify-icon>
        </div>
        <div>
          <h2 class="text-lg font-bold text-[#171a1f]">Pengaturan Akun</h2>
          <p class="text-sm text-[#565d6d]">Kelola informasi dasar institusi dan branding BiMBA AIUEO.</p>
        </div>
      </div>
      <div class="p-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="space-y-6">
          <div>
            <label class="block text-sm font-bold text-[#171a1f] mb-2">Nama Institusi</label>
            <input type="text" name="institution_name" value="{{ $settings['institution_name'] }}" class="w-full px-4 py-2.5 border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20">
          </div>
          <div>
            <label class="block text-sm font-bold text-[#171a1f] mb-2">Alamat Lengkap</label>
            <textarea name="institution_address" rows="4" class="w-full px-4 py-2.5 border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20 resize-none">{{ $settings['institution_address'] }}</textarea>
          </div>
        </div>
        <div x-data="{ preview: '{{ $settings['institution_logo'] ? asset('storage/' . $settings['institution_logo']) : '' }}' }">
          <label class="block text-sm font-bold text-[#171a1f] mb-2">Logo Institusi</label>
          <label class="border-2 border-dashed border-[#3d8af5]/30 rounded-xl bg-[#3d8af5]/5 h-[220px] flex flex-col items-center justify-center p-6 text-center cursor-pointer hover:bg-[#3d8af5]/10 transition-colors">
            <input type="file" name="institution_logo" accept="image/png,image/jpeg" class="hidden" @change="if($event.target.files[0]) preview = URL.createObjectURL($event.target.files[0])">
            <template x-if="preview">
              <img :src="preview" class="max-h-[160px] max-w-full object-contain rounded-lg">
            </template>
            <template x-if="!preview">
              <div class="flex flex-col items-center">
                <div class="w-16 h-16 bg-white rounded-full shadow-sm flex items-center justify-center mb-4">
                  <iconify-icon icon="lucide:upload-cloud" width="28" class="text-[#3d8af5]"></iconify-icon>
                </div>
                <p class="text-sm font-medium text-[#3d8af5] mb-1">Klik atau seret logo ke sini</p>
                <p class="text-xs text-[#565d6d]">PNG, JPG up to 2MB (Rekomendasi 512x512px)</p>
              </div>
            </template>
          </label>
        </div>
      </div>
      <div class="px-8 pb-6 flex justify-end">
        <button type="submit" class="flex items-center gap-2 px-6 py-2.5 text-sm font-bold text-white bg-[#3d8af5] hover:bg-blue-600 rounded-xl shadow-lg shadow-blue-200">
          <iconify-icon icon="lucide:save" width="16"></iconify-icon>
          Simpan Pengaturan Akun
        </button>
      </div>
    </section>
  </form>

  <!-- Section: Skala Penilaian -->
  <section class="bg-white rounded-xl border border-[#dee1e6] overflow-hidden main-shadow">
    <div class="bg-[#F1F6FE]/30 p-6 border-b border-[#dee1e6] flex items-start gap-4">
      <div class="w-9 h-9 bg-[#3d8af5]/20 rounded-lg flex items-center justify-center flex-shrink-0">
        <iconify-icon icon="lucide:sliders-horizontal" width="18" class="text-[#3d8af5]"></iconify-icon>
      </div>
      <div>
        <h2 class="text-lg font-bold text-[#171a1f]">Pengaturan Sistem & Penilaian</h2>
        <p class="text-sm text-[#565d6d]">Konfigurasi parameter akademik default untuk evaluasi pembelajaran murid.</p>
      </div>
    </div>
    <div class="p-8">
      <div class="flex items-center gap-2 mb-6">
        <h3 class="text-base font-bold text-[#171a1f]">Skala Penilaian Default (K/B/P/T)</h3>
        <iconify-icon icon="lucide:info" width="16" class="text-[#565d6d]"></iconify-icon>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-[#D92626]/10 p-5 rounded-2xl text-center">
          <div class="w-10 h-10 bg-[#D92626] text-white rounded-full flex items-center justify-center mx-auto mb-3 font-bold text-lg">K</div>
          <p class="text-xs font-bold text-[#D92626] mb-1">Kenal</p>
          <p class="text-[10px] text-[#565d6d]">Belum menunjukkan minat belajar.</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-[#dee1e6] text-center">
          <div class="w-10 h-10 border border-[#dee1e6] text-[#171a1f] rounded-full flex items-center justify-center mx-auto mb-3 font-bold text-lg">B</div>
          <p class="text-xs font-bold text-[#171a1f] mb-1">Belum</p>
          <p class="text-[10px] text-[#565d6d]">Mulai menunjukkan minat belajar.</p>
        </div>
        <div class="bg-[#3d8af5]/10 p-5 rounded-2xl text-center">
          <div class="w-10 h-10 bg-[#3d8af5] text-white rounded-full flex items-center justify-center mx-auto mb-3 font-bold text-lg">P</div>
          <p class="text-xs font-bold text-[#3d8af5] mb-1">Paham</p>
          <p class="text-[10px] text-[#565d6d]">Sering menunjukkan minat belajar.</p>
        </div>
        <div class="bg-[#63e98f]/10 p-5 rounded-2xl text-center">
          <div class="w-10 h-10 bg-[#63e98f] text-[#171a1f] rounded-full flex items-center justify-center mx-auto mb-3 font-bold text-lg">T</div>
          <p class="text-xs font-bold text-[#171a1f] mb-1">Terampil</p>
          <p class="text-[10px] text-[#565d6d]">Konsisten & mampu membimbing teman.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Section: Manajemen Role & Izin -->
  <section class="bg-white rounded-xl border border-[#dee1e6] overflow-hidden main-shadow">
    <div class="bg-[#F1F6FE]/30 p-6 border-b border-[#dee1e6] flex items-start gap-4">
      <div class="w-9 h-9 bg-[#3d8af5]/20 rounded-lg flex items-center justify-center flex-shrink-0">
        <iconify-icon icon="lucide:shield-check" width="18" class="text-[#3d8af5]"></iconify-icon>
      </div>
      <div>
        <h2 class="text-lg font-bold text-[#171a1f]">Manajemen Role & Izin</h2>
        <p class="text-sm text-[#565d6d]">Atur tingkat akses fungsional untuk setiap peran pengguna di dalam sistem.</p>
      </div>
    </div>
    <div class="p-8 space-y-4">
      <!-- Administrator Role -->
      <div class="border border-[#dee1e6] rounded-2xl overflow-hidden" x-data="{ open: true }">
        <div @click="open = !open" class="p-4 flex items-center justify-between bg-white cursor-pointer hover:bg-gray-50">
          <div class="flex items-center gap-4">
            <span class="px-3 py-1 bg-[#3d8af5]/20 text-[#3d8af5] text-xs font-bold rounded-full">Role: Administrator</span>
            <span class="text-sm font-medium text-[#565d6d]">Akses penuh ke semua modul sistem</span>
          </div>
          <iconify-icon :icon="open ? 'lucide:chevron-up' : 'lucide:chevron-down'" width="16" class="text-[#565d6d]"></iconify-icon>
        </div>
        <div x-show="open" x-collapse class="px-6 pb-6 space-y-4 border-t border-[#dee1e6]">
          <div class="pt-4 flex items-center justify-between">
            <div>
              <p class="text-sm font-bold text-[#171a1f]">Kelola Data Master</p>
              <p class="text-xs text-[#565d6d]">Menambah, mengedit, dan menghapus data murid & guru.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" checked disabled class="sr-only peer">
              <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#3d8af5]"></div>
            </label>
          </div>
          <div class="border-t border-[#dee1e6] pt-4 flex items-center justify-between">
            <div>
              <p class="text-sm font-bold text-[#171a1f]">Konfigurasi Sistem</p>
              <p class="text-xs text-[#565d6d]">Mengakses halaman pengaturan dan keamanan.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" checked disabled class="sr-only peer">
              <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#3d8af5]"></div>
            </label>
          </div>
          <div class="border-t border-[#dee1e6] pt-4 flex items-center justify-between">
            <div>
              <p class="text-sm font-bold text-[#171a1f]">Export Data</p>
              <p class="text-xs text-[#565d6d]">Melakukan ekspor laporan ke format PDF/CSV.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" checked disabled class="sr-only peer">
              <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#3d8af5]"></div>
            </label>
          </div>
        </div>
      </div>

      <!-- Motivator/Guru Role -->
      <div class="border border-[#dee1e6] rounded-2xl overflow-hidden" x-data="{ open: false }">
        <div @click="open = !open" class="p-4 flex items-center justify-between cursor-pointer hover:bg-gray-50">
          <div class="flex items-center gap-4">
            <span class="px-3 py-1 bg-[#63e98f]/20 text-[#171a1f] text-xs font-bold rounded-full">Role: Motivator/Guru</span>
            <span class="text-sm font-medium text-[#565d6d]">Akses input nilai & kelas</span>
          </div>
          <iconify-icon :icon="open ? 'lucide:chevron-up' : 'lucide:chevron-down'" width="16" class="text-[#565d6d]"></iconify-icon>
        </div>
        <div x-show="open" x-collapse class="px-6 pb-6 space-y-4 border-t border-[#dee1e6]">
          <div class="pt-4 flex items-center justify-between">
            <div>
              <p class="text-sm font-bold text-[#171a1f]">Input Nilai Murid</p>
              <p class="text-xs text-[#565d6d]">Menginput dan memperbarui progress nilai pembelajaran murid.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" checked disabled class="sr-only peer">
              <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#3d8af5]"></div>
            </label>
          </div>
          <div class="border-t border-[#dee1e6] pt-4 flex items-center justify-between">
            <div>
              <p class="text-sm font-bold text-[#171a1f]">Lihat Grafik Perkembangan</p>
              <p class="text-xs text-[#565d6d]">Melihat grafik perkembangan murid di kelasnya.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" checked disabled class="sr-only peer">
              <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#3d8af5]"></div>
            </label>
          </div>
          <div class="border-t border-[#dee1e6] pt-4 flex items-center justify-between">
            <div>
              <p class="text-sm font-bold text-[#171a1f]">Generate Rapor</p>
              <p class="text-xs text-[#565d6d]">Membuat laporan rapor murid untuk wali murid.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" checked disabled class="sr-only peer">
              <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#3d8af5]"></div>
            </label>
          </div>
        </div>
      </div>

      <!-- Wali Murid Role -->
      <div class="border border-[#dee1e6] rounded-2xl overflow-hidden" x-data="{ open: false }">
        <div @click="open = !open" class="p-4 flex items-center justify-between cursor-pointer hover:bg-gray-50">
          <div class="flex items-center gap-4">
            <span class="px-3 py-1 border border-[#3d8af5]/30 text-[#3d8af5] text-xs font-bold rounded-full">Role: Wali Murid</span>
            <span class="text-sm font-medium text-[#565d6d]">Hanya akses baca (Read-only)</span>
          </div>
          <iconify-icon :icon="open ? 'lucide:chevron-up' : 'lucide:chevron-down'" width="16" class="text-[#565d6d]"></iconify-icon>
        </div>
        <div x-show="open" x-collapse class="px-6 pb-6 space-y-4 border-t border-[#dee1e6]">
          <div class="pt-4 flex items-center justify-between">
            <div>
              <p class="text-sm font-bold text-[#171a1f]">Lihat Dashboard</p>
              <p class="text-xs text-[#565d6d]">Melihat ringkasan perkembangan anak.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" checked disabled class="sr-only peer">
              <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#3d8af5]"></div>
            </label>
          </div>
          <div class="border-t border-[#dee1e6] pt-4 flex items-center justify-between">
            <div>
              <p class="text-sm font-bold text-[#171a1f]">Lihat Rapor Anak</p>
              <p class="text-xs text-[#565d6d]">Mengakses dan mengunduh laporan rapor anak.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" checked disabled class="sr-only peer">
              <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#3d8af5]"></div>
            </label>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Section: Keamanan & Akses -->
  <form method="POST" action="{{ route('admin.pengaturan.password') }}">
    @csrf @method('PUT')
    <section class="bg-white rounded-xl border border-[#dee1e6] overflow-hidden main-shadow">
      <div class="bg-[#F1F6FE]/30 p-6 border-b border-[#dee1e6] flex items-start gap-4">
        <div class="w-9 h-9 bg-[#3d8af5]/20 rounded-lg flex items-center justify-center flex-shrink-0">
          <iconify-icon icon="lucide:lock" width="18" class="text-[#3d8af5]"></iconify-icon>
        </div>
        <div>
          <h2 class="text-lg font-bold text-[#171a1f]">Keamanan & Akses</h2>
          <p class="text-sm text-[#565d6d]">Lindungi data institusi dengan kontrol keamanan akun tingkat tinggi.</p>
        </div>
      </div>
      <div class="p-8 space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="lg:col-span-2">
            <label class="block text-sm font-bold text-[#171a1f] mb-2">Kata Sandi Saat Ini</label>
            <input type="password" name="current_password" placeholder="••••••••" required class="w-full px-4 py-2.5 border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20">
          </div>
          <div>
            <label class="block text-sm font-bold text-[#171a1f] mb-2">Kata Sandi Baru</label>
            <input type="password" name="password" placeholder="••••••••" required class="w-full px-4 py-2.5 border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20">
          </div>
          <div>
            <label class="block text-sm font-bold text-[#171a1f] mb-2">Konfirmasi Kata Sandi Baru</label>
            <input type="password" name="password_confirmation" placeholder="••••••••" required class="w-full px-4 py-2.5 border border-[#dee1e6] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#3d8af5]/20">
          </div>
        </div>
        <!-- 2FA Toggle -->
        <div class="bg-[#F1F6FE]/20 border border-[#3d8af5]/20 rounded-2xl p-6 flex items-center gap-6">
          <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center flex-shrink-0">
            <iconify-icon icon="lucide:shield" width="22" class="text-[#3d8af5]"></iconify-icon>
          </div>
          <div class="flex-1">
            <h3 class="text-sm font-bold text-[#171a1f] mb-1">Autentikasi Dua Faktor (2FA)</h3>
            <p class="text-xs text-[#565d6d] max-w-lg">Tambahkan lapisan keamanan ekstra ke akun Anda. Kode verifikasi akan diminta setiap kali Anda login melalui perangkat baru.</p>
          </div>
          <div class="flex items-center gap-3 flex-shrink-0">
            <span class="text-[10px] font-bold text-[#565d6d] uppercase">Nonaktif</span>
            <label class="relative inline-flex items-center cursor-pointer" x-data="{ enabled: false }">
              <input type="checkbox" x-model="enabled" class="sr-only peer">
              <div class="w-11 h-6 bg-[#bdc1ca] rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#3d8af5]"></div>
            </label>
            <span class="text-[10px] font-bold text-[#3d8af5] uppercase">Aktif</span>
          </div>
        </div>
      </div>
      <div class="px-8 pb-6 flex justify-end">
        <button type="submit" class="flex items-center gap-2 px-6 py-2.5 text-sm font-bold text-white bg-[#3d8af5] hover:bg-blue-600 rounded-xl shadow-lg shadow-blue-200">
          <iconify-icon icon="lucide:save" width="16"></iconify-icon>
          Perbarui Kata Sandi
        </button>
      </div>
    </section>
  </form>

  <!-- Warning Banner -->
  <div class="border border-[#dee1e6] rounded-2xl p-4 flex items-start gap-3">
    <iconify-icon icon="lucide:alert-triangle" width="18" class="text-amber-500 mt-0.5 flex-shrink-0"></iconify-icon>
    <p class="text-sm font-medium text-[#171a1f]">Pastikan semua perubahan telah ditinjau sebelum disimpan. Beberapa pengaturan sistem akan langsung berdampak pada seluruh pengguna.</p>
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
@endsection
