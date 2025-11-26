<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Executive Summary SPMB</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .kpi-grid { display: table; width: 100%; margin-bottom: 20px; }
        .kpi-item { display: table-cell; width: 25%; text-align: center; padding: 10px; border: 1px solid #ddd; }
        .kpi-value { font-size: 24px; font-weight: bold; color: #007bff; }
        .section { margin-bottom: 25px; }
        .section h3 { background: #f8f9fa; padding: 8px; margin: 0 0 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 10px; }
        .badge-success { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: black; }
        .badge-danger { background: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h1>EXECUTIVE SUMMARY</h1>
        <h2>Sistem Penerimaan Murid Baru Online</h2>
        <p>Periode: {{ $periode ?? date('F Y') }}</p>
        <p>Generated: {{ now()->format('d F Y H:i') }}</p>
    </div>

    <!-- KPI Section -->
    <div class="section">
        <h3>Key Performance Indicators</h3>
        <div class="kpi-grid">
            <div class="kpi-item">
                <div class="kpi-value">{{ number_format($data['total_pendaftar']) }}</div>
                <div>Total Pendaftar</div>
            </div>
            <div class="kpi-item">
                <div class="kpi-value">{{ $data['rasio_terverifikasi'] }}%</div>
                <div>Rasio Terverifikasi</div>
            </div>
            <div class="kpi-item">
                <div class="kpi-value">{{ $data['progress_kuota'] }}%</div>
                <div>Progress Kuota</div>
            </div>
            <div class="kpi-item">
                <div class="kpi-value">{{ number_format($data['total_pemasukan']) }}</div>
                <div>Total Pemasukan</div>
            </div>
        </div>
    </div>

    <!-- Status Distribution -->
    <div class="section">
        <h3>Distribusi Status Pendaftar</h3>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-center">Persentase</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['status_distribusi'] as $status)
                <tr>
                    <td>{{ $status->status }}</td>
                    <td class="text-center">{{ $status->jumlah }}</td>
                    <td class="text-center">{{ round(($status->jumlah / $data['total_pendaftar']) * 100, 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Jurusan Statistics -->
    <div class="section">
        <h3>Statistik per Jurusan</h3>
        <table>
            <thead>
                <tr>
                    <th>Jurusan</th>
                    <th class="text-center">Kuota</th>
                    <th class="text-center">Pendaftar</th>
                    <th class="text-center">Rasio</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['jurusan_stats'] as $jurusan)
                <tr>
                    <td>{{ $jurusan->nama }}</td>
                    <td class="text-center">{{ $jurusan->kuota }}</td>
                    <td class="text-center">{{ $jurusan->pendaftar_count }}</td>
                    <td class="text-center">{{ $jurusan->rasio }}%</td>
                    <td class="text-center">
                        @if($jurusan->rasio >= 100)
                            <span class="badge badge-success">Full</span>
                        @elseif($jurusan->rasio >= 75)
                            <span class="badge badge-warning">High</span>
                        @else
                            <span class="badge badge-danger">Low</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Top Schools -->
    <div class="section">
        <h3>Top 10 Asal Sekolah</h3>
        <table>
            <thead>
                <tr>
                    <th>Ranking</th>
                    <th>Nama Sekolah</th>
                    <th>Kabupaten</th>
                    <th class="text-center">Jumlah Pendaftar</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['asal_sekolah']->take(10) as $index => $sekolah)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $sekolah->nama_sekolah }}</td>
                    <td>{{ $sekolah->kabupaten }}</td>
                    <td class="text-center">{{ $sekolah->jumlah }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Recommendations -->
    <div class="section">
        <h3>Rekomendasi Strategis</h3>
        <ul>
            @if($data['progress_kuota'] < 50)
                <li><strong>Marketing:</strong> Tingkatkan promosi untuk mencapai target kuota ({{ $data['progress_kuota'] }}%)</li>
            @endif
            
            @if($data['rasio_terverifikasi'] < 80)
                <li><strong>Administrasi:</strong> Percepat proses verifikasi berkas ({{ $data['rasio_terverifikasi'] }}%)</li>
            @endif
            
            @if(isset($data['performance_indicator']) && $data['performance_indicator'] == 'low')
                <li><strong>Operasional:</strong> Evaluasi tren pendaftaran harian yang menurun</li>
            @endif
            
            <li><strong>Keuangan:</strong> Monitor pembayaran untuk memastikan cash flow optimal</li>
        </ul>
    </div>

    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666;">
        <p>Laporan ini dibuat secara otomatis oleh Sistem SPMB</p>
        <p>{{ config('app.name') }} - {{ now()->year }}</p>
    </div>
</body>
</html>