@extends('layouts.admin')

@section('title', 'Verifikasi Administrasi - SPMB')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1" style="font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; color: #1e293b; letter-spacing: -0.025em;">
                Verifikasi Administrasi
            </h1>
            <p class="mb-0" style="color: #64748b; font-size: 0.9rem;">Daftar lengkap pendaftar untuk verifikasi administrasi</p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-primary" style="font-size: 0.82rem; padding: 8px 16px;">{{ $pendaftar->total() }} total pendaftar</span>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="m-0 fw-bold">Daftar Pendaftar</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Pendaftaran</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Jurusan</th>
                            <th>Status Berkas</th>
                            <th>Status</th>
                            <th>Tanggal Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendaftar as $index => $p)
                        <tr>
                            <td><span class="fw-semibold">{{ $pendaftar->firstItem() + $index }}</span></td>
                            <td><span class="fw-semibold" style="color: #4f46e5;">{{ $p->no_pendaftaran }}</span></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width: 34px; height: 34px; background: linear-gradient(135deg, #eef2ff, #e0e7ff); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <span style="font-weight: 700; font-size: 0.78rem; color: #4f46e5;">{{ strtoupper(substr($p->nama, 0, 1)) }}</span>
                                    </div>
                                    <span class="fw-semibold">{{ $p->nama }}</span>
                                </div>
                            </td>
                            <td style="color: #64748b; font-size: 0.85rem;">{{ $p->email }}</td>
                            <td>{{ $p->jurusan->nama ?? '-' }}</td>
                            <td>
                                @php
                                    $berkasStatus = $p->getBerkasStatus();
                                    $berkasBg = '#f1f5f9'; $berkasText = '#475569';
                                    if (str_contains($berkasStatus, 'Lengkap')) {
                                        $berkasBg = '#d1fae5'; $berkasText = '#065f46';
                                    } elseif (str_contains($berkasStatus, 'Belum Lengkap')) {
                                        $berkasBg = '#fef3c7'; $berkasText = '#92400e';
                                    }
                                @endphp
                                <span class="badge" style="background-color: {{ $berkasBg }}; color: {{ $berkasText }};">{{ $berkasStatus }}</span>
                            </td>
                            <td>
                                @php
                                    $statusColor = $p->getStatusBadgeColor();
                                    $bgColor = match($statusColor) {
                                        'success' => '#d1fae5', 'danger' => '#fee2e2', 
                                        'warning' => '#fef3c7', 'info' => '#cffafe',
                                        'primary' => '#e0e7ff', default => '#f1f5f9'
                                    };
                                    $textColor = match($statusColor) {
                                        'success' => '#065f46', 'danger' => '#991b1b', 
                                        'warning' => '#92400e', 'info' => '#155e75',
                                        'primary' => '#3730a3', default => '#475569'
                                    };
                                @endphp
                                <span class="badge" style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                                    {{ $p->getStatusLabel() }}
                                </span>
                            </td>
                            <td style="color: #64748b; font-size: 0.85rem;">{{ $p->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('verifikator.detail', $p->id) }}" class="btn btn-primary btn-sm">
                                    <i class="ti ti-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">
                                <div style="padding: 40px 0;">
                                    <i class="ti ti-inbox" style="font-size: 2.5rem; color: #cbd5e1;"></i>
                                    <p class="mt-2 mb-0" style="color: #94a3b8;">Belum ada data pendaftar</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $pendaftar->links() }}
            </div>
        </div>
    </div>
</div>
@endsection