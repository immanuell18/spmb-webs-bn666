<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #333; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer { margin-top: 20px; font-size: 10px; color: #666; }
        .summary { background-color: #e9ecef; padding: 10px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>SMK Bakti Nusantara 666</p>
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <div class="summary">
        <strong>Ringkasan Pembayaran:</strong><br>
        Total Pendaftar: {{ $data->count() }} | 
        Sudah Bayar: {{ $data->where('status', 'PAID')->count() }} | 
        Total Pemasukan: Rp {{ number_format($data->where('status', 'PAID')->count() * \App\Models\SystemSetting::getBiayaPendaftaran(), 0, ',', '.') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No Pendaftaran</th>
                <th>Nama</th>
                <th>Jurusan</th>
                <th>Status Bayar</th>
                <th>Biaya</th>
                <th>Tanggal Bayar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $pendaftar)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $pendaftar->no_pendaftaran }}</td>
                <td>{{ $pendaftar->nama }}</td>
                <td>{{ $pendaftar->jurusan->nama ?? '-' }}</td>
                <td class="text-center">
                    @if($pendaftar->status === 'PAID')
                        ✓ Lunas
                    @else
                        ⏳ Pending
                    @endif
                </td>
                <td class="text-right">Rp 250.000</td>
                <td class="text-center">
                    @if($pendaftar->status === 'PAID')
                        {{ $pendaftar->updated_at->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total: {{ $data->count() }} transaksi | Sudah Bayar: {{ $data->where('status', 'PAID')->count() }}</p>
    </div>
</body>
</html>