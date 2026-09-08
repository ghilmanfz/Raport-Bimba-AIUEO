<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Absensi {{ $monthLabel }}</title>
  <style>
    @page { margin: 24px 28px; }
    body { font-family: DejaVu Sans, sans-serif; color:#1e293b; font-size:10px; }
    h1 { font-size:20px; margin:4px 0; text-align:center; }
    .subtitle { text-align:center; color:#64748b; margin-bottom:18px; }
    .institution { text-align:center; color:#ea580c; font-weight:bold; letter-spacing:1.5px; text-transform:uppercase; }
    .cards { width:100%; margin-bottom:16px; border-collapse:separate; border-spacing:6px 0; }
    .cards td { border:1px solid #e2e8f0; border-radius:6px; padding:8px; text-align:center; }
    .cards strong { display:block; font-size:18px; margin-top:3px; }
    .report { width:100%; border-collapse:collapse; margin-top:8px; }
    .report th, .report td { border:1px solid #cbd5e1; padding:6px; }
    .report th { background:#f1f5f9; text-transform:uppercase; font-size:9px; }
    .center { text-align:center; }
    .section { font-size:13px; margin:16px 0 6px; }
    .footer { margin-top:16px; color:#64748b; font-size:9px; }
  </style>
</head>
<body>
  <div class="institution">{{ $institutionName }}</div>
  <h1>Laporan Absensi Anak</h1>
  <div class="subtitle">Periode {{ $monthLabel }} · {{ $summaryRows->count() }} murid · Status: {{ $studentStatusLabel }}</div>
  @if ($institutionAddress)
    <div class="subtitle">{{ $institutionAddress }}</div>
  @endif

  <table class="cards"><tr><td>Total<strong>{{ $overall['total'] }}</strong></td><td>Hadir<strong>{{ $overall['hadir'] }}</strong></td><td>Sakit<strong>{{ $overall['sakit'] }}</strong></td><td>Izin<strong>{{ $overall['izin'] }}</strong></td><td>Alpa<strong>{{ $overall['alpa'] }}</strong></td></tr></table>
  <div class="footer">Rasio hadir = Hadir ÷ total hari yang sudah dicatat (H/S/I/A). Tanda “-” berarti belum ada data absensi pada periode ini.</div>

  <div class="section"><strong>Rekap Bulanan per Anak</strong></div>
  <table class="report"><thead><tr><th>NIS</th><th>Nama Anak</th><th>Status</th><th>Kelas Saat Ini</th><th>H</th><th>S</th><th>I</th><th>A</th><th>Hari Tercatat</th><th>Hadir/Tercatat</th></tr></thead><tbody>
    @forelse($summaryRows as $row)<tr><td>{{ $row['student']->nis }}</td><td>{{ $row['student']->name }}</td><td>{{ $row['student']->status_label }}</td><td>{{ $row['student']->classroom?->name ?? '-' }}</td><td class="center">{{ $row['hadir'] }}</td><td class="center">{{ $row['sakit'] }}</td><td class="center">{{ $row['izin'] }}</td><td class="center">{{ $row['alpa'] }}</td><td class="center">{{ $row['total'] }}</td><td class="center">{{ $row['percentage'] === null ? '-' : $row['percentage'].'%' }}</td></tr>@empty<tr><td colspan="10" class="center">Tidak ada data.</td></tr>@endforelse
  </tbody></table>

  <div class="section"><strong>Rekap Harian</strong></div>
  <table class="report"><thead><tr><th>Tanggal</th><th>Hadir</th><th>Sakit</th><th>Izin</th><th>Alpa</th><th>Total Tercatat</th><th>Hadir/Tercatat</th></tr></thead><tbody>
    @forelse($dailySummary as $day)<tr><td>{{ \Carbon\Carbon::parse($day['date'])->locale('id')->translatedFormat('d F Y') }}</td><td class="center">{{ $day['hadir'] }}</td><td class="center">{{ $day['sakit'] }}</td><td class="center">{{ $day['izin'] }}</td><td class="center">{{ $day['alpa'] }}</td><td class="center">{{ $day['total'] }}</td><td class="center">{{ $day['percentage'] }}%</td></tr>@empty<tr><td colspan="7" class="center">Belum ada absensi pada periode ini.</td></tr>@endforelse
  </tbody></table>
  <div class="footer">Dicetak pada {{ now()->locale('id')->translatedFormat('d F Y H:i') }} · Keterangan: H = Hadir, S = Sakit, I = Izin, A = Alpa.</div>
</body>
</html>
