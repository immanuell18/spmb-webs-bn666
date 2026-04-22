@extends('layouts.admin')

@section('title', 'Dashboard Verifikator - SPMB')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="mb-1">Dashboard Verifikator <i class="bi bi-clipboard-data text-warning me-1"></i></h3>
            <p class="mb-0" style="color: var(--text3); font-size: 13px;">Kelola verifikasi berkas pendaftar</p>
        </div>
    </div>

    <!-- KPI -->
    <div class="row mb-2">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div style="font-size: 12px; font-weight: 500; color: var(--text4); text-transform: uppercase; margin-bottom: 6px;">Menunggu Verifikasi</div>
                            <div style="font-size: 32px; font-weight: 800; color: var(--text); line-height: 1;">{{ $stats['menunggu_verifikasi'] }}</div>
                            <div style="font-size: 12px; color: #ef4444; font-weight: 500; margin-top: 6px;">Perlu ditinjau</div>
                        </div>
                        <div style="width: 48px; height: 48px; background: #eef2ff; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="ti ti-clock" style="font-size: 22px; color: #6366f1;"></i>
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
                            <div style="font-size: 12px; font-weight: 500; color: var(--text4); text-transform: uppercase; margin-bottom: 6px;">Terverifikasi</div>
                            <div style="font-size: 32px; font-weight: 800; color: var(--text); line-height: 1;">{{ $stats['terverifikasi'] }}</div>
                            <div class="mt-2">
                                @php 
                                    $totalVerif = $stats['terverifikasi'] + $stats['menunggu_verifikasi'] + $stats['perlu_perbaikan']; 
                                    $percent = $totalVerif > 0 ? round(($stats['terverifikasi']/$totalVerif)*100) : 0;
                                @endphp
                                <div style="font-size: 12px; color: #10b981; font-weight: 500; margin-top: 6px;">
                                    <i class="ti ti-trending-up me-1"></i> {{ $percent }}% dari total berkas
                                </div>
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
                            <div style="font-size: 12px; font-weight: 500; color: var(--text4); text-transform: uppercase; margin-bottom: 6px;">Perlu Perbaikan</div>
                            <div style="font-size: 32px; font-weight: 800; color: var(--text); line-height: 1;">{{ $stats['perlu_perbaikan'] }}</div>
                            <div style="font-size: 12px; color: var(--text4); margin-top: 6px;">menunggu revisi</div>
                        </div>
                        <div style="width: 48px; height: 48px; background: #fffbeb; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="ti ti-alert-triangle" style="font-size: 22px; color: #f59e0b;"></i>
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
                        <div style="width: 48px; height: 48px; background: #ecfeff; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="ti ti-cash" style="font-size: 22px; color: #06b6d4;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-2">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 style="font-size: 16px; font-weight: 700; margin: 0;">Pendaftar Terbaru</h6>
                        <div class="d-flex gap-2">
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">{{ $stats['menunggu_verifikasi'] }} menunggu</span>
                            <a href="{{ route('verifikator.verifikasi') }}" class="btn btn-sm btn-primary">Lihat Semua <i class="ti ti-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive px-4 pb-4">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Jurusan</th>
                                <th>Status Berkas</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendaftar_terbaru as $p)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 32px; height: 32px; background: #fffbeb; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <span style="font-weight: 600; font-size: 12px; color: #92400e;">{{ strtoupper(substr($p->nama, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <div style="font-weight: 500;">{{ $p->nama }}</div>
                                            <div style="font-size: 11px; color: var(--text4);">{{ $p->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $p->jurusan->nama ?? '-' }}</td>
                                <td>
                                    @php
                                        $bs = $p->getBerkasStatus();
                                        $bBg = str_contains($bs, 'Lengkap') ? '#ecfdf5' : (str_contains($bs, 'Belum') ? '#fffbeb' : '#fef2f2');
                                        $bTx = str_contains($bs, 'Lengkap') ? '#065f46' : (str_contains($bs, 'Belum') ? '#92400e' : '#991b1b');
                                    @endphp
                                    <span class="badge" style="background: {{ $bBg }}; color: {{ $bTx }};">{{ $bs }}</span>
                                </td>
                                <td style="color: var(--text3);">{{ $p->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('verifikator.detail', $p->id) }}" class="btn btn-sm btn-primary" style="padding: 4px 10px !important;">
                                        <i class="ti ti-eye" style="font-size: 14px;"></i> Verifikasi
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="ti ti-users-minus fs-1 d-block mb-3 text-secondary" style="opacity: 0.5;"></i>
                                    <p class="mt-2 mb-0 fw-medium">Belum ada pendaftar baru</p>
                                    <small>Data pendaftar akan otomatis muncul di sini</small>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="col-lg-4 mb-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h6 style="font-size: 16px; font-weight: 700; margin-bottom: 14px;">Aksi Cepat</h6>
                    <a href="{{ route('verifikator.verifikasi') }}" class="d-flex align-items-center gap-3 mb-3" style="text-decoration: none; padding: 12px; background: #f8fafc; border-radius: 12px; transition: all .2s;" onmouseover="this.style.background='#eef2ff'" onmouseout="this.style.background='#f8fafc'">
                        <div style="width: 40px; height: 40px; background: #eef2ff; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="ti ti-file-check" style="font-size: 18px; color: #6366f1;"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 13px; color: #0f172a;">Verifikasi Berkas</div>
                            <div style="font-size: 11px; color: #94a3b8;">Periksa berkas pendaftar</div>
                        </div>
                    </a>
                    <a href="{{ route('verifikator.administrasi') }}" class="d-flex align-items-center gap-3" style="text-decoration: none; padding: 12px; background: #f8fafc; border-radius: 12px; transition: all .2s;" onmouseover="this.style.background='#ecfdf5'" onmouseout="this.style.background='#f8fafc'">
                        <div style="width: 40px; height: 40px; background: #ecfdf5; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="ti ti-clipboard-list" style="font-size: 18px; color: #10b981;"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 13px; color: #0f172a;">Administrasi</div>
                            <div style="font-size: 11px; color: #94a3b8;">Kelola data administrasi</div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="card">
                <div class="card-body" style="background: #0f172a; border-radius: 20px;">
                    <div style="font-size: 11px; text-transform: uppercase; letter-spacing: .04em; color: #94a3b8; margin-bottom: 8px;">Ringkasan Hari Ini</div>
                    <div class="d-flex justify-content-between mb-3">
                        <span style="color: #e2e8f0; font-size: 13px;">Terverifikasi</span>
                        <span style="color: #fff; font-weight: 700;">{{ $stats['terverifikasi'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span style="color: #e2e8f0; font-size: 13px;">Menunggu</span>
                        <span style="color: #fbbf24; font-weight: 700;">{{ $stats['menunggu_verifikasi'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color: #e2e8f0; font-size: 13px;">Perbaikan</span>
                        <span style="color: #fb923c; font-weight: 700;">{{ $stats['perlu_perbaikan'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection