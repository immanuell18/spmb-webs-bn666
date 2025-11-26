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
        .footer { margin-top: 20px; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>SMK Bakti Nusantara 666</p>
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
        @if(!empty($filters['date_from']) || !empty($filters['date_to']))
            <p>Periode: {{ $filters['date_from'] ?? 'Semua' }} - {{ $filters['date_to'] ?? 'Semua' }}</p>
        @endif
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
            @foreach($data as $index => $pendaftar)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $pendaftar->no_pendaftaran }}</td>
                <td>{{ $pendaftar->nama }}</td>
                <td>{{ $pendaftar->jurusan->nama ?? '-' }}</td>
                <td>{{ $pendaftar->gelombang->nama ?? '-' }}</td>
                <td>
                    @switch($pendaftar->status)
                        @case('SUBMIT') Menunggu Verifikasi @break
                        @case('ADM_PASS') Berkas Disetujui @break
                        @case('ADM_REJECT') Berkas Ditolak @break
                        @case('PAID') Sudah Bayar @break
                        @default Unknown
                    @endswitch
                </td>
                <td>{{ $pendaftar->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total: {{ $data->count() }} pendaftar</p>
        <p>Laporan ini dibuat secara otomatis oleh sistem SPMB SMK Bakti Nusantara 666</p>
    </div>
</body>
</html>