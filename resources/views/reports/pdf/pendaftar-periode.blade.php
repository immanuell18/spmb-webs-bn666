<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pendaftar per Periode</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 20px; }
        .header { text-align: center; margin-bottom: 25px; }
        .header h1 { font-size: 18px; margin-bottom: 5px; }
        .header h3 { font-size: 14px; margin-bottom: 5px; }
        .section { margin-bottom: 20px; page-break-inside: avoid; }
        .section-title { font-size: 14px; font-weight: bold; margin-bottom: 8px; border-bottom: 2px solid #007bff; padding-bottom: 3px; }
        .table { width: 100%; border-collapse: collapse; font-size: 10px; }
        .table th, .table td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        .table th { background-color: #f8f9fa; font-weight: bold; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Pendaftar per Periode</h1>
        <h3>SMK Bakti Nusantara 666</h3>
        <p>Dibuat pada: {{ now()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Data Pendaftar</div>
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No Pendaftaran</th>
                    <th>Nama</th>
                    <th>Jurusan</th>
                    <th>Gelombang</th>
                    <th>Status</th>
                    <th>Tanggal Daftar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendaftar as $index => $p)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $p->no_pendaftaran }}</td>
                    <td>{{ $p->nama }}</td>
                    <td>{{ $p->jurusan->nama ?? '-' }}</td>
                    <td>{{ $p->gelombang->nama ?? '-' }}</td>
                    <td class="text-center">
                        @switch($p->status)
                            @case('SUBMIT')
                                Menunggu Verifikasi
                                @break
                            @case('ADM_PASS')
                                Berkas Disetujui
                                @break
                            @case('ADM_REJECT')
                                Berkas Ditolak
                                @break
                            @case('PAID')
                                Sudah Bayar
                                @break
                            @default
                                {{ $p->status }}
                        @endswitch
                    </td>
                    <td class="text-center">{{ $p->created_at->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Belum ada data pendaftar</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Ringkasan</div>
        <table class="table">
            <tr>
                <td><strong>Total Pendaftar</strong></td>
                <td>{{ $pendaftar->count() }}</td>
            </tr>
            <tr>
                <td><strong>Sudah Bayar</strong></td>
                <td>{{ $pendaftar->where('status', 'PAID')->count() }}</td>
            </tr>
            <tr>
                <td><strong>Menunggu Verifikasi</strong></td>
                <td>{{ $pendaftar->where('status', 'SUBMIT')->count() }}</td>
            </tr>
            <tr>
                <td><strong>Berkas Disetujui</strong></td>
                <td>{{ $pendaftar->where('status', 'ADM_PASS')->count() }}</td>
            </tr>
        </table>
    </div>
</body>
</html>