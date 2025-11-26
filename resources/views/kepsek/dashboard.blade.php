@extends('layouts.admin')

@section('title', 'Dashboard Kepala Sekolah - SPMB')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Eksekutif</h1>
        <div>
            <select class="form-control d-inline-block w-auto" onchange="filterByGelombang(this.value)">
                <option value="">Semua Gelombang</option>
                @foreach(\App\Models\Gelombang::all() as $g)
                <option value="{{ $g->id }}">{{ $g->nama }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pendaftar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $kpi['total_pendaftar'] }}</div>
                            <div class="text-xs text-success">Target: {{ $kpi['total_kuota'] }}</div>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Rasio Terverifikasi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format(($kpi['terverifikasi'] / max($kpi['total_pendaftar'], 1)) * 100, 1) }}%</div>
                            <div class="progress progress-sm mr-2">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($kpi['terverifikasi'] / max($kpi['total_pendaftar'], 1)) * 100 }}%"></div>
                            </div>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pendaftar Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $trenToday }}</div>
                            <div class="text-xs text-{{ $performanceIndicator == 'good' ? 'success' : 'warning' }}">{{ $performanceIndicator == 'good' ? 'Di atas rata-rata' : 'Di bawah rata-rata' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Progress Kuota</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $progressKuota }}%</div>
                            <div class="text-xs text-warning">Pendaftar vs Kuota</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Tren Pendaftaran -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tren Pendaftaran Harian</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 200px;">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Komposisi Jurusan -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Komposisi Jurusan</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="jurusanChart"></canvas>
                    </div>
                    <div class="mt-3">
                        @foreach($jurusanStats as $index => $jurusan)
                            @php
                                $colors = ['text-primary', 'text-success', 'text-info', 'text-warning', 'text-danger'];
                                $shortName = str_replace(['Pengembangan Perangkat Lunak dan Gim', 'Akuntansi dan Keuangan Lembaga', 'Desain Komunikasi Visual', 'Broadcasting dan Perfilman', 'Bisnis Daring dan Pemasaran'], ['PPLG', 'AKT', 'DKV', 'BP', 'BDP'], $jurusan->nama);
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-2 px-2">
                                <span class="small"><i class="fas fa-circle {{ $colors[$index] ?? 'text-secondary' }} mr-1"></i> {{ $shortName }}</span>
                                <span class="small font-weight-bold">{{ $jurusan->pendaftar_count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional KPI Row -->
    <div class="row">
        <!-- Asal Sekolah -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top 10 Asal Sekolah</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Sekolah</th>
                                    <th>Jumlah</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($asalSekolah as $sekolah)
                                <tr>
                                    <td>{{ $sekolah->nama_sekolah }}</td>
                                    <td>{{ $sekolah->jumlah }}</td>
                                    <td>{{ $totalPendaftar > 0 ? number_format(($sekolah->jumlah / $totalPendaftar) * 100, 1) : 0 }}%</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sebaran Wilayah -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sebaran Wilayah</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Kabupaten/Kota</th>
                                    <th>Jumlah</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sebaranWilayah as $wilayah)
                                <tr>
                                    <td>{{ trim($wilayah->wilayah) }}</td>
                                    <td>{{ $wilayah->jumlah }}</td>
                                    <td>{{ $totalPendaftar > 0 ? number_format(($wilayah->jumlah / $totalPendaftar) * 100, 1) : 0 }}%</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Tren Chart
const trendCtx = document.getElementById('trendChart').getContext('2d');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: @json($trenHarian->pluck('tanggal')),
        datasets: [{
            label: 'Pendaftar',
            data: @json($trenHarian->pluck('pendaftar')),
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: false,
                ticks: {
                    precision: 0
                }
            }
        }
    }
});

// Jurusan Chart
const jurusanCtx = document.getElementById('jurusanChart').getContext('2d');
const jurusanLabels = @json($jurusanStats->pluck('nama')).map(nama => {
    return nama.replace('Pengembangan Perangkat Lunak dan Gim', 'PPLG')
              .replace('Akuntansi dan Keuangan Lembaga', 'AKT')
              .replace('Desain Komunikasi Visual', 'DKV')
              .replace('Broadcasting dan Perfilman', 'BP')
              .replace('Bisnis Daring dan Pemasaran', 'BDP');
});

new Chart(jurusanCtx, {
    type: 'doughnut',
    data: {
        labels: jurusanLabels,
        datasets: [{
            data: @json($jurusanStats->pluck('pendaftar_count')),
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

function filterByGelombang(gelombangId) {
    if (gelombangId) {
        window.location.href = '{{ route("kepsek.dashboard") }}?gelombang=' + gelombangId;
    } else {
        window.location.href = '{{ route("kepsek.dashboard") }}';
    }
}
</script>
@endsection