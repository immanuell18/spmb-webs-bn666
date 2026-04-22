@extends('layouts.admin')

@section('title', 'Dashboard - SPMB Admin')

@section('content')
<div class="container-fluid">
    <!-- Welcome -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="mb-1">Selamat Datang, {{ auth()->user()->name }} 👋</h3>
            <p class="mb-0" style="color: var(--text3); font-size: 13px;">Ringkasan pendaftaran, {{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
        <select class="form-select" id="gelombangFilter" style="max-width: 220px;">
            @foreach($statistikGelombang as $gel)
            <option value="{{ $gel->id }}" {{ $gelombangAktif && $gelombangAktif->id == $gel->id ? 'selected' : '' }}>
                {{ $gel->nama }} ({{ $gel->status }})
            </option>
            @endforeach
        </select>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-2">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div style="font-size: 12px; font-weight: 500; color: var(--text4); text-transform: uppercase; letter-spacing: .03em; margin-bottom: 6px;">Total Pendaftar</div>
                            <div class="total-pendaftar" style="font-size: 32px; font-weight: 800; color: var(--text); letter-spacing: -0.02em; line-height: 1;">{{ $totalPendaftar }}</div>
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
                            <div style="font-size: 12px; font-weight: 500; color: var(--text4); text-transform: uppercase; letter-spacing: .03em; margin-bottom: 6px;">Terverifikasi</div>
                            <div style="font-size: 32px; font-weight: 800; color: var(--text); letter-spacing: -0.02em; line-height: 1;">{{ $sudahVerifikasi }}</div>
                            <div class="mt-2">
                                <div class="progress" style="width: 100px;"><div class="progress-bar bg-success" style="width: {{ $totalPendaftar > 0 ? ($sudahVerifikasi/$totalPendaftar)*100 : 0 }}%"></div></div>
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
                            <div style="font-size: 12px; font-weight: 500; color: var(--text4); text-transform: uppercase; letter-spacing: .03em; margin-bottom: 6px;">Sudah Bayar</div>
                            <div style="font-size: 32px; font-weight: 800; color: var(--text); letter-spacing: -0.02em; line-height: 1;">{{ $sudahBayar }}</div>
                            <div class="mt-2" style="font-size: 12px; color: var(--text4);">Rp {{ number_format($sudahBayar * \App\Models\SystemSetting::getBiayaPendaftaran(), 0, ',', '.') }}</div>
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
                            <div style="font-size: 12px; font-weight: 500; color: var(--text4); text-transform: uppercase; letter-spacing: .03em; margin-bottom: 6px;">Menunggu</div>
                            <div style="font-size: 32px; font-weight: 800; color: var(--text); letter-spacing: -0.02em; line-height: 1;">{{ $menungguVerifikasi }}</div>
                            <div class="mt-2" style="font-size: 12px; color: #ef4444; font-weight: 500;">Perlu ditindaklanjuti</div>
                        </div>
                        <div style="width: 48px; height: 48px; background: #fef2f2; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="ti ti-clock" style="font-size: 22px; color: #ef4444;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions + Status Gelombang -->
    <div class="row mb-2">
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="mb-3" style="font-size: 16px; font-weight: 700;">Aksi Cepat</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{ route('admin.master-data') }}" style="text-decoration: none;">
                                <div style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 14px; padding: 20px; text-align: center; transition: all .2s;" onmouseover="this.style.borderColor='#6366f1'; this.style.background='#eef2ff'" onmouseout="this.style.borderColor='#e5e7eb'; this.style.background='#f8fafc'">
                                    <div style="width: 44px; height: 44px; background: #eef2ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;">
                                        <i class="ti ti-database" style="font-size: 20px; color: #6366f1;"></i>
                                    </div>
                                    <div style="font-weight: 600; font-size: 13px; color: #0f172a;">Master Data</div>
                                    <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">Kelola jurusan & gelombang</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin.monitoring-berkas') }}" style="text-decoration: none;">
                                <div style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 14px; padding: 20px; text-align: center; transition: all .2s;" onmouseover="this.style.borderColor='#10b981'; this.style.background='#ecfdf5'" onmouseout="this.style.borderColor='#e5e7eb'; this.style.background='#f8fafc'">
                                    <div style="width: 44px; height: 44px; background: #ecfdf5; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;">
                                        <i class="ti ti-file-check" style="font-size: 20px; color: #10b981;"></i>
                                    </div>
                                    <div style="font-weight: 600; font-size: 13px; color: #0f172a;">Monitoring Berkas</div>
                                    <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">Cek kelengkapan berkas</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin.pengumuman') }}" style="text-decoration: none;">
                                <div style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 14px; padding: 20px; text-align: center; transition: all .2s;" onmouseover="this.style.borderColor='#f59e0b'; this.style.background='#fffbeb'" onmouseout="this.style.borderColor='#e5e7eb'; this.style.background='#f8fafc'">
                                    <div style="width: 44px; height: 44px; background: #fffbeb; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;">
                                        <i class="ti ti-speakerphone" style="font-size: 20px; color: #f59e0b;"></i>
                                    </div>
                                    <div style="font-weight: 600; font-size: 13px; color: #0f172a;">Pengumuman</div>
                                    <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">Atur hasil kelulusan</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin.users.index') }}" style="text-decoration: none;">
                                <div style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 14px; padding: 20px; text-align: center; transition: all .2s;" onmouseover="this.style.borderColor='#06b6d4'; this.style.background='#ecfeff'" onmouseout="this.style.borderColor='#e5e7eb'; this.style.background='#f8fafc'">
                                    <div style="width: 44px; height: 44px; background: #ecfeff; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;">
                                        <i class="ti ti-users" style="font-size: 20px; color: #06b6d4;"></i>
                                    </div>
                                    <div style="font-weight: 600; font-size: 13px; color: #0f172a;">Kelola Akun</div>
                                    <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">Manage user & role</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('reports.index') }}" style="text-decoration: none;">
                                <div style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 14px; padding: 20px; text-align: center; transition: all .2s;" onmouseover="this.style.borderColor='#a855f7'; this.style.background='#faf5ff'" onmouseout="this.style.borderColor='#e5e7eb'; this.style.background='#f8fafc'">
                                    <div style="width: 44px; height: 44px; background: #faf5ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;">
                                        <i class="ti ti-file-export" style="font-size: 20px; color: #a855f7;"></i>
                                    </div>
                                    <div style="font-weight: 600; font-size: 13px; color: #0f172a;">Export Laporan</div>
                                    <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">Download PDF & Excel</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin.peta-sebaran') }}" style="text-decoration: none;">
                                <div style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 14px; padding: 20px; text-align: center; transition: all .2s;" onmouseover="this.style.borderColor='#ec4899'; this.style.background='#fdf2f8'" onmouseout="this.style.borderColor='#e5e7eb'; this.style.background='#f8fafc'">
                                    <div style="width: 44px; height: 44px; background: #fdf2f8; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;">
                                        <i class="ti ti-map" style="font-size: 20px; color: #ec4899;"></i>
                                    </div>
                                    <div style="font-weight: 600; font-size: 13px; color: #0f172a;">Peta Sebaran</div>
                                    <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">Lihat sebaran pendaftar</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="mb-3" style="font-size: 16px; font-weight: 700;">Status Gelombang</h6>
                    @foreach($statistikGelombang as $gel)
                    <div style="padding: 12px; background: #f8fafc; border-radius: 12px; margin-bottom: 8px;">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span style="font-weight: 600; font-size: 13px; color: var(--text);">{{ $gel->nama }}</span>
                            @if($gel->status == 'aktif')
                            <span class="badge bg-success">Aktif</span>
                            @else
                            <span class="badge bg-secondary">{{ ucfirst($gel->status) }}</span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center justify-content-between" style="font-size: 12px; color: var(--text3);">
                            <span>{{ $gel->pendaftar_count ?? $gel->pendaftar->count() ?? 0 }} pendaftar</span>
                            <span>{{ \Carbon\Carbon::parse($gel->tgl_mulai)->format('d M') }} - {{ \Carbon\Carbon::parse($gel->tgl_selesai)->format('d M Y') }}</span>
                        </div>
                    </div>
                    @endforeach

                    <div style="margin-top: 16px; padding: 14px; background: #0f172a; border-radius: 12px; color: #fff;">
                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: .04em; color: #94a3b8; margin-bottom: 4px;">Total Pemasukan</div>
                        <div class="total-pembayaran" style="font-size: 22px; font-weight: 800; letter-spacing: -0.01em;">
                            Rp {{ number_format($sudahBayar * \App\Models\SystemSetting::getBiayaPendaftaran(), 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pendaftar Terbaru — Full Width Table -->
    <div class="card mb-4">
        <div class="card-body" style="padding: 20px 24px !important;">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h6 style="font-size: 16px; font-weight: 700; margin-bottom: 2px;">Pendaftar Terbaru</h6>
                    <span style="font-size: 12px; color: var(--text4);">{{ $totalPendaftar }} total, data pendaftar yang baru masuk</span>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.export-excel') }}" class="btn btn-sm btn-secondary">
                        <i class="ti ti-download"></i> Export
                    </a>
                    <a href="{{ route('admin.master-data') }}" class="btn btn-sm btn-primary">
                        Lihat Semua <i class="ti ti-arrow-right" style="font-size: 14px;"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>No. Pendaftaran</th>
                        <th>Nama</th>
                        <th>Jurusan</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftarTerbaru as $p)
                    <tr>
                        <td style="font-weight: 600; color: #6366f1;">{{ $p->no_pendaftaran }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width: 32px; height: 32px; background: #eef2ff; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <span style="font-weight: 600; font-size: 12px; color: #6366f1;">{{ strtoupper(substr($p->nama, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <div style="font-weight: 500;">{{ $p->nama }}</div>
                                    <div style="font-size: 11px; color: var(--text4);">{{ $p->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $p->jurusan->nama ?? 'N/A' }}</td>
                        <td>
                            @php
                                $sc = ['PAID'=>['#ecfdf5','#065f46','Terbayar'], 'ADM_PASS'=>['#e0e7ff','#3730a3','Terverifikasi'], 'SUBMIT'=>['#fffbeb','#92400e','Pending']];
                                $s = $sc[$p->status] ?? ['#fef2f2','#991b1b','Ditolak'];
                            @endphp
                            <span class="badge" style="background: {{ $s[0] }}; color: {{ $s[1] }};">{{ $s[2] }}</span>
                        </td>
                        <td style="color: var(--text3);">{{ $p->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.monitoring-berkas') }}" class="btn btn-sm btn-secondary" style="padding: 4px 10px !important;">
                                <i class="ti ti-eye" style="font-size: 14px;"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 40px 16px !important;">
                            <i class="ti ti-inbox" style="font-size: 36px; color: var(--text4);"></i>
                            <p class="mt-2 mb-0" style="color: var(--text4);">Belum ada pendaftar</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection