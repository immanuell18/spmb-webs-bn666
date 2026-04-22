@extends('layouts.admin')

@section('title', 'Dashboard Keuangan - SPMB')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="mb-1">Dashboard Keuangan 💰</h3>
            <p class="mb-0" style="color: var(--text3); font-size: 13px;">Kelola dan verifikasi pembayaran pendaftar</p>
        </div>
        <a href="{{ route('keuangan.rekap') }}" class="btn btn-primary btn-sm">
            <i class="ti ti-download"></i> Export Laporan
        </a>
    </div>

    <!-- KPI -->
    <div class="row mb-2">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div style="font-size: 12px; font-weight: 500; color: var(--text4); text-transform: uppercase; margin-bottom: 6px;">Total Pemasukan</div>
                            <div style="font-size: 24px; font-weight: 800; color: var(--text); line-height: 1;">Rp {{ number_format($stats['total_pemasukan'], 0, ',', '.') }}</div>
                            <div style="font-size: 12px; color: var(--text4); margin-top: 6px;">Semua gelombang</div>
                        </div>
                        <div style="width: 48px; height: 48px; background: #ecfdf5; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="ti ti-cash" style="font-size: 22px; color: #10b981;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div style="font-size: 12px; font-weight: 500; color: var(--text4); text-transform: uppercase; margin-bottom: 6px;">Sudah Bayar</div>
                            <div style="font-size: 32px; font-weight: 800; color: var(--text); line-height: 1;">{{ $stats['sudah_bayar'] }}</div>
                            <div style="font-size: 12px; color: var(--text4); margin-top: 6px;">pendaftar</div>
                        </div>
                        <div style="width: 48px; height: 48px; background: #eef2ff; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="ti ti-circle-check" style="font-size: 22px; color: #6366f1;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div style="font-size: 12px; font-weight: 500; color: var(--text4); text-transform: uppercase; margin-bottom: 6px;">Menunggu Validasi</div>
                            <div style="font-size: 32px; font-weight: 800; color: var(--text); line-height: 1;">{{ $pembayaranPending->count() }}</div>
                            <div style="font-size: 12px; color: #ef4444; font-weight: 500; margin-top: 6px;">Perlu tindakan!</div>
                        </div>
                        <div style="width: 48px; height: 48px; background: #fffbeb; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="ti ti-clock" style="font-size: 22px; color: #f59e0b;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div style="font-size: 12px; font-weight: 500; color: var(--text4); text-transform: uppercase; margin-bottom: 6px;">Belum Bayar</div>
                            <div style="font-size: 32px; font-weight: 800; color: var(--text); line-height: 1;">{{ $stats['belum_bayar'] }}</div>
                            <div style="font-size: 12px; color: var(--text4); margin-top: 6px;">pendaftar</div>
                        </div>
                        <div style="width: 48px; height: 48px; background: #fef2f2; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="ti ti-alert-circle" style="font-size: 22px; color: #ef4444;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions + Verifikasi Table -->
    <div class="row mb-2">
        <div class="col-lg-8 mb-4">
            <!-- Tren Pembayaran Chart -->
            <div class="card mb-4">
                <div class="card-body" style="padding: 20px 24px !important;">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 style="font-size: 16px; font-weight: 700; margin: 0;">Tren Pembayaran (7 Hari Terakhir)</h6>
                    </div>
                    <div id="chartPemasukan" style="min-height: 250px;"></div>
                </div>
            </div>

            <!-- Verifikasi Pembayaran Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 style="font-size: 16px; font-weight: 700; margin: 0;">Verifikasi Pembayaran</h6>
                        <div class="d-flex gap-2">
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">{{ $pembayaranPending->count() }} menunggu</span>
                            <a href="{{ route('keuangan.verifikasi') }}" class="btn btn-sm btn-primary">Lihat Semua <i class="ti ti-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive px-4 pb-4">
                    <table class="table mb-0 align-middle" style="font-size: 13px;">
                        <thead style="background: var(--bg);">
                            <tr>
                                <th style="padding-left: 24px;">Pendaftar</th>
                                <th>Jurusan</th>
                                <th>Nominal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pembayaranPending as $index => $p)
                            @if($index < 5)
                            <tr>
                                <td style="padding-left: 24px;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width: 36px; height: 36px; background: #ecfdf5; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <span style="font-weight: 600; font-size: 14px; color: #059669;">{{ strtoupper(substr($p->nama, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: var(--text);">{{ $p->nama }}</div>
                                            <div style="font-size: 11px; color: var(--text4);">{{ $p->no_pendaftaran }} • {{ $p->created_at->format('d M') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="color: var(--text3);">{{ Str::limit($p->jurusan->nama ?? '-', 15) }}</td>
                                <td style="font-weight: 600; color: var(--text);">Rp {{ number_format($p->gelombang->biaya_daftar ?? \App\Models\SystemSetting::getBiayaPendaftaran(), 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('keuangan.verifikasi') }}" class="btn btn-sm" style="background: #ecfdf5; color: #059669; font-weight: 600;">
                                        <i class="ti ti-check ms-1"></i> Cek
                                    </a>
                                </td>
                            </tr>
                            @endif
                            @empty
                            <tr>
                                <td colspan="4" class="text-center" style="padding: 60px 16px !important;">
                                    <div style="width: 64px; height: 64px; background: #ecfdf5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
                                        <i class="ti ti-mood-check" style="font-size: 32px; color: #10b981;"></i>
                                    </div>
                                    <h6 style="font-weight: 600; margin-bottom: 4px;">Antrean Kosong</h6>
                                    <p class="mb-0" style="color: var(--text4); font-size: 13px;">Semua pembayaran sudah berhasil diverifikasi! <i class="bi bi-award-fill text-success me-1"></i></p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h6 style="font-size: 16px; font-weight: 700; margin-bottom: 14px;">Aksi Cepat</h6>
                    <a href="{{ route('keuangan.verifikasi') }}" class="d-flex align-items-center gap-3 mb-3" style="text-decoration: none; padding: 12px; background: #f8fafc; border-radius: 12px; transition: all .2s;" onmouseover="this.style.background='#ecfdf5'" onmouseout="this.style.background='#f8fafc'">
                        <div style="width: 40px; height: 40px; background: #ecfdf5; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="ti ti-file-dollar" style="font-size: 18px; color: #10b981;"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 13px; color: #0f172a;">Verifikasi Pembayaran</div>
                            <div style="font-size: 11px; color: #94a3b8;">Validasi bukti bayar</div>
                        </div>
                    </a>
                    <a href="{{ route('keuangan.rekap') }}" class="d-flex align-items-center gap-3" style="text-decoration: none; padding: 12px; background: #f8fafc; border-radius: 12px; transition: all .2s;" onmouseover="this.style.background='#eef2ff'" onmouseout="this.style.background='#f8fafc'">
                        <div style="width: 40px; height: 40px; background: #eef2ff; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="ti ti-report-money" style="font-size: 18px; color: #6366f1;"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 13px; color: #0f172a;">Rekap Keuangan</div>
                            <div style="font-size: 11px; color: #94a3b8;">Lihat laporan & export</div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="card">
                <div class="card-body" style="background: #0f172a; border-radius: 20px;">
                    <div style="font-size: 11px; text-transform: uppercase; letter-spacing: .04em; color: #94a3b8; margin-bottom: 8px;">Ringkasan Keuangan</div>
                    <div style="font-size: 22px; font-weight: 800; color: #fff; margin-bottom: 14px;">
                        Rp {{ number_format($stats['total_pemasukan'], 0, ',', '.') }}
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color: #e2e8f0; font-size: 13px;">Sudah Bayar</span>
                        <span style="color: #34d399; font-weight: 700;">{{ $stats['sudah_bayar'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color: #e2e8f0; font-size: 13px;">Menunggu</span>
                        <span style="color: #fbbf24; font-weight: 700;">{{ $pembayaranPending->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color: #e2e8f0; font-size: 13px;">Belum Bayar</span>
                        <span style="color: #fb7185; font-weight: 700;">{{ $stats['belum_bayar'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    // Prepare data for the chart from $trenPembayaran
    $labels = $trenPembayaran->pluck('tanggal')->map(fn($t) => \Carbon\Carbon::parse($t)->format('d M'))->toJson();
    $values = $trenPembayaran->pluck('jumlah')->toJson();
@endphp
<script>
document.addEventListener("DOMContentLoaded", function() {
    if (typeof ApexCharts !== 'undefined') {
        var options = {
            series: [{
                name: "Verifikasi Berhasil",
                data: {!! $values !!}
            }],
            chart: {
                type: 'area',
                height: 250,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            colors: ['#10b981'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            xaxis: {
                categories: {!! $labels !!},
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: '#94a3b8', fontSize: '11px' } }
            },
            yaxis: {
                labels: { style: { colors: '#94a3b8', fontSize: '11px' } }
            },
            grid: {
                borderColor: '#e2e8f0',
                strokeDashArray: 4,
                yaxis: { lines: { show: true } },
                xaxis: { lines: { show: false } },
            },
            tooltip: { theme: 'light' }
        };

        var chart = new ApexCharts(document.querySelector("#chartPemasukan"), options);
        chart.render();
    }
});
</script>
@endsection