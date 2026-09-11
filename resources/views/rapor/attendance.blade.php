<style>
    .attendance-table-scroll { overflow-x: auto; }
    .attendance-table-scroll .rapor-table { min-width: 560px; }
    .attendance-table-scroll .rapor-table th, .attendance-table-scroll .rapor-table td { padding: 6px 8px; }
    @media print {
        .attendance-table-scroll { overflow: visible; }
        .attendance-table-scroll .rapor-table { min-width: 0; }
    }
</style>
<section id="hasil-absensi" style="margin: 18px 0 24px; scroll-margin-top: 96px; page-break-inside: avoid; break-inside: avoid; font-size: 13px;">
    <h3 style="font-size: 14px; font-weight: bold; margin-bottom: 6px;">Hasil Absensi per Bulan</h3>
    <p style="font-size: 12px; margin-bottom: 8px;">
        Periode {{ $attendanceReport['period']['number'] }} (3 bulan):
        {{ $attendanceReport['period']['start']->translatedFormat('d M Y') }} - {{ $attendanceReport['period']['end']->translatedFormat('d M Y') }}.
        @if($attendanceReport['period']['cutoff']->lt($attendanceReport['period']['end']))
            Data sampai {{ $attendanceReport['period']['cutoff']->translatedFormat('d M Y') }}; periode belum selesai.
        @endif
    </p>
    <div class="attendance-table-scroll" tabindex="0" role="region" aria-label="Rekap absensi per bulan, geser tabel jika diperlukan">
    <table class="rapor-table" style="width: 100%; table-layout: fixed;">
        <thead>
            <tr>
                <th style="width: 40%;">Bulan / Tanggal</th>
                @foreach(\App\Models\Attendance::STATUS_LABELS as $label)
                    <th>{{ $label }}</th>
                @endforeach
                <th>Total Tercatat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendanceReport['months'] as $month)
                <tr>
                    <td>
                        {{ $month['label'] }}
                        <span style="display: block; font-size: 11px; color: #475569;">{{ $month['start']->translatedFormat('d M') }} - {{ $month['end']->translatedFormat('d M') }}</span>
                    </td>
                    @foreach(['hadir', 'sakit', 'izin', 'alpa', 'total'] as $key)
                        <td style="text-align: center;">{{ $month['future'] ? '-' : $month['summary'][$key] }}</td>
                    @endforeach
                </tr>
            @endforeach
            <tr class="group-header">
                <td>Total Periode 3 Bulan</td>
                @foreach(['hadir', 'sakit', 'izin', 'alpa', 'total'] as $key)
                    <td style="text-align: center;">{{ $attendanceReport['summary'][$key] }}</td>
                @endforeach
            </tr>
        </tbody>
    </table>
    </div>
    <p style="font-size: 11px; color: #475569; margin-top: 6px;">
        Total tercatat = Hadir + Sakit + Izin + Alpa. Tanggal yang belum dicatat tidak dihitung sebagai Alpa. Tanda - berarti bulan belum berjalan.
        @if($attendanceReport['summary']['total'] === 0)
            Belum ada absensi tercatat pada periode ini.
        @endif
    </p>
</section>
