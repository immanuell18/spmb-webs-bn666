@extends('layouts.admin')

@section('title', 'Advanced Dashboard - SPMB Admin')

@section('content')
<div class="container-fluid">
    <!-- Refresh Indicator -->
    <div id="refresh-indicator" class="position-fixed top-0 end-0 m-3 alert alert-info" style="display: none; z-index: 9999;">
        <i class="ti ti-refresh fa-spin me-2"></i>Dashboard Updated
    </div>

    <!-- KPI Cards Row -->
    <div class="row mb-4 g-3">
        <div class="col-md-2">
            <div class="card bg-primary text-white h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <i class="ti ti-users fs-1 mb-2"></i>
                    <h3 id="total-pendaftar" class="mb-1">{{ $kpi['total_pendaftar'] }}</h3>
                    <p class="mb-2 small">Total Pendaftar</p>
                    <div class="progress mt-auto" style="height: 4px;">
                        <div id="kuota-progress" class="progress-bar bg-light" style="width: {{ $kpi['progress_percentage'] }}%"></div>
                    </div>
                    <small class="mt-1">{{ $kpi['progress_percentage'] }}% dari target</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <i class="ti ti-check fs-1 mb-2"></i>
                    <h3 id="sudah-verifikasi" class="mb-1">{{ $kpi['sudah_verifikasi'] }}</h3>
                    <p class="mb-0 small">Terverifikasi</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <i class="ti ti-credit-card fs-1 mb-2"></i>
                    <h3 id="sudah-bayar" class="mb-1">{{ $kpi['sudah_bayar'] }}</h3>
                    <p class="mb-0 small">Sudah Bayar</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-white h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <i class="ti ti-percentage fs-1 mb-2"></i>
                    <h3 id="conversion-rate" class="mb-1">{{ $kpi['conversion_rate'] }}%</h3>
                    <p class="mb-0 small">Conversion Rate</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-dark h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <i class="ti ti-currency-dollar fs-1 mb-2 text-white"></i>
                    <h3 id="total-revenue" class="mb-1 text-white">Rp {{ number_format($kpi['total_revenue'], 0, ',', '.') }}</h3>
                    <p class="mb-0 small text-white">Total Revenue</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <button class="btn btn-light btn-sm mb-2" onclick="refreshDashboard()">
                        <i class="ti ti-refresh"></i> Refresh
                    </button>
                    <p class="mb-0 small">Real-time Data</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="card-title mb-0">📈 Tren Pendaftaran (30 Hari)</h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="exportChart('registrationTrend', 'registration-trend.png')">
                        <i class="ti ti-download"></i> Export
                    </button>
                </div>
                <div class="card-body">
                    <canvas id="registrationTrendChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">🎯 Distribusi Jurusan</h5>
                </div>
                <div class="card-body">
                    <canvas id="jurusanDistributionChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">💳 Payment Analytics</h5>
                </div>
                <div class="card-body">
                    <canvas id="paymentAnalyticsChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">💰 Revenue Trend</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Geographic & Performance Row -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">🗺️ Sebaran Geografis</h5>
                </div>
                <div class="card-body">
                    <canvas id="geographicChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">⚡ Performance Metrics</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Processing Time</small>
                        <h6 id="avg-processing-time">{{ $performanceMetrics['avg_processing_time'] }}</h6>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Active Users Today</small>
                        <h6 id="active-users">{{ $performanceMetrics['active_users_today'] }}</h6>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Error Rate</small>
                        <h6 id="error-rate">{{ $performanceMetrics['error_rate'] }}</h6>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Database Size</small>
                        <h6 id="database-size">{{ $performanceMetrics['database_size'] }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Usage Row -->
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6>CPU Usage</h6>
                    <div class="progress">
                        <div id="cpu-usage" class="progress-bar bg-primary" style="width: 0%">{{ $performanceMetrics['system_usage']['cpu_usage'] ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6>Memory Usage</h6>
                    <div class="progress">
                        <div id="memory-usage" class="progress-bar bg-warning" style="width: 0%">{{ $performanceMetrics['system_usage']['memory_usage'] ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6>Disk Usage</h6>
                    <div class="progress">
                        <div id="disk-usage" class="progress-bar bg-success" style="width: {{ str_replace('%', '', $performanceMetrics['system_usage']['disk_usage'] ?? '0') }}%">{{ $performanceMetrics['system_usage']['disk_usage'] ?? 'N/A' }}</div>
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
document.addEventListener('DOMContentLoaded', function() {
    // Payment Analytics Chart
    const paymentCtx = document.getElementById('paymentAnalyticsChart');
    if (paymentCtx) {
        new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: ['Paid', 'Pending', 'Failed'],
                datasets: [{
                    data: [{{ $paymentAnalytics['paid_transactions'] ?? 0 }}, {{ $paymentAnalytics['pending_transactions'] ?? 0 }}, {{ $paymentAnalytics['failed_transactions'] ?? 0 }}],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
    
    // Revenue Trend Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        const revenueData = @json($paymentAnalytics['revenue_trend'] ?? ['labels' => [], 'data' => []]);
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: revenueData.labels,
                datasets: [{
                    label: 'Revenue (Rp)',
                    data: revenueData.data,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Registration Trend Chart
    const registrationCtx = document.getElementById('registrationTrendChart');
    if (registrationCtx) {
        const trendData = @json($registrationTrend ?? ['labels' => [], 'data' => []]);
        new Chart(registrationCtx, {
            type: 'line',
            data: {
                labels: trendData.labels,
                datasets: [{
                    label: 'Pendaftar',
                    data: trendData.data,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
    
    // Jurusan Distribution Chart
    const jurusanCtx = document.getElementById('jurusanDistributionChart');
    if (jurusanCtx) {
        const jurusanData = @json($jurusanDistribution ?? ['labels' => [], 'data' => [], 'colors' => []]);
        new Chart(jurusanCtx, {
            type: 'pie',
            data: {
                labels: jurusanData.labels,
                datasets: [{
                    data: jurusanData.data,
                    backgroundColor: jurusanData.colors.length > 0 ? jurusanData.colors : ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
    
    // Geographic Chart
    const geographicCtx = document.getElementById('geographicChart');
    if (geographicCtx) {
        const geoData = @json($geographicData ?? ['labels' => [], 'data' => []]);
        new Chart(geographicCtx, {
            type: 'bar',
            data: {
                labels: geoData.labels,
                datasets: [{
                    label: 'Pendaftar',
                    data: geoData.data,
                    backgroundColor: '#17a2b8'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
});

function refreshDashboard() {
    location.reload();
}

function exportChart(chartId, filename) {
    // Get the chart canvas
    const canvas = document.getElementById(chartId + 'Chart');
    if (canvas) {
        // Create download link
        const link = document.createElement('a');
        link.download = filename;
        link.href = canvas.toDataURL('image/png');
        link.click();
    }
}
</script>

<style>
.animate-update {
    animation: pulse 1s ease-in-out;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); color: #28a745; }
    100% { transform: scale(1); }
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

canvas {
    max-height: 400px;
}

.progress {
    height: 20px;
}

.progress-bar {
    transition: width 0.6s ease;
}
</style>
@endsection