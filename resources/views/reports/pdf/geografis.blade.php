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
        .progress-bar { 
            background-color: #e9ecef; 
            height: 15px; 
            border-radius: 3px; 
            overflow: hidden; 
        }
        .progress-fill { 
            background-color: #28a745; 
            height: 100%; 
            color: white; 
            text-align: center; 
            font-size: 10px; 
            line-height: 15px; 
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>SMK Bakti Nusantara 666</p>
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Wilayah</th>
                <th>Jurusan</th>
                <th>Total Pendaftar</th>
                <th>Sudah Bayar</th>
                <th>Persentase Bayar</th>
                <th>Koordinat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            @php
                $persentase = $item->total_pendaftar > 0 ? 
                    round(($item->sudah_bayar / $item->total_pendaftar) * 100, 1) : 0;
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->wilayah }}</td>
                <td>{{ $item->jurusan }}</td>
                <td class="text-center">{{ $item->total_pendaftar }}</td>
                <td class="text-center">{{ $item->sudah_bayar }}</td>
                <td class="text-center">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $persentase }}%">
                            {{ $persentase }}%
                        </div>
                    </div>
                </td>
                <td class="text-center">
                    {{ round($item->avg_lat, 4) }}, {{ round($item->avg_lng, 4) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total Wilayah: {{ $data->count() }} | Rata-rata Persentase Bayar: {{ $data->avg('sudah_bayar') > 0 ? round($data->sum('sudah_bayar') / $data->sum('total_pendaftar') * 100, 1) : 0 }}%</p>
    </div>
</body>
</html>