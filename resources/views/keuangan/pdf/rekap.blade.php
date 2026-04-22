<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $judul }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        h2 { text-align: center; margin-bottom: 6px; }
        p.sub { text-align: center; color: #666; margin-bottom: 20px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #1a56db; color: #fff; padding: 8px 10px; text-align: left; }
        td { padding: 7px 10px; border-bottom: 1px solid #e5e7eb; }
        tr:nth-child(even) td { background: #f9fafb; }
        tfoot td { font-weight: bold; background: #f1f5f9; border-top: 2px solid #1a56db; }
    </style>
</head>
<body>
    <h2>{{ $judul }}</h2>
    <p class="sub">Dicetak pada: {{ now()->format('d F Y, H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Gelombang</th>
                <th>Jurusan</th>
                <th>Total Pendaftar</th>
                <th>Sudah Bayar</th>
                <th>Total Pemasukan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $r)
                <tr>
                    <td>{{ $r['gelombang'] }}</td>
                    <td>{{ $r['jurusan'] }}</td>
                    <td>{{ $r['total_pendaftar'] }}</td>
                    <td>{{ $r['sudah_bayar'] }}</td>
                    <td>{{ $r['total_pemasukan'] }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;color:#999;">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
