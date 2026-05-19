@extends('layouts.admin')

@section('title', 'Dashboard - E-Rapor BiMBA AIUEO')
@section('page-title', 'Dashboard')

@section('content')
<!-- Welcome Section -->
<section class="mb-8 rounded-2xl p-6 border border-[#dee1e6]" style="background: linear-gradient(135deg, rgba(249,115,22,0.10) 0%, rgba(59,130,246,0.08) 50%, rgba(34,197,94,0.08) 100%);">
  <h1 class="text-2xl lg:text-3xl font-bold text-[#171a1f] tracking-tight">Ringkasan Dashboard</h1>
  <p class="text-[#565d6d] mt-2 font-roboto">Selamat datang kembali, {{ auth()->user()->name }}. Pantau statistik institusi Anda hari ini.</p>
</section>

<!-- Stats Grid -->
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
  <div class="bg-white p-6 rounded-xl main-shadow border border-gray-50">
    <div class="flex justify-between items-start mb-6">
      <div class="w-10 h-10 bg-[#F97316]/10 rounded-lg flex items-center justify-center">
        <iconify-icon icon="lucide:users" width="22" class="text-[#F97316]"></iconify-icon>
      </div>
      <div class="flex items-center gap-1 text-[#C2410C] text-xs font-medium font-roboto">
        <iconify-icon icon="lucide:user-plus" width="12"></iconify-icon>
        {{ $stats['murid_baru'] }} baru
      </div>
    </div>
    <p class="text-sm font-medium text-[#565d6d] mb-1">Total Murid</p>
    <h3 class="text-3xl font-bold text-[#171a1f] mb-2">{{ number_format($stats['total_murid']) }}</h3>
    <p class="text-xs text-[#565d6d] font-roboto">+{{ $stats['murid_baru'] }} murid baru bulan ini</p>
  </div>

  <div class="bg-white p-6 rounded-xl main-shadow border border-gray-50">
    <div class="flex justify-between items-start mb-6">
      <div class="w-10 h-10 bg-[#3B82F6]/10 rounded-lg flex items-center justify-center">
        <iconify-icon icon="lucide:user-check" width="22" class="text-[#3B82F6]"></iconify-icon>
      </div>
    </div>
    <p class="text-sm font-medium text-[#565d6d] mb-1">Total Guru</p>
    <h3 class="text-3xl font-bold text-[#171a1f] mb-2">{{ $stats['total_guru'] }}</h3>
    <p class="text-xs text-[#565d6d] font-roboto">Guru terdaftar di sistem</p>
  </div>

  <div class="bg-white p-6 rounded-xl main-shadow border border-gray-50">
    <div class="flex justify-between items-start mb-6">
      <div class="w-10 h-10 bg-[#22C55E]/10 rounded-lg flex items-center justify-center">
        <iconify-icon icon="lucide:school" width="22" class="text-[#22C55E]"></iconify-icon>
      </div>
    </div>
    <p class="text-sm font-medium text-[#565d6d] mb-1">Total Kelas</p>
    <h3 class="text-3xl font-bold text-[#171a1f] mb-2">{{ $stats['total_kelas'] }}</h3>
    <p class="text-xs text-[#565d6d] font-roboto">Kelas aktif dalam sistem</p>
  </div>
</section>

<!-- Bottom Grid: Aktivitas & Bantuan -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

  <!-- Aktivitas Terkini -->
  <div class="lg:col-span-2 bg-white p-6 rounded-2xl main-shadow border border-gray-50">
    <h2 class="text-lg font-bold text-[#171a1f] mb-1">Aktivitas Terkini</h2>
    <p class="text-sm text-[#565d6d] font-roboto mb-6">Log aktivitas dalam 24 jam terakhir.</p>
    <div class="space-y-6 relative">
      <div class="absolute left-[5px] top-2 bottom-2 w-[1px] bg-[#dee1e6]"></div>
      @forelse($recentActivities as $activity)
        @php
          $activityColor = match($activity->type) {
            'success' => '#22C55E',
            'warning' => '#F97316',
            'error' => '#DC2626',
            default => '#3B82F6',
          };
        @endphp
        <a href="{{ $activity->link ?: '#' }}" class="relative pl-8 block hover:opacity-80">
          <div class="absolute left-0 top-1.5 w-3 h-3 bg-white border-2 rounded-full z-10" style="border-color: {{ $activityColor }}"></div>
          <p class="text-sm font-medium text-[#171a1f] font-roboto">{{ $activity->title }}</p>
          <p class="text-xs text-[#565d6d] font-roboto mt-1">{{ $activity->message }}</p>
          <p class="text-[10px] text-[#9095a0] font-roboto mt-1">{{ $activity->created_at?->diffForHumans() }}</p>
        </a>
      @empty
        <div class="relative pl-8">
          <div class="absolute left-0 top-1.5 w-3 h-3 bg-white border-2 border-[#dee1e6] rounded-full z-10"></div>
          <p class="text-sm font-medium text-[#171a1f] font-roboto">Belum ada aktivitas</p>
          <p class="text-xs text-[#565d6d] font-roboto mt-1">Aktivitas admin akan muncul setelah ada perubahan data.</p>
        </div>
      @endforelse
    </div>
  </div>

  <!-- Right Column -->
  <div class="space-y-8">
    <!-- Pusat Bantuan -->
    <div class="bg-[#F97316] p-6 rounded-2xl text-white relative overflow-hidden shadow-[0px_4px_7px_0px_rgba(249,115,22,0.2)]">
      <div class="absolute -right-4 -top-4 opacity-10 rotate-12">
        <iconify-icon icon="lucide:graduation-cap" width="112" class="text-white"></iconify-icon>
      </div>
      <h2 class="text-lg font-bold mb-2 relative z-10 text-white" style="color: white;">Pusat Bantuan BiMBA</h2>
      <p class="text-sm text-white/80 font-roboto mb-6 relative z-10 leading-relaxed">Butuh bantuan dalam mengelola data atau sistem rapor? Tim teknis kami siap membantu 24/7.</p>
      <a href="{{ $supportWhatsappUrl ?? '#' }}" target="_blank" class="w-full py-2.5 bg-white text-[#1e2128] rounded-lg text-sm font-semibold hover:bg-gray-100 relative z-10 block text-center">
        <iconify-icon icon="lucide:message-circle" width="16" class="inline mr-1"></iconify-icon>
        Hubungi Support via WhatsApp
      </a>
    </div>
  </div>
</div>
@endsection
