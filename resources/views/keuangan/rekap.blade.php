@extends('layouts.admin')

@section('title', 'Rekap Keuangan - Keuangan')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1" style="font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; color: #1e293b; letter-spacing: -0.025em;">
                Rekap Keuangan 📊
            </h1>
            <p class="mb-0" style="color: #64748b; font-size: 0.9rem;">Laporan pemasukan dan status pembayaran</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('keuangan.export.excel') }}" class="btn btn-success btn-sm">
                <i class="ti ti-file-spreadsheet"></i> Export Excel
            </a>
            <a href="{{ route('keuangan.export.pdf') }}" class="btn btn-danger btn-sm">
                <i class="ti ti-file-type-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
            <h6 class="m-0 fw-bold" style="font-size: 16px;"><i class="ti ti-filter me-1 text-primary"></i> Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label style="font-weight: 600; font-size: 0.85rem; color: #374151; margin-bottom: 6px;">Gelombang</label>
                        <select name="gelombang" class="form-select">
                            <option value="">Semua Gelombang</option>
                            @foreach($gelombang as $g)
                            <option value="{{ $g->id }}" {{ request('gelombang') == $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label style="font-weight: 600; font-size: 0.85rem; color: #374151; margin-bottom: 6px;">Jurusan</label>
                        <select name="jurusan" class="form-select">
                            <option value="">Semua Jurusan</option>
                            @foreach($jurusan as $j)
                            <option value="{{ $j->id }}" {{ request('jurusan') == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label style="font-weight: 600; font-size: 0.85rem; color: #374151; margin-bottom: 6px;">Periode</label>
                        <input type="month" name="periode" class="form-control" value="{{ request('periode') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-filter"></i> Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Rekap Table -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex align-items-center justify-content-between">
            <h6 class="m-0 fw-bold" style="font-size: 16px;">Laporan Pemasukan</h6>
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2">
                @php $total_pemasukan = 0; @endphp
                @foreach($rekap as $r) @php $total_pemasukan += $r->total_pemasukan; @endphp @endforeach
                Total: Rp {{ number_format($total_pemasukan, 0, ',', '.') }}
            </span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Gelombang</th>
                            <th>Jurusan</th>
                            <th>Total Pendaftar</th>
                            <th>Sudah Bayar</th>
                            <th>Total Pemasukan</th>
                            <th style="width: 180px;">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total_pemasukan = 0; @endphp
                        @foreach($rekap as $r)
                        <tr>
                            <td><span class="fw-semibold">{{ $r->gelombang->nama ?? '-' }}</span></td>
                            <td>{{ $r->jurusan->nama ?? '-' }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $r->total_pendaftar }}</span>
                            </td>
                            <td>
                                <span class="badge bg-success">{{ $r->sudah_bayar }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold" style="color: #059669;">Rp {{ number_format($r->total_pemasukan, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                @php 
                                    $persentase = $r->total_pendaftar > 0 ? ($r->sudah_bayar / $r->total_pendaftar) * 100 : 0;
                                    $total_pemasukan += $r->total_pemasukan;
                                    $barColor = $persentase >= 75 ? 'linear-gradient(90deg, #059669, #34d399)' : ($persentase >= 50 ? 'linear-gradient(90deg, #d97706, #fbbf24)' : 'linear-gradient(90deg, #dc2626, #f87171)');
                                @endphp
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 8px;">
                                        <div class="progress-bar" style="width: {{ $persentase }}%; background: {{ $barColor }};"></div>
                                    </div>
                                    <small style="font-weight: 700; color: #1e293b; min-width: 40px; text-align: right;">{{ number_format($persentase, 1) }}%</small>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background: #f8fafc;">
                            <td colspan="4" style="font-weight: 700; color: #1e293b; font-size: 0.9rem;">Total Keseluruhan</td>
                            <td style="font-weight: 800; color: #059669; font-size: 0.95rem;">Rp {{ number_format($total_pemasukan, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h6 class="m-0 fw-bold" style="font-size: 16px;">Pemasukan per Jurusan</h6>
                    <small style="color: #94a3b8;">Distribusi pendapatan berdasarkan program keahlian</small>
                </div>
                <div class="card-body p-4">
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="jurusanChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h6 class="m-0 fw-bold" style="font-size: 16px;">Status Pembayaran</h6>
                    <small style="color: #94a3b8;">Rasio sudah bayar vs belum bayar</small>
                </div>
                <div class="card-body p-4 d-flex align-items-center justify-content-center">
                    <div style="position: relative; height: 300px; width: 100%; display: flex; justify-content: center;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart implementation
const jurusanData = @json($rekap->pluck('jurusan.nama'));
const pemasukanData = @json($rekap->pluck('total_pemasukan'));

// Jurusan Chart
const jurusanCtx = document.getElementById('jurusanChart').getContext('2d');
const barGradient = jurusanCtx.createLinearGradient(0, 0, 0, 250);
barGradient.addColorStop(0, 'rgba(79, 70, 229, 0.8)');
barGradient.addColorStop(1, 'rgba(129, 140, 248, 0.3)');

new Chart(jurusanCtx, {
    type: 'bar',
    data: {
        labels: jurusanData,
        datasets: [{
            label: 'Pemasukan',
            data: pemasukanData,
            backgroundColor: barGradient,
            borderColor: '#4f46e5',
            borderWidth: 0,
            borderRadius: 8,
            borderSkipped: false,
            barThickness: 36
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1e293b',
                titleFont: { family: 'Inter', size: 12, weight: '600' },
                bodyFont: { family: 'Inter', size: 12 },
                padding: 12,
                cornerRadius: 10,
                callbacks: {
                    label: function(ctx) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y); }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    font: { family: 'Inter', size: 11, weight: '500' },
                    color: '#94a3b8',
                    callback: function(value) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(value); }
                },
                grid: { color: '#f1f5f9', drawBorder: false }
            },
            x: {
                ticks: {
                    font: { family: 'Inter', size: 11, weight: '500' },
                    color: '#64748b'
                },
                grid: { display: false }
            }
        }
    }
});

// Status Pembayaran Chart
const totalSudahBayar = @json($rekap->sum('sudah_bayar'));
const totalPendaftar = @json($rekap->sum('total_pendaftar'));
const belumBayar = totalPendaftar - totalSudahBayar;

new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Sudah Bayar', 'Belum Bayar'],
        datasets: [{
            data: [totalSudahBayar, belumBayar],
            backgroundColor: ['#10b981', '#ef4444'],
            borderWidth: 0,
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true,
                    pointStyle: 'circle',
                    font: { family: 'Inter', size: 12, weight: '500' }
                }
            },
            tooltip: {
                backgroundColor: '#1e293b',
                titleFont: { family: 'Inter', size: 12, weight: '600' },
                bodyFont: { family: 'Inter', size: 12 },
                padding: 12,
                cornerRadius: 10,
                callbacks: {
                    label: function(ctx) { return ctx.label + ': ' + ctx.parsed + ' orang'; }
                }
            }
        }
    }
});
</script>
@endsection