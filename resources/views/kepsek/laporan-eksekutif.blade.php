@extends('layouts.admin')

@section('title', 'Laporan Eksekutif - Kepala Sekolah')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Laporan Eksekutif</h1>
        <div>
            <a href="{{ route('kepsek.laporan.export.excel') }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <a href="{{ route('kepsek.laporan.export.pdf') }}" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pendaftar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $laporan['total_pendaftar'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Rasio Verifikasi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($laporan['rasio_verifikasi'], 1) }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Rasio Pembayaran</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($laporan['rasio_pembayaran'], 1) }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Pemasukan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($laporan['total_pemasukan'], 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Laporan per Gelombang -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Laporan per Gelombang</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Gelombang</th>
                            <th>Periode</th>
                            <th>Total Pendaftar</th>
                            <th>Terverifikasi</th>
                            <th>Terbayar</th>
                            <th>Pemasukan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gelombang as $g)
                        <tr>
                            <td>{{ $g->nama }}</td>
                            <td>{{ $g->tanggal_mulai }} - {{ $g->tanggal_selesai }}</td>
                            <td>{{ $g->pendaftar->count() }}</td>
                            <td>{{ $g->pendaftar->where('status', 'ADM_PASS')->count() }}</td>
                            <td>{{ $g->pendaftar->where('status_pembayaran', 'terbayar')->count() }}</td>
                            <td>Rp {{ number_format($g->pendaftar->where('status_pembayaran', 'terbayar')->sum('biaya_pendaftaran'), 0, ',', '.') }}</td>
                            <td>
                                @if($g->tanggal_selesai < now())
                                    <span class="badge badge-secondary">Selesai</span>
                                @elseif($g->tanggal_mulai <= now())
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-warning">Belum Mulai</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Metrik Performa</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Tingkat Konversi (Daftar → Bayar)</span>
                            <span>{{ number_format($laporan['rasio_pembayaran'], 1) }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: {{ $laporan['rasio_pembayaran'] }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Efisiensi Verifikasi</span>
                            <span>{{ number_format($laporan['rasio_verifikasi'], 1) }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-info" style="width: {{ $laporan['rasio_verifikasi'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Target vs Realisasi</h6>
                </div>
                <div class="card-body">
                    <canvas id="targetChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Target vs Realisasi Chart
new Chart(document.getElementById('targetChart'), {
    type: 'bar',
    data: {
        labels: ['Target', 'Realisasi'],
        datasets: [{
            label: 'Pendaftar',
            data: [300, {{ $laporan['total_pendaftar'] }}],
            backgroundColor: ['rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)'],
            borderColor: ['rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)'],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endsection