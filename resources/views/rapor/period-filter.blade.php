<form method="GET" action="{{ $action }}" class="no-print max-w-[850px] mx-auto bg-white border border-[#dee1e6] rounded-xl p-4 mb-6 flex flex-wrap items-end gap-3">
    <input type="hidden" name="student_id" value="{{ $student->id }}">
    <div class="flex-1 min-w-0">
        <label for="rapor-period" class="block text-sm font-semibold mb-2">Periode Rapor (3 Bulan)</label>
        <select id="rapor-period" name="period_number" class="w-full border border-[#dee1e6] rounded-lg px-3 py-2 text-sm bg-white">
            @foreach($periodOptions as $option)
                <option value="{{ $option['number'] }}" @selected($option['number'] === $attendanceReport['period']['number'])>
                    Periode {{ $option['number'] }}: {{ $option['start']->translatedFormat('d M Y') }} - {{ $option['end']->translatedFormat('d M Y') }}
                </option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="px-4 py-2 rounded-lg bg-[#F97316] text-white text-sm font-semibold">Tampilkan Periode</button>
</form>
