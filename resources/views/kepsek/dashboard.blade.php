@extends('layouts.admin')

@section('title', 'Dashboard Kepala Sekolah - SPMB')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="mb-1">Dashboard Eksekutif 👨‍💼</h3>
            <p class="mb-0" style="color: var(--text3); font-size: 13px;">Ringkasan strategis penerimaan murid baru</p>
        </div>
        <select class="form-select" style="max-width: 220px;">
            <option value="">Semua Gelombang</option>
            @foreach(\App\Models\Gelombang::all() as $g)
            <option value="{{ $g->id }}">{{ $g->nama }}</option>
            @endforeach
        </select>
    </div>

    <!-- KPI -->
    <div class="row mb-2">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div style="font-size: 12px; font-weight: 500; color: var(--text4); text-transform: uppercase; margin-bottom: 6px;">Total Pendaftar</div>
                            <div style="font-size: 32px; font-weight: 800; color: var(--text); line-height: 1;">{{ $kpi['total_pendaftar'] }}</div>
                            <div style="font-size: 12px; color: #10b981; font-weight: 500; margin-top: 6px;">Target: {{ $kpi['total_kuota'] }}</div>
                        </div>
                        <div style="width: 48px; height: 48px; background: #eef2ff; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="ti ti-users" style="font-size: 22px; color: #6366f1;"></i>
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
                            <div style="font-size: 12px; font-weight: 500; color: var(--text4); text-transform: uppercase; margin-bottom: 6px;">Rasio Verifikasi</div>
                            <div style="font-size: 32px; font-weight: 800; color: var(--text); line-height: 1;">{{ number_format(($kpi['terverifikasi'] / max($kpi['total_pendaftar'], 1)) * 100, 1) }}%</div>
                            <div class="mt-2">
                                <div class="progress" style="width: 100px;"><div class="progress-bar bg-success" style="width: {{ ($kpi['terverifikasi'] / max($kpi['total_pendaftar'], 1)) * 100 }}%"></div></div>
                            </div>
                        </div>
                        <div style="width: 48px; height: 48px; background: #ecfdf5; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="ti ti-circle-check" style="font-size: 22px; color: #10b981;"></i>
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
                            <div style="font-size: 12px; font-weight: 500; color: var(--text4); text-transform: uppercase; margin-bottom: 6px;">Total Pemasukan</div>
                            <div style="font-size: 22px; font-weight: 800; color: var(--text); line-height: 1;">Rp {{ number_format($kpi['total_pemasukan'], 0, ',', '.') }}</div>
                            <div style="font-size: 12px; color: var(--text4); margin-top: 6px;">dari pembayaran</div>
                        </div>
                        <div style="width: 48px; height: 48px; background: #fffbeb; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="ti ti-cash" style="font-size: 22px; color: #f59e0b;"></i>
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
                            <div style="font-size: 12px; font-weight: 500; color: var(--text4); text-transform: uppercase; margin-bottom: 6px;">Pencapaian</div>
                            @php $pencapaian = min(($kpi['total_pendaftar'] / max($kpi['total_kuota'], 1)) * 100, 100); @endphp
                            <div style="font-size: 32px; font-weight: 800; color: var(--text); line-height: 1;">{{ number_format($pencapaian, 0) }}%</div>
                            <div class="mt-2">
                                <div class="progress" style="width: 100px;"><div class="progress-bar" style="width: {{ $pencapaian }}%; background: #6366f1;"></div></div>
                            </div>
                        </div>
                        <div style="width: 48px; height: 48px; background: #faf5ff; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="ti ti-target" style="font-size: 22px; color: #a855f7;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions + Per Jurusan -->
    <div class="row mb-2">
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-body" style="padding: 20px 24px !important;">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 style="font-size: 16px; font-weight: 700; margin: 0;">Pendaftar per Jurusan</h6>
                        <a href="{{ route('kepsek.laporan') }}" class="btn btn-sm btn-primary">Laporan Lengkap <i class="ti ti-arrow-right"></i></a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Jurusan</th>
                                <th>Kuota</th>
                                <th>Pendaftar</th>
                                <th>Terverifikasi</th>
                                <th>Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($perJurusan as $j)
                            <tr>
                                <td style="font-weight: 600;">{{ $j['nama'] }}</td>
                                <td>{{ $j['kuota'] }}</td>
                                <td><span class="badge bg-primary">{{ $j['pendaftar'] }}</span></td>
                                <td><span class="badge bg-success">{{ $j['terverifikasi'] }}</span></td>
                                <td style="min-width: 140px;">
                                    @php $prog = $j['kuota'] > 0 ? min(($j['pendaftar'] / $j['kuota']) * 100, 100) : 0; @endphp
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1"><div class="progress-bar" style="width: {{ $prog }}%; background: {{ $prog >= 80 ? '#10b981' : '#6366f1' }};"></div></div>
                                        <span style="font-size: 12px; font-weight: 600; color: var(--text2); min-width: 35px;">{{ number_format($prog, 0) }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h6 style="font-size: 16px; font-weight: 700; margin-bottom: 14px;">Akses Cepat</h6>
                    <a href="{{ route('kepsek.laporan') }}" class="d-flex align-items-center gap-3 mb-3" style="text-decoration: none; padding: 12px; background: #f8fafc; border-radius: 12px; transition: all .2s;" onmouseover="this.style.background='#faf5ff'" onmouseout="this.style.background='#f8fafc'">
                        <div style="width: 40px; height: 40px; background: #faf5ff; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="ti ti-report-analytics" style="font-size: 18px; color: #a855f7;"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 13px; color: #0f172a;">Laporan Eksekutif</div>
                            <div style="font-size: 11px; color: #94a3b8;">Download PDF & Excel</div>
                        </div>
                    </a>
                    <a href="{{ route('kepsek.peta-sebaran') }}" class="d-flex align-items-center gap-3" style="text-decoration: none; padding: 12px; background: #f8fafc; border-radius: 12px; transition: all .2s;" onmouseover="this.style.background='#ecfeff'" onmouseout="this.style.background='#f8fafc'">
                        <div style="width: 40px; height: 40px; background: #ecfeff; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="ti ti-map" style="font-size: 18px; color: #06b6d4;"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 13px; color: #0f172a;">Peta Sebaran</div>
                            <div style="font-size: 11px; color: #94a3b8;">Lihat sebaran pendaftar</div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="card">
                <div class="card-body" style="background: #0f172a; border-radius: 20px;">
                    <div style="font-size: 11px; text-transform: uppercase; letter-spacing: .04em; color: #94a3b8; margin-bottom: 8px;">Ringkasan Eksekutif</div>
                    <div style="font-size: 22px; font-weight: 800; color: #fff; margin-bottom: 14px;">
                        {{ $kpi['total_pendaftar'] }} <span style="font-size: 14px; font-weight: 400; color: #94a3b8;">/ {{ $kpi['total_kuota'] }} kuota</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color: #e2e8f0; font-size: 13px;">Terverifikasi</span>
                        <span style="color: #34d399; font-weight: 700;">{{ $kpi['terverifikasi'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color: #e2e8f0; font-size: 13px;">Sudah Bayar</span>
                        <span style="color: #60a5fa; font-weight: 700;">{{ $kpi['sudah_bayar'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color: #e2e8f0; font-size: 13px;">Pemasukan</span>
                        <span style="color: #fbbf24; font-weight: 700;">Rp {{ number_format($kpi['total_pemasukan'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection