<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan SPMB</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 10px; }
        .subtitle { font-size: 14px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background-color: #f9f9f9; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">LAPORAN KEUANGAN SPMB</div>
        <div class="subtitle">Periode: {{ date('Y') }}</div>
        <div class="subtitle">Tanggal Cetak: {{ date('d/m/Y H:i:s') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Gelombang</th>
                <th>Total Pendaftar</th>
                <th>Sudah Bayar</th>
                <th>Belum Bayar</th>
                <th>Persentase Bayar</th>
                <th>Estimasi Pemasukan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalPendaftar = 0;
                $totalSudahBayar = 0;
                $totalPemasukan = 0;
                $biayaPendaftaran = \App\Models\SystemSetting::getBiayaPendaftaran();
            @endphp
            
            @foreach($rekap as $index => $gelombang)
                @php
                    $belumBayar = $gelombang->pendaftar_count - $gelombang->sudah_bayar;
                    $persentase = $gelombang->pendaftar_count > 0 ? round(($gelombang->sudah_bayar / $gelombang->pendaftar_count) * 100, 1) : 0;
                    
                    // Hitung pemasukan dari payment transactions yang sudah paid
                    $pemasukan = 0;
                    foreach($gelombang->pendaftar as $pendaftar) {
                        $hasPaidTransaction = false;
                        foreach($pendaftar->paymentTransactions as $transaction) {
                            if($transaction->status === 'paid') {
                                $pemasukan += $transaction->amount;
                                $hasPaidTransaction = true;
                            }
                        }
                        // Jika tidak ada payment transaction tapi status PAID, gunakan biaya default
                        if(!$hasPaidTransaction && $pendaftar->status === 'PAID') {
                            $pemasukan += $biayaPendaftaran;
                        }
                    }
                    
                    $totalPendaftar += $gelombang->pendaftar_count;
                    $totalSudahBayar += $gelombang->sudah_bayar;
                    $totalPemasukan += $pemasukan;
                @endphp
                
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $gelombang->nama }}</td>
                    <td class="text-right">{{ number_format($gelombang->pendaftar_count) }}</td>
                    <td class="text-right">{{ number_format($gelombang->sudah_bayar) }}</td>
                    <td class="text-right">{{ number_format($belumBayar) }}</td>
                    <td class="text-right">{{ $persentase }}%</td>
                    <td class="text-right">Rp {{ number_format($pemasukan) }}</td>
                </tr>
            @endforeach
            
            @php
                $totalBelumBayar = $totalPendaftar - $totalSudahBayar;
                $totalPersentase = $totalPendaftar > 0 ? round(($totalSudahBayar / $totalPendaftar) * 100, 1) : 0;
            @endphp
            
            <tr class="total-row">
                <td colspan="2" class="text-center">TOTAL</td>
                <td class="text-right">{{ number_format($totalPendaftar) }}</td>
                <td class="text-right">{{ number_format($totalSudahBayar) }}</td>
                <td class="text-right">{{ number_format($totalBelumBayar) }}</td>
                <td class="text-right">{{ $totalPersentase }}%</td>
                <td class="text-right">Rp {{ number_format($totalPemasukan) }}</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 30px;">
        <h3>Ringkasan:</h3>
        <ul>
            <li>Total Pendaftar: {{ number_format($totalPendaftar) }} orang</li>
            <li>Sudah Membayar: {{ number_format($totalSudahBayar) }} orang ({{ $totalPersentase }}%)</li>
            <li>Belum Membayar: {{ number_format($totalBelumBayar) }} orang</li>
            <li>Total Pemasukan: Rp {{ number_format($totalPemasukan) }}</li>
        </ul>
    </div>
</body>
</html>