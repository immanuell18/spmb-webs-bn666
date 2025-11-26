@extends('layouts.admin')

@section('title', 'Executive Dashboard - Kepala Sekolah')

@section('content')
<div class="container-fluid">
    <!-- Executive Summary Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-1">👨‍💼 Executive Dashboard</h2>
                            <p class="mb-0">Ringkasan Eksekutif SPMB SMK Bakti Nusantara 666</p>
                            <small>Last updated: {{ now()->format('d/m/Y H:i:s') }}</small>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('kepsek.export-executive-pdf') }}" class="btn btn-light me-2">
                                <i class="ti ti-file-type-pdf"></i> Export PDF
                            </a>
                            <button class="btn btn-outline-light" onclick="window.print()">
                                <i class="ti ti-printer"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <div class="display-4 text-primary mb-2">{{ $executiveSummary['kpi']['total_pendaftar'] }}</div>
                    <h6 class="card-title">Total Pendaftar</h6>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-primary" style="width: {{ $executiveSummary['kpi']['progress_percentage'] }}%"></div>
                    </div>
                    <small class="text-muted">{{ $executiveSummary['kpi']['progress_percentage'] }}% dari target {{ $executiveSummary['kpi']['target_kuota'] }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <div class="display-4 text-success mb-2">{{ $executiveSummary['kpi']['conversion_rate'] }}%</div>
                    <h6 class="card-title">Conversion Rate</h6>
                    <p class="text-muted mb-0">{{ $executiveSummary['kpi']['sudah_bayar'] }} dari {{ $executiveSummary['kpi']['total_pendaftar'] }} pendaftar</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <div class="display-4 text-info mb-2">Rp {{ number_format($executiveSummary['payment_summary']['total_revenue'], 0, ',', '.') }}</div>
                    <h6 class="card-title">Total Revenue</h6>
                    <p class="text-muted mb-0">Success Rate: {{ $executiveSummary['payment_summary']['success_rate'] }}%</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <div class="display-4 text-warning mb-2">{{ $executiveSummary['kpi']['menunggu_verifikasi'] }}</div>
                    <h6 class="card-title">Pending Review</h6>
                    <p class="text-muted mb-0">Memerlukan tindak lanjut</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts & Notifications -->
    @if(count($executiveSummary['alerts']) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">🚨 Alerts & Notifications</h5>
                </div>
                <div class="card-body">
                    @foreach($executiveSummary['alerts'] as $alert)
                    <div class="alert alert-{{ $alert['type'] }} d-flex align-items-center" role="alert">
                        <i class="ti ti-alert-circle me-2"></i>
                        {{ $alert['message'] }}
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Weekly Trend & Top Jurusan -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">📊 Tren Pendaftaran Mingguan</h5>
                </div>
                <div class="card-body">
                    <div style="height: 150px;">
                        <canvas id="weeklyTrendChart"></canvas>
                    </div>
                    <div class="row mt-3 text-center">
                        <div class="col-md-4">
                            <h6 class="text-primary">{{ array_sum($executiveSummary['weekly_trend']['data']) }}</h6>
                            <small class="text-muted">Total 7 Hari</small>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-success">{{ end($executiveSummary['weekly_trend']['data']) }}</h6>
                            <small class="text-muted">Hari Ini</small>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-info">{{ round(array_sum($executiveSummary['weekly_trend']['data']) / 7, 1) }}</h6>
                            <small class="text-muted">Rata-rata Harian</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">🏆 Top Jurusan</h5>
                </div>
                <div class="card-body">
                    @foreach($executiveSummary['top_jurusan'] as $index => $jurusan)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                            <strong>{{ $jurusan->nama }}</strong>
                        </div>
                        <div class="text-end">
                            <h6 class="mb-0">{{ $jurusan->pendaftar_count }}</h6>
                            <small class="text-muted">pendaftar</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">💰 Ringkasan Keuangan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h6 class="text-muted">Revenue Terealisasi</h6>
                            <h4 class="text-success">Rp {{ number_format($executiveSummary['payment_summary']['total_revenue'], 0, ',', '.') }}</h4>
                        </div>
                        <div class="col-6">
                            <h6 class="text-muted">Pending Revenue</h6>
                            <h4 class="text-warning">Rp {{ number_format($executiveSummary['payment_summary']['pending_amount'], 0, ',', '.') }}</h4>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Success Rate</small>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: {{ $executiveSummary['payment_summary']['success_rate'] }}%">
                                    {{ $executiveSummary['payment_summary']['success_rate'] }}%
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Target Achievement</small>
                            <div class="progress">
                                <div class="progress-bar bg-primary" style="width: {{ $executiveSummary['kpi']['progress_percentage'] }}%">
                                    {{ $executiveSummary['kpi']['progress_percentage'] }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">📈 Status Breakdown</h5>
                </div>
                <div class="card-body">
                    <div style="height: 120px;">
                        <canvas id="statusBreakdownChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Items -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">✅ Recommended Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="border-start border-primary border-4 ps-3 mb-3">
                                <h6 class="text-primary">Marketing & Promotion</h6>
                                <p class="text-muted mb-0">
                                    @if($executiveSummary['kpi']['progress_percentage'] < 50)
                                        Tingkatkan promosi untuk mencapai target kuota
                                    @else
                                        Pertahankan momentum pendaftaran yang baik
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border-start border-warning border-4 ps-3 mb-3">
                                <h6 class="text-warning">Administrative</h6>
                                <p class="text-muted mb-0">
                                    @if($executiveSummary['kpi']['menunggu_verifikasi'] > 20)
                                        Percepat proses verifikasi berkas ({{ $executiveSummary['kpi']['menunggu_verifikasi'] }} pending)
                                    @else
                                        Proses verifikasi berjalan lancar
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border-start border-success border-4 ps-3 mb-3">
                                <h6 class="text-success">Financial</h6>
                                <p class="text-muted mb-0">
                                    @if($executiveSummary['payment_summary']['success_rate'] < 80)
                                        Evaluasi sistem pembayaran (success rate: {{ $executiveSummary['payment_summary']['success_rate'] }}%)
                                    @else
                                        Sistem pembayaran berfungsi optimal
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Compact charts with smaller size
const weeklyCtx = document.getElementById('weeklyTrendChart').getContext('2d');
new Chart(weeklyCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($executiveSummary['weekly_trend']['labels']) !!},
        datasets: [{
            label: 'Pendaftar',
            data: {!! json_encode($executiveSummary['weekly_trend']['data']) !!},
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});

const statusCtx = document.getElementById('statusBreakdownChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Sudah Bayar', 'Menunggu'],
        datasets: [{
            data: [{{ $executiveSummary['kpi']['sudah_bayar'] }}, {{ $executiveSummary['kpi']['menunggu_verifikasi'] }}],
            backgroundColor: ['#28a745', '#ffc107']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.display-4 {
    font-weight: 700;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

@media print {
    .btn, .card-header .btn {
        display: none !important;
    }
}
</style>
@endsection