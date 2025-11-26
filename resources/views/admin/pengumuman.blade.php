@extends('layouts.admin')

@section('title', 'Pengumuman Hasil - Admin')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="ti ti-trophy fs-1"></i>
                        </div>
                        <div>
                            <h4 class="mb-1">🏆 Pengumuman Hasil Seleksi</h4>
                            <p class="mb-0 opacity-75">Kelola pengumuman hasil seleksi untuk pendaftar yang sudah menyelesaikan pembayaran</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="text-success mb-3">
                        <i class="ti ti-check-circle" style="font-size: 2.5rem;"></i>
                    </div>
                    <h4 class="text-success mb-1">{{ $pendaftar->where('status_akhir', 'LULUS')->count() }}</h4>
                    <p class="text-muted mb-0 fw-medium">Lulus</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="text-danger mb-3">
                        <i class="ti ti-x-circle" style="font-size: 2.5rem;"></i>
                    </div>
                    <h4 class="text-danger mb-1">{{ $pendaftar->where('status_akhir', 'TIDAK_LULUS')->count() }}</h4>
                    <p class="text-muted mb-0 fw-medium">Tidak Lulus</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="text-warning mb-3">
                        <i class="ti ti-clock" style="font-size: 2.5rem;"></i>
                    </div>
                    <h4 class="text-warning mb-1">{{ $pendaftar->where('status_akhir', 'CADANGAN')->count() }}</h4>
                    <p class="text-muted mb-0 fw-medium">Cadangan</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="text-secondary mb-3">
                        <i class="ti ti-hourglass" style="font-size: 2.5rem;"></i>
                    </div>
                    <h4 class="text-secondary mb-1">{{ $pendaftar->whereNull('status_akhir')->count() }}</h4>
                    <p class="text-muted mb-0 fw-medium">Belum Diumumkan</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ti ti-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">📋 Daftar Pendaftar yang Sudah Bayar</h5>
                    <small class="text-muted">Total: {{ $pendaftar->total() }} pendaftar</small>
                </div>
                <div>
                    <button class="btn btn-outline-primary btn-sm" onclick="location.reload()">
                        <i class="ti ti-refresh"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 ps-4">No Pendaftaran</th>
                            <th class="border-0">Nama Lengkap</th>
                            <th class="border-0">Jurusan</th>
                            <th class="border-0">Status Pembayaran</th>
                            <th class="border-0">Status Akhir</th>
                            <th class="border-0 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendaftar as $p)
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-primary">{{ $p->no_pendaftaran }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3">
                                        <i class="ti ti-user text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-semibold">{{ $p->nama }}</h6>
                                        <small class="text-muted">{{ $p->email ?? 'Email tidak tersedia' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info bg-opacity-10 text-info border border-info">{{ $p->jurusan->nama ?? 'Belum dipilih' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-success"><i class="ti ti-check me-1"></i>LUNAS</span>
                            </td>
                            <td>
                                @if($p->status_akhir)
                                    @if($p->status_akhir === 'LULUS')
                                        <span class="badge bg-success fs-6"><i class="ti ti-trophy me-1"></i>LULUS</span>
                                    @elseif($p->status_akhir === 'TIDAK_LULUS')
                                        <span class="badge bg-danger fs-6"><i class="ti ti-x me-1"></i>TIDAK LULUS</span>
                                    @else
                                        <span class="badge bg-warning fs-6"><i class="ti ti-clock me-1"></i>CADANGAN</span>
                                    @endif
                                    <br><small class="text-muted">{{ $p->tgl_pengumuman ? $p->tgl_pengumuman->format('d/m/Y H:i') : '-' }}</small>
                                @else
                                    <span class="badge bg-secondary"><i class="ti ti-hourglass me-1"></i>Belum Diumumkan</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if(!$p->status_akhir)
                                    <div class="d-flex justify-content-center gap-1">
                                        <button class="btn btn-success btn-sm px-3" onclick="setStatus({{ $p->id }}, 'LULUS')" title="Lulus">
                                            <i class="ti ti-check me-1"></i>Lulus
                                        </button>
                                        <button class="btn btn-danger btn-sm px-3" onclick="setStatus({{ $p->id }}, 'TIDAK_LULUS')" title="Tidak Lulus">
                                            <i class="ti ti-x me-1"></i>Tolak
                                        </button>
                                        <button class="btn btn-warning btn-sm px-3" onclick="setStatus({{ $p->id }}, 'CADANGAN')" title="Cadangan">
                                            <i class="ti ti-clock me-1"></i>Cadangan
                                        </button>
                                    </div>
                                @else
                                    <button class="btn btn-outline-primary btn-sm" onclick="changeStatus({{ $p->id }})" title="Ubah Status">
                                        <i class="ti ti-edit me-1"></i>Ubah
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="ti ti-inbox mb-3 d-block" style="font-size: 4rem; opacity: 0.3;"></i>
                                    <h5 class="mb-2">Belum ada pendaftar yang sudah bayar</h5>
                                    <p class="mb-0">Pendaftar akan muncul di sini setelah menyelesaikan pembayaran</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pendaftar->hasPages())
            <div class="card-footer bg-white border-top-0">
                {{ $pendaftar->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Enhanced Modal Set Status -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="ti ti-trophy me-2"></i>Konfirmasi Status Akhir</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusForm" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <input type="hidden" name="status_akhir" id="statusInput">
                    <div class="text-center mb-4">
                        <div id="statusIcon" class="mb-3"></div>
                        <h6 id="statusText" class="mb-2"></h6>
                        <p class="text-muted mb-0">Tindakan ini akan mengirim notifikasi otomatis ke pendaftar</p>
                    </div>
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        <strong>Perhatian:</strong> Status yang sudah ditetapkan masih bisa diubah jika diperlukan.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check me-1"></i>Konfirmasi & Kirim Notifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function setStatus(id, status) {
    document.getElementById('statusInput').value = status;
    document.getElementById('statusForm').action = `/admin/pengumuman/${id}`;
    
    let statusText = '';
    let statusIcon = '';
    
    if (status === 'LULUS') {
        statusText = 'Pendaftar ini akan dinyatakan LULUS';
        statusIcon = '<i class="ti ti-trophy text-success" style="font-size: 3rem;"></i>';
    } else if (status === 'TIDAK_LULUS') {
        statusText = 'Pendaftar ini akan dinyatakan TIDAK LULUS';
        statusIcon = '<i class="ti ti-x-circle text-danger" style="font-size: 3rem;"></i>';
    } else {
        statusText = 'Pendaftar ini akan dinyatakan CADANGAN';
        statusIcon = '<i class="ti ti-clock text-warning" style="font-size: 3rem;"></i>';
    }
    
    document.getElementById('statusText').textContent = statusText;
    document.getElementById('statusIcon').innerHTML = statusIcon;
    
    const modal = new bootstrap.Modal(document.getElementById('statusModal'));
    modal.show();
}

function changeStatus(id) {
    // Show options to change status
    const currentRow = event.target.closest('tr');
    const currentStatus = currentRow.querySelector('.badge').textContent.trim();
    
    if (confirm('Apakah Anda yakin ingin mengubah status pendaftar ini?')) {
        // You can customize this to show a selection modal
        setStatus(id, 'LULUS'); // Default to LULUS, can be enhanced
    }
}

// Auto refresh every 30 seconds
setInterval(function() {
    const refreshBtn = document.querySelector('[onclick="location.reload()"]');
    if (refreshBtn) {
        refreshBtn.innerHTML = '<i class="ti ti-refresh fa-spin"></i> Refreshing...';
        setTimeout(() => {
            refreshBtn.innerHTML = '<i class="ti ti-refresh"></i> Refresh';
        }, 1000);
    }
}, 30000);
</script>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.table tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.btn-group .btn {
    border-radius: 0.375rem !important;
    margin-right: 2px;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection