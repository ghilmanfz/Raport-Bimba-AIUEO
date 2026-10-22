<div class="grid grid-cols-1 md:grid-cols-3 gap-6" data-skill-summary>
  @foreach(['baca' => ['Membaca', 'lucide:book-open', 'text-orange-600'], 'tulis' => ['Menulis', 'lucide:pencil', 'text-blue-600'], 'hitung' => ['Berhitung', 'lucide:calculator', 'text-green-700']] as $skillKey => [$label, $icon, $color])
    <div class="min-w-0 space-y-4" data-skill="{{ $skillKey }}">
      @forelse($skillReport[$skillKey]['levels'] as $level)
        <div class="flex items-start gap-3">
          <iconify-icon icon="{{ $icon }}" width="20" class="{{ $color }} shrink-0 mt-0.5"></iconify-icon>
          <div class="min-w-0">
            <h3 class="text-sm font-semibold text-[#171a1f] break-words">{{ $label }} ({{ $level['level'] }})</h3>
            <p class="text-sm text-[#565d6d] mt-1"><strong class="text-[#171a1f]">{{ $level['skilled'] }}/{{ $level['total'] }}</strong> materi terampil</p>
          </div>
        </div>
      @empty
        <div class="flex items-start gap-3">
          <iconify-icon icon="{{ $icon }}" width="20" class="{{ $color }} shrink-0 mt-0.5"></iconify-icon>
          <div class="min-w-0">
            <h3 class="text-sm font-semibold text-[#171a1f]">{{ $label }}</h3>
            <p class="text-sm text-[#565d6d] mt-1">Belum ada materi yang dinilai.</p>
          </div>
        </div>
      @endforelse
    </div>
  @endforeach
</div>
