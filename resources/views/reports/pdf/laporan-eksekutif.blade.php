<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Eksekutif SPMB</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 20px; }
        .header { text-align: center; margin-bottom: 25px; }
        .header h1 { font-size: 18px; margin-bottom: 5px; }
        .header h3 { font-size: 14px; margin-bottom: 5px; }
        .kpi-grid { width: 100%; margin-bottom: 20px; }
        .kpi-row { display: table; width: 100%; margin-bottom: 10px; }
        .kpi-card { display: table-cell; width: 25%; text-align: center; border: 1px solid #ddd; padding: 10px; vertical-align: top; }
        .kpi-value { font-size: 18px; font-weight: bold; color: #007bff; margin-bottom: 3px; }
        .kpi-label { font-size: 10px; font-weight: bold; }
        .section { margin-bottom: 20px; page-break-inside: avoid; }
        .section-title { font-size: 14px; font-weight: bold; margin-bottom: 8px; border-bottom: 2px solid #007bff; padding-bottom: 3px; }
        .table { width: 100%; border-collapse: collapse; font-size: 10px; }
        .table th, .table td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        .table th { background-color: #f8f9fa; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Eksekutif SPMB</h1>
        <h3>SMK Bakti Nusantara 666</h3>
        <p>Dibuat pada: {{ now()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Ringkasan Kinerja</div>
        <div class="kpi-grid">
            <div class="kpi-row">
                <div class="kpi-card">
                    <div class="kpi-value">{{ $laporan['total_pendaftar'] }}</div>
                    <div class="kpi-label">Total Pendaftar</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-value">{{ number_format($laporan['rasio_verifikasi'], 1) }}%</div>
                    <div class="kpi-label">Rasio Verifikasi</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-value">{{ number_format($laporan['rasio_pembayaran'], 1) }}%</div>
                    <div class="kpi-label">Rasio Pembayaran</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-value">Rp {{ number_format($laporan['total_pemasukan']/1000000, 1) }}M</div>
                    <div class="kpi-label">Total Pemasukan</div>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Laporan per Gelombang</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Gelombang</th>
                    <th>Periode</th>
                    <th>Total Pendaftar</th>
                    <th>Terverifikasi</th>
                    <th>Terbayar</th>
                    <th>Pemasukan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gelombang as $g)
                <tr>
                    <td>{{ $g->nama }}</td>
                    <td>{{ $g->tanggal_mulai }} - {{ $g->tanggal_selesai }}</td>
                    <td>{{ $g->pendaftar->count() }}</td>
                    <td>{{ $g->pendaftar->where('status', 'ADM_PASS')->count() }}</td>
                    <td>{{ $g->pendaftar->where('status_pembayaran', 'terbayar')->count() }}</td>
                    <td>Rp {{ number_format($g->pendaftar->where('status_pembayaran', 'terbayar')->sum('biaya_pendaftaran'), 0, ',', '.') }}</td>
                    <td>
                        @if($g->tanggal_selesai < now())
                            Selesai
                        @elseif($g->tanggal_mulai <= now())
                            Aktif
                        @else
                            Belum Mulai
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Metrik Performa</div>
        <table class="table">
            <tr>
                <td><strong>Tingkat Konversi (Daftar → Bayar)</strong></td>
                <td>{{ number_format($laporan['rasio_pembayaran'], 1) }}%</td>
            </tr>
            <tr>
                <td><strong>Efisiensi Verifikasi</strong></td>
                <td>{{ number_format($laporan['rasio_verifikasi'], 1) }}%</td>
            </tr>
            <tr>
                <td><strong>Total Pendapatan Terealisasi</strong></td>
                <td>Rp {{ number_format($laporan['total_pemasukan'], 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
</body>
</html>