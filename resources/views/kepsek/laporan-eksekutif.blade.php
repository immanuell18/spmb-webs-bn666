@extends('layouts.admin')

@section('title', 'Laporan Eksekutif - Kepala Sekolah')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="mb-1">Laporan Eksekutif</h3>
            <p class="mb-0" style="color: var(--text3); font-size: 13px;">Ringkasan kinerja penerimaan murid baru</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('kepsek.laporan.export.excel') }}" class="btn btn-success btn-sm">
                <i class="ti ti-file-spreadsheet"></i> Export Excel
            </a>
            <a href="{{ route('kepsek.laporan.export.pdf') }}" class="btn btn-danger btn-sm">
                <i class="ti ti-file-type-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs text-primary text-uppercase mb-1">Total Pendaftar</div>
                            <div class="h5 mb-0">{{ $laporan['total_pendaftar'] }}</div>
                        </div>
                        <div style="width: 44px; height: 44px; background: var(--p-100); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i class="ti ti-users" style="font-size: 20px; color: var(--p);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs text-success text-uppercase mb-1">Rasio Verifikasi</div>
                            <div class="h5 mb-0">{{ number_format($laporan['rasio_verifikasi'], 1) }}%</div>
                        </div>
                        <div style="width: 44px; height: 44px; background: var(--ok-light); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i class="ti ti-circle-check" style="font-size: 20px; color: var(--ok);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs text-info text-uppercase mb-1">Rasio Pembayaran</div>
                            <div class="h5 mb-0">{{ number_format($laporan['rasio_pembayaran'], 1) }}%</div>
                        </div>
                        <div style="width: 44px; height: 44px; background: var(--info-light); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i class="ti ti-credit-card" style="font-size: 20px; color: var(--info);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs text-warning text-uppercase mb-1">Total Pemasukan</div>
                            <div class="h5 mb-0" style="font-size: 22px !important;">Rp {{ number_format($laporan['total_pemasukan'], 0, ',', '.') }}</div>
                        </div>
                        <div style="width: 44px; height: 44px; background: var(--warn-light); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i class="ti ti-cash" style="font-size: 20px; color: var(--warn);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Laporan per Gelombang -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex align-items-center justify-content-between">
            <h6 class="m-0 fw-bold" style="font-size: 16px;">Laporan per Gelombang</h6>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2">{{ $gelombang->count() }} gelombang</span>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Gelombang</th>
                            <th>Periode</th>
                            <th>Pendaftar</th>
                            <th>Terverifikasi</th>
                            <th>Terbayar</th>
                            <th>Pemasukan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gelombang as $g)
                        <tr>
                            <td style="font-weight: 600;">{{ $g->nama }}</td>
                            <td style="color: var(--text3);">{{ optional($g->tgl_mulai)->format('d M Y') }} - {{ optional($g->tgl_selesai)->format('d M Y') }}</td>
                            <td><span class="badge bg-primary">{{ $g->pendaftar->count() }}</span></td>
                            <td><span class="badge bg-success">{{ $g->pendaftar->whereIn('status', ['ADM_PASS', 'PAID'])->count() }}</span></td>
                            <td><span class="badge bg-info">{{ $g->pendaftar->where('status', 'PAID')->count() }}</span></td>
                            <td style="font-weight: 600; color: #059669;">Rp {{ number_format($g->pendaftar->where('status', 'PAID')->sum('biaya_pendaftaran'), 0, ',', '.') }}</td>
                            <td>
                                @if($g->tgl_selesai < now())
                                    <span class="badge bg-secondary">Selesai</span>
                                @elseif($g->tgl_mulai <= now())
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-warning">Belum Mulai</span>
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
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h6 class="m-0 fw-bold" style="font-size: 16px;">Metrik Performa</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1" style="font-size: 13px;">
                            <span style="color: var(--text2); font-weight: 500;">Tingkat Konversi (Daftar → Bayar)</span>
                            <span style="font-weight: 600;">{{ number_format($laporan['rasio_pembayaran'], 1) }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: {{ $laporan['rasio_pembayaran'] }}%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1" style="font-size: 13px;">
                            <span style="color: var(--text2); font-weight: 500;">Efisiensi Verifikasi</span>
                            <span style="font-weight: 600;">{{ number_format($laporan['rasio_verifikasi'], 1) }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-info" style="width: {{ $laporan['rasio_verifikasi'] }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-1" style="font-size: 13px;">
                            <span style="color: var(--text2); font-weight: 500;">Pencapaian Target</span>
                            @php $targetPersen = min(($laporan['total_pendaftar'] / max(300, 1)) * 100, 100); @endphp
                            <span style="font-weight: 600;">{{ number_format($targetPersen, 1) }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $targetPersen }}%; background: var(--p);"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h6 class="m-0 fw-bold" style="font-size: 16px;">Target vs Realisasi</h6>
                </div>
                <div class="card-body p-4 d-flex align-items-center justify-content-center">
                    <div style="position: relative; height: 300px; width: 100%; display: flex; justify-content: center;">
                        <canvas id="targetChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('targetChart').getContext('2d');
const g1 = ctx.createLinearGradient(0, 0, 0, 200);
g1.addColorStop(0, 'rgba(99,102,241,0.8)');
g1.addColorStop(1, 'rgba(99,102,241,0.3)');
const g2 = ctx.createLinearGradient(0, 0, 0, 200);
g2.addColorStop(0, 'rgba(16,185,129,0.8)');
g2.addColorStop(1, 'rgba(16,185,129,0.3)');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Target', 'Realisasi'],
        datasets: [{
            label: 'Pendaftar',
            data: [300, {{ $laporan['total_pendaftar'] }}],
            backgroundColor: [g1, g2],
            borderWidth: 0,
            borderRadius: 8,
            barThickness: 48
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#0f172a',
                titleFont: { family: 'Inter', size: 12, weight: '600' },
                bodyFont: { family: 'Inter', size: 12 },
                padding: 10,
                cornerRadius: 8
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { font: { family: 'Inter', size: 11 }, color: '#94a3b8' },
                grid: { color: '#f1f5f9', drawBorder: false }
            },
            x: {
                ticks: { font: { family: 'Inter', size: 12, weight: '500' }, color: '#334155' },
                grid: { display: false }
            }
        }
    }
});
</script>
@endsection