<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Dashboard Eksekutif</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 20px; }
        .header { text-align: center; margin-bottom: 25px; }
        .header h1 { font-size: 18px; margin-bottom: 5px; }
        .header h3 { font-size: 14px; margin-bottom: 5px; }
        .kpi-grid { width: 100%; margin-bottom: 20px; }
        .kpi-row { display: table; width: 100%; margin-bottom: 10px; }
        .kpi-card { display: table-cell; width: 25%; text-align: center; border: 1px solid #ddd; padding: 10px; vertical-align: top; }
        .kpi-value { font-size: 18px; font-weight: bold; color: #007bff; margin-bottom: 3px; }
        .kpi-label { font-size: 10px; font-weight: bold; margin-bottom: 2px; }
        .kpi-desc { font-size: 9px; color: #666; }
        .section { margin-bottom: 20px; page-break-inside: avoid; }
        .section-title { font-size: 14px; font-weight: bold; margin-bottom: 8px; border-bottom: 2px solid #007bff; padding-bottom: 3px; }
        .table { width: 100%; border-collapse: collapse; font-size: 10px; }
        .table th, .table td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        .table th { background-color: #f8f9fa; font-weight: bold; }
        .alert { padding: 8px; margin: 8px 0; border-left: 4px solid #007bff; background-color: #f8f9fa; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Dashboard Eksekutif</h1>
        <h3>SMK Bakti Nusantara 666</h3>
        <p>Dibuat pada: {{ now()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Indikator Kinerja Utama</div>
        <div class="kpi-grid">
            <div class="kpi-row">
                <div class="kpi-card">
                    <div class="kpi-value">{{ $kpi['total_pendaftar'] }}</div>
                    <div class="kpi-label">Total Pendaftar</div>
                    <div class="kpi-desc">Target: {{ $kpi['target_kuota'] }}</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-value">{{ number_format($kpi['conversion_rate'], 1) }}%</div>
                    <div class="kpi-label">Tingkat Konversi</div>
                    <div class="kpi-desc">{{ $kpi['sudah_bayar'] }}/{{ $kpi['total_pendaftar'] }}</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-value">Rp {{ $payment_summary['total_revenue'] > 0 ? number_format($payment_summary['total_revenue']/1000000, 1) . 'M' : '0' }}</div>
                    <div class="kpi-label">Total Pendapatan</div>
                    <div class="kpi-desc">Sukses: {{ number_format($payment_summary['success_rate'], 1) }}%</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-value">{{ $kpi['menunggu_verifikasi'] }}</div>
                    <div class="kpi-label">Menunggu Review</div>
                    <div class="kpi-desc">Perlu Tindakan</div>
                </div>
            </div>
        </div>
    </div>

    @if(count($alerts) > 0)
    <div class="section">
        <div class="section-title">Peringatan & Notifikasi</div>
        @foreach($alerts as $alert)
        <div class="alert">{{ $alert['message'] }}</div>
        @endforeach
    </div>
    @endif

    <div class="section">
        <div class="section-title">Top Jurusan</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Peringkat</th>
                    <th>Jurusan</th>
                    <th>Pendaftar</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                @foreach($top_jurusan as $index => $jurusan)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $jurusan->nama }}</td>
                    <td>{{ $jurusan->pendaftar_count }}</td>
                    <td>{{ $kpi['total_pendaftar'] > 0 ? round(($jurusan->pendaftar_count / $kpi['total_pendaftar']) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Tren Pendaftaran Mingguan</div>
        <table class="table">
            <thead>
                <tr>
                    @foreach($weekly_trend['labels'] as $label)
                    <th>{{ $label }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    @foreach($weekly_trend['data'] as $data)
                    <td>{{ $data }}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
        <div style="margin-top: 10px; font-size: 10px;">
            <strong>Total 7 Hari:</strong> {{ array_sum($weekly_trend['data']) }} | 
            <strong>Hari Ini:</strong> {{ end($weekly_trend['data']) }} | 
            <strong>Rata-rata Harian:</strong> {{ round(array_sum($weekly_trend['data']) / 7, 1) }}
        </div>
    </div>

    <div class="section">
        <div class="section-title">Ringkasan Keuangan</div>
        <table class="table">
            <tr>
                <td><strong>Revenue Terealisasi</strong></td>
                <td>Rp {{ number_format($payment_summary['total_revenue'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Pending Revenue</strong></td>
                <td>Rp {{ number_format($payment_summary['pending_amount'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Tingkat Keberhasilan</strong></td>
                <td>{{ number_format($payment_summary['success_rate'], 1) }}%</td>
            </tr>
            <tr>
                <td><strong>Pencapaian Target</strong></td>
                <td>{{ number_format($kpi['progress_percentage'], 1) }}%</td>
            </tr>
        </table>
    </div>
</body>
</html>