<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Pendaftar SPMB</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2, h4 { margin: 0; padding: 0; text-align: center; }
        .header { margin-bottom: 20px; text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LEMBAR LAPORAN DATA PENDAFTAR</h2>
        <h4>Sistem Penerimaan Murid Baru (SPMB)</h4>
        <p>Tanggal Cetak: {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No Pendaftaran</th>
                <th>Nama Lengkap</th>
                <th>Jurusan</th>
                <th>Gelombang</th>
                <th>Status</th>
                <th>Tanggal Daftar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pendaftar as $index => $p)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $p->no_pendaftaran }}</td>
                <td>{{ $p->nama }}</td>
                <td>{{ $p->jurusan->nama ?? '-' }}</td>
                <td>{{ $p->gelombang->nama ?? '-' }}</td>
                <td>
                    @if($p->status == 'PAID')
                        Sudah Bayar
                    @elseif($p->status == 'ADM_PASS')
                        Lolos Administrasi
                    @elseif($p->status == 'ADM_REJECT')
                        Ditolak
                    @else
                        Menunggu Verifikasi
                    @endif
                </td>
                <td>{{ $p->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>