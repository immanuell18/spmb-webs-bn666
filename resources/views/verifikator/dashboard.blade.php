@extends('layouts.admin')

@section('title', 'Dashboard Verifikator - SPMB')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Verifikator</h1>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Menunggu Verifikasi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['menunggu_verifikasi'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Terverifikasi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['terverifikasi'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Perlu Perbaikan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['perlu_perbaikan'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Sudah Bayar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['sudah_bayar'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Verifikasi Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pendaftar - Verifikasi Berkas</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Jurusan</th>
                            <th>Status Berkas</th>
                            <th>Tanggal Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendaftar_terbaru as $index => $p)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $p->nama }}</td>
                            <td>{{ $p->email }}</td>
                            <td>{{ $p->jurusan->nama ?? '-' }}</td>
                            <td>
                                @php
                                    $berkasStatus = $p->getBerkasStatus();
                                    $statusLabel = $p->getStatusLabel();
                                    
                                    // Badge untuk status berkas
                                    $berkasBgColor = '#6c757d';
                                    $berkasTextColor = '#fff';
                                    if (str_contains($berkasStatus, 'Lengkap')) {
                                        $berkasBgColor = '#28a745';
                                    } elseif (str_contains($berkasStatus, 'Belum Lengkap')) {
                                        $berkasBgColor = '#ffc107';
                                        $berkasTextColor = '#000';
                                    } else {
                                        $berkasBgColor = '#dc3545';
                                    }
                                    
                                    // Badge untuk status verifikasi
                                    $statusColor = $p->getStatusBadgeColor();
                                    $statusBgColor = match($statusColor) {
                                        'success' => '#28a745',
                                        'danger' => '#dc3545', 
                                        'warning' => '#ffc107',
                                        'info' => '#17a2b8',
                                        'primary' => '#007bff',
                                        default => '#6c757d'
                                    };
                                    $statusTextColor = in_array($statusColor, ['warning']) ? '#000' : '#fff';
                                @endphp
                                <div>
                                    <span class="badge" style="background-color: {{ $berkasBgColor }}; color: {{ $berkasTextColor }}; font-weight: bold;">{{ $berkasStatus }}</span><br>
                                    <small><span class="badge mt-1" style="background-color: {{ $statusBgColor }}; color: {{ $statusTextColor }}; font-weight: bold;">{{ $statusLabel }}</span></small>
                                </div>
                            </td>
                            <td>{{ $p->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('verifikator.detail', $p->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i> Verifikasi
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data pendaftar</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Verifikasi Modal -->
<div class="modal fade" id="verifikasiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Verifikasi Berkas Pendaftar</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Data Pendaftar</h6>
                        <p><strong>Nama:</strong> Ahmad Rizki</p>
                        <p><strong>Email:</strong> ahmad@email.com</p>
                        <p><strong>Jurusan:</strong> RPL</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Berkas Upload</h6>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between">
                                Foto 3x4 <a href="#" class="btn btn-sm btn-outline-primary">Lihat</a>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                KTP <a href="#" class="btn btn-sm btn-outline-primary">Lihat</a>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                Ijazah <a href="#" class="btn btn-sm btn-outline-primary">Lihat</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <hr>
                <div class="form-group">
                    <label>Catatan Verifikasi</label>
                    <textarea class="form-control" id="catatan" rows="3" placeholder="Berikan catatan untuk pendaftar..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="setStatus('lulus')">
                    <i class="fas fa-check"></i> Lulus
                </button>
                <button type="button" class="btn btn-warning" onclick="setStatus('perbaikan')">
                    <i class="fas fa-edit"></i> Perlu Perbaikan
                </button>
                <button type="button" class="btn btn-danger" onclick="setStatus('tolak')">
                    <i class="fas fa-times"></i> Tolak
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function verifikasiModal(id) {
    $('#verifikasiModal').modal('show');
}

function setStatus(status) {
    const catatan = $('#catatan').val();
    // Ajax call to update status
    alert('Status berhasil diupdate: ' + status);
    $('#verifikasiModal').modal('hide');
    location.reload();
}
</script>
@endsection