@extends('layouts.admin')

@section('title', 'Verifikasi Berkas - SPMB')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="mb-1">Verifikasi Berkas</h3>
            <p class="mb-0" style="color: var(--text3); font-size: 13px;">Filter dan kelola verifikasi berkas pendaftar</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="SUBMIT" {{ request('status') == 'SUBMIT' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="ADM_PASS" {{ request('status') == 'ADM_PASS' ? 'selected' : '' }}>Lulus Administrasi</option>
                        <option value="ADM_REJECT" {{ request('status') == 'ADM_REJECT' ? 'selected' : '' }}>Ditolak</option>
                        <option value="PAID" {{ request('status') == 'PAID' ? 'selected' : '' }}>Sudah Bayar</option>
                        <option value="LULUS" {{ request('status') == 'LULUS' ? 'selected' : '' }}>LULUS</option>
                        <option value="TIDAK_LULUS" {{ request('status') == 'TIDAK_LULUS' ? 'selected' : '' }}>TIDAK LULUS</option>
                        <option value="CADANGAN" {{ request('status') == 'CADANGAN' ? 'selected' : '' }}>CADANGAN</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Pencarian</label>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ti ti-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="m-0 fw-semibold">Daftar Pendaftar</h6>
            <span class="badge bg-primary">{{ $pendaftar->total() }} data</span>
        </div>
        <div class="card-body" style="padding: 0 !important;">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Pendaftaran</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Jurusan</th>
                            <th>Berkas</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendaftar as $index => $p)
                        <tr>
                            <td>{{ $pendaftar->firstItem() + $index }}</td>
                            <td style="font-weight: 600; color: var(--p);">{{ $p->no_pendaftaran }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width: 30px; height: 30px; background: var(--p-100); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <span style="font-weight: 600; font-size: 11px; color: var(--p);">{{ strtoupper(substr($p->nama, 0, 1)) }}</span>
                                    </div>
                                    <span style="font-weight: 500;">{{ $p->nama }}</span>
                                </div>
                            </td>
                            <td style="color: var(--text3);">{{ $p->email }}</td>
                            <td>{{ $p->jurusan->nama ?? '-' }}</td>
                            <td>
                                @php
                                    $bs = $p->getBerkasStatus();
                                    $bBg = str_contains($bs, 'Lengkap') ? 'var(--ok-light)' : (str_contains($bs, 'Belum') ? 'var(--warn-light)' : 'var(--bg2)');
                                    $bTx = str_contains($bs, 'Lengkap') ? '#065f46' : (str_contains($bs, 'Belum') ? '#92400e' : 'var(--text3)');
                                @endphp
                                <span class="badge" style="background: {{ $bBg }}; color: {{ $bTx }};">{{ $bs }}</span>
                            </td>
                            <td>
                                @php
                                    $sc = $p->getStatusBadgeColor();
                                    $sBg = match($sc) { 'success'=>'var(--ok-light)', 'danger'=>'var(--err-light)', 'warning'=>'var(--warn-light)', 'info'=>'var(--info-light)', 'primary'=>'var(--p-100)', default=>'var(--bg2)' };
                                    $sTx = match($sc) { 'success'=>'#065f46', 'danger'=>'#991b1b', 'warning'=>'#92400e', 'info'=>'#155e75', 'primary'=>'var(--p-dark)', default=>'var(--text3)' };
                                @endphp
                                <span class="badge" style="background: {{ $sBg }}; color: {{ $sTx }};">{{ $p->getStatusLabel() }}</span>
                            </td>
                            <td style="color: var(--text3);">{{ $p->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('verifikator.detail', $p->id) }}" class="btn btn-primary btn-sm">
                                    <i class="ti ti-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center" style="padding: 40px 14px !important;">
                                <i class="ti ti-inbox" style="font-size: 36px; color: var(--text4);"></i>
                                <p class="mt-2 mb-0" style="color: var(--text4);">Tidak ada data pendaftar</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pendaftar->hasPages())
            <div class="d-flex justify-content-center" style="padding: 16px;">
                {{ $pendaftar->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection