@extends('layouts.admin')

@section('title', 'Dashboard Keuangan')

@section('content')
<div class="container-fluid">
    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Total Pemasukan</h6>
                    <h3>Rp {{ number_format($statusPembayaran['total_pemasukan'], 0, ',', '.') }}</h3>
                    <small>Dari {{ $statusPembayaran['sudah_bayar'] }} pembayaran</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6>Sudah Bayar</h6>
                    <h3>{{ $statusPembayaran['sudah_bayar'] }}</h3>
                    <small>Terverifikasi</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6>Menunggu Verifikasi</h6>
                    <h3>{{ $pembayaranPending->count() }}</h3>
                    <small>Perlu ditindaklanjuti</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6>Belum Bayar</h6>
                    <h3>{{ $statusPembayaran['belum_bayar'] }}</h3>
                    <small>Lulus administrasi</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5>💰 Rekap Pemasukan per Gelombang</h5>
                    <a href="{{ route('keuangan.export.excel') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-download"></i> Export Excel
                    </a>
                </div>
                <div class="card-body">
                    <canvas id="rekapChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>📈 Tren Pembayaran (7 Hari)</h5>
                </div>
                <div class="card-body">
                    <canvas id="trenChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>📊 Rekap per Jurusan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Jurusan</th>
                                    <th class="text-end">Pendaftar</th>
                                    <th class="text-end">Bayar</th>
                                    <th class="text-end">Pemasukan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rekapJurusan as $jurusan)
                                <tr>
                                    <td>{{ $jurusan->nama }}</td>
                                    <td class="text-end">{{ $jurusan->pendaftar_count }}</td>
                                    <td class="text-end">{{ $jurusan->sudah_bayar }}</td>
                                    <td class="text-end">Rp {{ number_format($jurusan->total_pemasukan, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>⏳ Pembayaran Pending Verifikasi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>No. Pendaftaran</th>
                                    <th>Nama</th>
                                    <th>Jurusan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pembayaranPending->take(5) as $pendaftar)
                                <tr>
                                    <td>{{ $pendaftar->no_pendaftaran }}</td>
                                    <td>{{ Str::limit($pendaftar->nama, 15) }}</td>
                                    <td>{{ $pendaftar->jurusan->nama ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('keuangan.verifikasi') }}" class="btn btn-sm btn-primary">
                                            Verifikasi
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($pembayaranPending->count() > 5)
                        <div class="text-center">
                            <a href="{{ route('keuangan.verifikasi') }}" class="btn btn-sm btn-outline-primary">
                                Lihat Semua ({{ $pembayaranPending->count() }})
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Rekap Chart
const rekapCtx = document.getElementById('rekapChart').getContext('2d');
new Chart(rekapCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($rekapGelombang->pluck('nama')) !!},
        datasets: [{
            label: 'Pemasukan (Rp)',
            data: {!! json_encode($rekapGelombang->pluck('total_pemasukan')) !!},
            backgroundColor: '#28a745',
            borderColor: '#1e7e34',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { 
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});

// Tren Chart
const trenCtx = document.getElementById('trenChart').getContext('2d');
new Chart(trenCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($trenPembayaran->pluck('tanggal')) !!},
        datasets: [{
            label: 'Pembayaran',
            data: {!! json_encode($trenPembayaran->pluck('jumlah')) !!},
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
@endsection