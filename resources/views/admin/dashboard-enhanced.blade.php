@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid">
    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6>Total Pendaftar</h6>
                            <h3>{{ number_format($totalPendaftar) }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                    <small>+{{ $pendaftarBaru }} hari ini</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6>Sudah Verifikasi</h6>
                            <h3>{{ number_format($sudahVerifikasi) }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                    <small>{{ $totalPendaftar > 0 ? round(($sudahVerifikasi/$totalPendaftar)*100, 1) : 0 }}% dari total</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6>Sudah Bayar</h6>
                            <h3>{{ number_format($sudahBayar) }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-money-bill fa-2x"></i>
                        </div>
                    </div>
                    <small>{{ $totalPendaftar > 0 ? round(($sudahBayar/$totalPendaftar)*100, 1) : 0 }}% dari total</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6>Menunggu Verifikasi</h6>
                            <h3>{{ number_format($menungguVerifikasi) }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                    <small>Perlu ditindaklanjuti</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>📈 Tren Pendaftaran Harian (7 Hari Terakhir)</h5>
                </div>
                <div class="card-body">
                    <canvas id="trenChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>🏫 Top 5 Asal Sekolah</h5>
                </div>
                <div class="card-body">
                    @foreach($topSekolah as $index => $sekolah)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                            <small>{{ Str::limit($sekolah->nama_sekolah, 20) }}</small>
                        </div>
                        <span class="badge bg-secondary">{{ $sekolah->jumlah }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Row -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>📊 Statistik per Jurusan</h5>
                </div>
                <div class="card-body">
                    <canvas id="jurusanChart" height="150"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>🗺️ Peta Sebaran Pendaftar</h5>
                </div>
                <div class="card-body">
                    <div id="map" style="height: 300px;"></div>
                    <small class="text-muted">{{ $sebaranKoordinat->count() }} pendaftar dengan koordinat</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

<script>
// Tren Chart
const trenCtx = document.getElementById('trenChart').getContext('2d');
new Chart(trenCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($trenHarian->pluck('tanggal')) !!},
        datasets: [{
            label: 'Pendaftar',
            data: {!! json_encode($trenHarian->pluck('jumlah')) !!},
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            borderWidth: 3,
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

// Jurusan Chart
const jurusanCtx = document.getElementById('jurusanChart').getContext('2d');
new Chart(jurusanCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($statistikJurusan->pluck('nama')) !!},
        datasets: [{
            label: 'Pendaftar',
            data: {!! json_encode($statistikJurusan->pluck('pendaftar_count')) !!},
            backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1']
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

// Map
const map = L.map('map').setView([-6.2088, 106.8456], 10);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

@if($sebaranKoordinat->count() > 0)
    @foreach($sebaranKoordinat as $koordinat)
        L.marker([{{ $koordinat->lat }}, {{ $koordinat->lng }}])
         .addTo(map)
         .bindPopup('<b>{{ $koordinat->nama }}</b><br>{{ $koordinat->jurusan }}');
    @endforeach
@endif
</script>
@endsection