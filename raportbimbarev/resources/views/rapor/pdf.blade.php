<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rapor {{ $student->name }} - {{ $institutionName }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 13px; color: #1e293b; }
        .page { padding: 40px; }
        .text-center { text-align: center; }
        .font-bold { font-weight: 700; }
        .uppercase { text-transform: uppercase; }

        h1.title { font-size: 18px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 4px; }
        .title-line { width: 80px; height: 3px; background: #1e293b; margin: 0 auto 20px; }

        .institution { font-size: 16px; font-weight: 700; text-transform: uppercase; margin-bottom: 2px; }
        .address { font-size: 12px; color: #475569; margin-bottom: 20px; }

        /* Identity Table */
        .identity-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .identity-table td { padding: 3px 8px; font-size: 13px; border: none; vertical-align: top; }

        /* Main Table */
        .rapor-table { border-collapse: collapse; width: 100%; margin-bottom: 10px; }
        .rapor-table th, .rapor-table td { border: 1.5px solid #1e293b; padding: 6px 10px; font-size: 12px; }
        .rapor-table th { background: #f1f5f9; font-weight: 700; text-align: center; }
        .group-header td { background: #e2e8f0; font-weight: 700; font-size: 12px; }
        .status-T { background: #FEE2E2; color: #991B1B; font-weight: 700; }
        .status-P { background: #dbeafe; color: #1e40af; font-weight: 700; }
        .status-B { background: #f1f5f9; color: #475569; font-weight: 700; }
        .status-K { background: #f1f5f9; color: #475569; font-weight: 700; }
        .avg-row td { background: #e2e8f0; }

        /* Legend */
        .legend { font-size: 11px; color: #475569; margin: 8px 0 16px; }

        /* Notes */
        .notes { border: 1.5px solid #1e293b; border-radius: 4px; padding: 12px; margin-bottom: 20px; font-size: 12px; line-height: 1.6; }

        /* Signature */
        .signature-wrapper { width: 100%; margin-top: 30px; }
        .signature-table { width: 100%; border-collapse: collapse; }
        .signature-table td { width: 50%; text-align: center; vertical-align: top; padding: 0 20px; }
        .sign-line { width: 180px; border-bottom: 1px solid #1e293b; margin: 0 auto; }
        .qr-box { margin: 8px auto; }
        .qr-box img { width: 100px; height: 100px; }
    </style>
</head>
<body>
<div class="page">

    <!-- Report Title -->
    <div class="text-center">
        <h1 class="title">Ringkasan Laporan Hasil Belajar Murid</h1>
        <div class="title-line"></div>
    </div>

    <!-- Institution Header -->
    <div class="text-center" style="margin-bottom: 20px;">
        <div class="institution">{{ $institutionName }}</div>
        @if($institutionAddress)
            <div class="address">{{ $institutionAddress }}</div>
        @endif
    </div>

    <!-- Student Identity -->
    <table class="identity-table">
        <tr>
            <td style="width:15%;">Nama</td>
            <td style="width:2%;">:</td>
            <td style="width:33%;"><strong>{{ $student->name }}</strong></td>
            <td style="width:15%;">Lembaga</td>
            <td style="width:2%;">:</td>
            <td style="width:33%;">{{ $institutionName }}</td>
        </tr>
        <tr>
            <td>NIS</td>
            <td>:</td>
            <td><strong>{{ $student->nis }}</strong></td>
            <td>Unit</td>
            <td>:</td>
            <td>{{ $unitName ?: '-' }}</td>
        </tr>
        <tr>
            <td>Tanggal Lahir</td>
            <td>:</td>
            <td>{{ $student->birth_date?->translatedFormat('d F Y') ?? '-' }}</td>
            <td>Kelas</td>
            <td>:</td>
            <td>{{ $student->classroom?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td>
            <td>:</td>
            <td>{{ ($student->gender ?? '') == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            <td>Level</td>
            <td>:</td>
            <td>{{ $student->classroom?->level ?? '-' }}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td>Periode</td>
            <td>:</td>
            <td>{{ now()->translatedFormat('F Y') }}</td>
        </tr>
    </table>

    <!-- Main Grades Table -->
    @php
        $statusCode = fn($s) => $s === 'B' ? 'K' : $s;
        $statusLabel = fn($s) => match($s) { 'T' => 'Terampil', 'P' => 'Paham', default => 'Kenal' };
        $bacaPct   = $reportData['baca']['percentage'];
        $tulisPct  = $reportData['tulis']['percentage'];
        $hitungPct = $reportData['hitung']['percentage'];
        $avgPct    = round(($bacaPct + $tulisPct + $hitungPct) / 3);

        $skills  = ['Membaca' => $bacaPct, 'Menulis' => $tulisPct, 'Berhitung' => $hitungPct];
        $highest = array_keys($skills, max($skills))[0];
        $focus   = array_keys($skills, min($skills))[0];
    @endphp

    <table class="rapor-table">
        <thead>
            <tr>
                <th style="width:6%;">No</th>
                <th style="width:42%;">Materi Pembelajaran</th>
                <th style="width:14%;">Level</th>
                <th style="width:14%;">Status</th>
                <th style="width:24%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            {{-- MEMBACA --}}
            @php $no = 0; @endphp
            <tr class="group-header">
                <td colspan="4"><strong>Membaca</strong></td>
                <td class="text-center"><strong>{{ round($bacaPct) }}%</strong></td>
            </tr>
            @foreach($reportData['baca']['by_level']->sortKeys() as $level => $progresses)
                @foreach($progresses->sortBy('material.sort_order') as $prog)
                    @php $no++; @endphp
                    <tr>
                        <td class="text-center">{{ $no }}</td>
                        <td>{{ $prog->material->name }}</td>
                        <td class="text-center">{{ $prog->material->level }}</td>
                        <td class="text-center status-{{ $statusCode($prog->status) }}">{{ $statusCode($prog->status) }}</td>
                        <td>{{ $statusLabel($prog->status) }}</td>
                    </tr>
                @endforeach
            @endforeach

            {{-- MENULIS --}}
            @php $no = 0; @endphp
            <tr class="group-header">
                <td colspan="4"><strong>Menulis</strong></td>
                <td class="text-center"><strong>{{ round($tulisPct) }}%</strong></td>
            </tr>
            @foreach($reportData['tulis']['by_level']->sortKeys() as $level => $progresses)
                @foreach($progresses->sortBy('material.sort_order') as $prog)
                    @php $no++; @endphp
                    <tr>
                        <td class="text-center">{{ $no }}</td>
                        <td>{{ $prog->material->name }}</td>
                        <td class="text-center">{{ $prog->material->level }}</td>
                        <td class="text-center status-{{ $statusCode($prog->status) }}">{{ $statusCode($prog->status) }}</td>
                        <td>{{ $statusLabel($prog->status) }}</td>
                    </tr>
                @endforeach
            @endforeach

            {{-- BERHITUNG --}}
            @php $no = 0; @endphp
            <tr class="group-header">
                <td colspan="4"><strong>Berhitung</strong></td>
                <td class="text-center"><strong>{{ round($hitungPct) }}%</strong></td>
            </tr>
            @foreach($reportData['hitung']['by_level']->sortKeys() as $level => $progresses)
                @foreach($progresses->sortBy('material.sort_order') as $prog)
                    @php $no++; @endphp
                    <tr>
                        <td class="text-center">{{ $no }}</td>
                        <td>{{ $prog->material->name }}</td>
                        <td class="text-center">{{ $prog->material->level }}</td>
                        <td class="text-center status-{{ $statusCode($prog->status) }}">{{ $statusCode($prog->status) }}</td>
                        <td>{{ $statusLabel($prog->status) }}</td>
                    </tr>
                @endforeach
            @endforeach

            <tr class="avg-row">
                <td colspan="4" class="text-center"><strong>Rata-Rata Penguasaan</strong></td>
                <td class="text-center"><strong>{{ $avgPct }}%</strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Legend -->
    <div class="legend">
        <strong>Keterangan Status:</strong>
        K = Kenal &nbsp;|&nbsp; P = Paham &nbsp;|&nbsp; T = Terampil
    </div>

    <!-- Notes -->
    <div class="notes">
        <strong>Catatan Perkembangan:</strong><br>
        <strong>{{ $student->name }}</strong> menunjukkan perkembangan yang
        @if($avgPct >= 70) sangat positif @elseif($avgPct >= 40) cukup baik @else perlu ditingkatkan @endif
        dengan rata-rata penguasaan <strong>{{ $avgPct }}%</strong>.
        Aspek terkuat pada bidang <strong>{{ $highest }}</strong> ({{ $skills[$highest] }}%)
        dan fokus pengembangan pada bidang <strong>{{ $focus }}</strong> ({{ $skills[$focus] }}%).
        @if($student->development_notes)
        <br><br><strong>Catatan Guru:</strong> {{ $student->development_notes }}
        @endif
    </div>

    <!-- Signature Section with QR Codes -->
    <table class="signature-table">
        <tr>
            <td>
                <p>Mengetahui,</p>
                <p>Orang Tua / Wali Murid</p>
                <div class="qr-box">
                    <img src="data:image/svg+xml;base64,{{ $qrCodeBase64 }}" alt="QR Code">
                </div>
                <div class="sign-line"></div>
                @if($student->parent)
                    <p style="margin-top: 4px;"><strong>{{ $student->parent->name }}</strong></p>
                @endif
            </td>
            <td>
                <p>{{ now()->translatedFormat('d F Y') }}</p>
                <p>Guru Pengajar</p>
                <div class="qr-box">
                    <img src="data:image/svg+xml;base64,{{ $qrCodeBase64 }}" alt="QR Code">
                </div>
                <div class="sign-line"></div>
                @if(isset($teacherName))
                    <p style="margin-top: 4px;"><strong>{{ $teacherName }}</strong></p>
                @endif
            </td>
        </tr>
    </table>

</div>
</body>
</html>
