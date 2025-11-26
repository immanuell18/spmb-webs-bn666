@extends('layouts.admin')

@section('title', 'Verifikasi Administrasi - SPMB')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Verifikasi Administrasi</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pendaftar</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
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
                            <td>{{ $pendaftar->firstItem() + $index }}</td>
                            <td>{{ $p->no_pendaftaran }}</td>
                            <td>{{ $p->nama }}</td>
                            <td>{{ $p->email }}</td>
                            <td>{{ $p->jurusan->nama ?? '-' }}</td>
                            <td>
                                @php
                                    $berkasStatus = $p->getBerkasStatus();
                                    $badgeClass = 'secondary';
                                    if (str_contains($berkasStatus, 'Lengkap')) {
                                        $badgeClass = 'success';
                                    } elseif (str_contains($berkasStatus, 'Belum Lengkap')) {
                                        $badgeClass = 'warning';
                                    }
                                @endphp
                                <span class="badge badge-{{ $badgeClass }} text-dark">{{ $berkasStatus }}</span>
                            </td>
                            <td>
                                @php
                                    $statusColor = $p->getStatusBadgeColor();
                                    $bgColor = match($statusColor) {
                                        'success' => '#28a745',
                                        'danger' => '#dc3545', 
                                        'warning' => '#ffc107',
                                        'info' => '#17a2b8',
                                        'primary' => '#007bff',
                                        default => '#6c757d'
                                    };
                                    $textColor = in_array($statusColor, ['warning']) ? '#000' : '#fff';
                                @endphp
                                <span class="badge" style="background-color: {{ $bgColor }}; color: {{ $textColor }}; font-weight: bold;">
                                    {{ $p->getStatusLabel() }}
                                </span>
                            </td>
                            <td>{{ $p->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('verifikator.detail', $p->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Belum ada data pendaftar</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $pendaftar->links() }}
            </div>
        </div>
    </div>
</div>
@endsection