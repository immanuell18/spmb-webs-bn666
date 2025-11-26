@extends('layouts.admin')

@section('title', 'Monitoring Berkas - SPMB Admin')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title fw-semibold mb-0">Monitoring Berkas Pendaftar</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.export-excel') }}" class="btn btn-success">
                        <i class="ti ti-download"></i> Export Excel
                    </a>
                    <a href="{{ route('admin.export-pdf') }}" class="btn btn-primary">
                        <i class="ti ti-file-text"></i> Export PDF
                    </a>
                </div>
            </div>
            
            <!-- Filter Section -->
            <form method="GET" action="{{ route('admin.monitoring-berkas') }}">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <select class="form-select" name="jurusan" onchange="this.form.submit()">
                            <option value="">Semua Jurusan</option>
                            @foreach($jurusan ?? [] as $j)
                                <option value="{{ $j->id }}" {{ request('jurusan') == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="gelombang" onchange="this.form.submit()">
                            <option value="">Semua Gelombang</option>
                            @foreach($gelombang ?? [] as $g)
                                <option value="{{ $g->id }}" {{ request('gelombang') == $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="status" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="SUBMIT" {{ request('status') == 'SUBMIT' ? 'selected' : '' }}>Submit</option>
                            <option value="ADM_PASS" {{ request('status') == 'ADM_PASS' ? 'selected' : '' }}>Lolos Administrasi</option>
                            <option value="ADM_REJECT" {{ request('status') == 'ADM_REJECT' ? 'selected' : '' }}>Ditolak Administrasi</option>
                            <option value="PAID" {{ request('status') == 'PAID' ? 'selected' : '' }}>Sudah Bayar</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Cari nama/no pendaftaran...">
                    </div>
                </div>
            </form>
            
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="text-white">{{ $totalPendaftar ?? 0 }}</h4>
                                    <p class="mb-0">Total Pendaftar</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="ti ti-users fs-6"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="text-white">{{ $berkasLengkap ?? 0 }}</h4>
                                    <p class="mb-0">Berkas Lengkap</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="ti ti-file-check fs-6"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="text-white">{{ $pendingReview ?? 0 }}</h4>
                                    <p class="mb-0">Pending Review</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="ti ti-clock fs-6"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="text-white">{{ $tidakLengkap ?? 0 }}</h4>
                                    <p class="mb-0">Tidak Lengkap</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="ti ti-file-x fs-6"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Data Table -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>No. Pendaftaran</th>
                            <th>Nama Lengkap</th>
                            <th>Jurusan</th>
                            <th>Gelombang</th>
                            <th>Kelengkapan Berkas</th>
                            <th>Status Verifikasi</th>
                            <th>Tanggal Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendaftar ?? [] as $p)
                        <tr>
                            <td><strong>{{ $p->no_pendaftaran }}</strong></td>
                            <td>
                                <div>
                                    <h6 class="mb-0">{{ $p->nama }}</h6>
                                    <small class="text-muted">{{ $p->email }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge" style="background-color: #17a2b8; color: #fff; font-weight: bold;">{{ $p->jurusan->kode ?? 'N/A' }}</span>
                            </td>
                            <td>{{ $p->gelombang->nama ?? 'N/A' }}</td>
                            <td>
                                @php
                                    $progress = 0;
                                    $color = 'danger';
                                    $text = 'Submit';
                                    
                                    if($p->status == 'ADM_PASS') {
                                        $progress = 75;
                                        $color = 'success';
                                        $text = 'Lolos Administrasi';
                                    } elseif($p->status == 'PAID') {
                                        $progress = 100;
                                        $color = 'success';
                                        $text = 'Sudah Bayar';
                                    } elseif($p->status == 'SUBMIT') {
                                        $progress = 25;
                                        $color = 'warning';
                                        $text = 'Submit';
                                    } elseif($p->status == 'ADM_REJECT') {
                                        $progress = 0;
                                        $color = 'danger';
                                        $text = 'Ditolak';
                                    }
                                @endphp
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-{{ $color }}" style="width: {{ $progress }}%">{{ $progress }}%</div>
                                </div>
                                <small class="text-{{ $color }}">{{ $text }}</small>
                            </td>
                            <td>
                                @php
                                    $statusStyles = [
                                        'ADM_PASS' => ['bg' => '#28a745', 'text' => '#fff', 'label' => 'Lolos Administrasi'],
                                        'SUBMIT' => ['bg' => '#ffc107', 'text' => '#000', 'label' => 'Menunggu Verifikasi'],
                                        'PAID' => ['bg' => '#28a745', 'text' => '#fff', 'label' => 'Sudah Bayar'],
                                        'default' => ['bg' => '#dc3545', 'text' => '#fff', 'label' => 'Ditolak']
                                    ];
                                    $style = $statusStyles[$p->status] ?? $statusStyles['default'];
                                @endphp
                                <span class="badge" style="background-color: {{ $style['bg'] }}; color: {{ $style['text'] }}; font-weight: bold;">{{ $style['label'] }}</span>
                            </td>
                            <td>{{ $p->created_at->format('d M Y') }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#detailModal{{ $p->id }}">
                                    <i class="ti ti-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#verifikasiModal{{ $p->id }}">
                                    <i class="ti ti-check"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada data pendaftar</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if(isset($pendaftar) && $pendaftar->hasPages())
            <div class="d-flex justify-content-center">
                {{ $pendaftar->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Verifikasi -->
@foreach($pendaftar ?? [] as $p)
<div class="modal fade" id="verifikasiModal{{ $p->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.verifikasi-berkas', $p->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Verifikasi Berkas - {{ $p->nama }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Status Verifikasi</label>
                        <select class="form-select" name="status" required>
                            <option value="">Pilih Status</option>
                            <option value="ADM_PASS">Lolos Administrasi</option>
                            <option value="ADM_REJECT">Ditolak Administrasi</option>
                            <option value="PAID">Sudah Bayar</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" name="catatan" rows="3" placeholder="Berikan catatan jika diperlukan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal{{ $p->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Berkas Pendaftar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Data Pribadi</h6>
                        <table class="table table-sm">
                            <tr><td>No. Pendaftaran</td><td>{{ $p->no_pendaftaran }}</td></tr>
                            <tr><td>Nama</td><td>{{ $p->nama }}</td></tr>
                            <tr><td>Email</td><td>{{ $p->email }}</td></tr>
                            <tr><td>Jurusan</td><td>{{ $p->jurusan->nama ?? 'N/A' }}</td></tr>
                            <tr><td>Gelombang</td><td>{{ $p->gelombang->nama ?? 'N/A' }}</td></tr>
                        </table>
                        
                        @if($p->dataSiswa)
                        <h6>Data Siswa</h6>
                        <table class="table table-sm">
                            <tr><td>NIK</td><td>{{ $p->dataSiswa->nik ?? '-' }}</td></tr>
                            <tr><td>NISN</td><td>{{ $p->dataSiswa->nisn ?? '-' }}</td></tr>
                            <tr><td>Jenis Kelamin</td><td>{{ $p->dataSiswa->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                            <tr><td>Tempat Lahir</td><td>{{ $p->dataSiswa->tmp_lahir ?? '-' }}</td></tr>
                            <tr><td>Tanggal Lahir</td><td>{{ $p->dataSiswa->tgl_lahir ?? '-' }}</td></tr>
                            <tr><td>Alamat</td><td>{{ $p->dataSiswa->alamat ?? '-' }}</td></tr>
                        </table>
                        @endif
                    </div>
                    <div class="col-md-6">
                        @if($p->dataOrtu)
                        <h6>Data Orang Tua</h6>
                        <table class="table table-sm">
                            <tr><td>Nama Ayah</td><td>{{ $p->dataOrtu->nama_ayah ?? '-' }}</td></tr>
                            <tr><td>Pekerjaan Ayah</td><td>{{ $p->dataOrtu->pekerjaan_ayah ?? '-' }}</td></tr>
                            <tr><td>HP Ayah</td><td>{{ $p->dataOrtu->hp_ayah ?? '-' }}</td></tr>
                            <tr><td>Nama Ibu</td><td>{{ $p->dataOrtu->nama_ibu ?? '-' }}</td></tr>
                            <tr><td>Pekerjaan Ibu</td><td>{{ $p->dataOrtu->pekerjaan_ibu ?? '-' }}</td></tr>
                            <tr><td>HP Ibu</td><td>{{ $p->dataOrtu->hp_ibu ?? '-' }}</td></tr>
                        </table>
                        @endif
                        
                        @if($p->asalSekolah)
                        <h6>Asal Sekolah</h6>
                        <table class="table table-sm">
                            <tr><td>NPSN</td><td>{{ $p->asalSekolah->npsn ?? '-' }}</td></tr>
                            <tr><td>Nama Sekolah</td><td>{{ $p->asalSekolah->nama_sekolah ?? '-' }}</td></tr>
                            <tr><td>Kabupaten</td><td>{{ $p->asalSekolah->kabupaten ?? '-' }}</td></tr>
                            <tr><td>Nilai Rata-rata</td><td>{{ $p->asalSekolah->nilai_rata ?? '-' }}</td></tr>
                        </table>
                        @endif
                        
                        <h6>Status Berkas</h6>
                        <div class="list-group">
                            <div class="list-group-item d-flex justify-content-between">
                                <span>Status</span>
                                @php
                                    $modalStatusStyles = [
                                        'SUBMIT' => ['bg' => '#ffc107', 'text' => '#000', 'label' => 'Menunggu Verifikasi'],
                                        'ADM_PASS' => ['bg' => '#28a745', 'text' => '#fff', 'label' => 'Lolos Administrasi'],
                                        'ADM_REJECT' => ['bg' => '#dc3545', 'text' => '#fff', 'label' => 'Ditolak Administrasi'],
                                        'PAID' => ['bg' => '#28a745', 'text' => '#fff', 'label' => 'Sudah Bayar'],
                                        'default' => ['bg' => '#6c757d', 'text' => '#fff', 'label' => $p->status]
                                    ];
                                    $modalStyle = $modalStatusStyles[$p->status] ?? $modalStatusStyles['default'];
                                @endphp
                                <span class="badge" style="background-color: {{ $modalStyle['bg'] }}; color: {{ $modalStyle['text'] }}; font-weight: bold;">{{ $modalStyle['label'] }}</span>
                            </div>
                            @if($p->user_verifikasi_adm)
                            <div class="list-group-item">
                                <strong>Diverifikasi oleh:</strong> {{ $p->user_verifikasi_adm }}<br>
                                <strong>Tanggal:</strong> {{ $p->tgl_verifikasi_adm ? $p->tgl_verifikasi_adm->format('d M Y H:i') : '-' }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Berkas yang diupload -->
                @if($p->berkas && $p->berkas->count() > 0)
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Berkas Terupload ({{ $p->berkas->count() }})</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Jenis</th>
                                        <th>Nama File</th>
                                        <th>Ukuran</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($p->berkas as $berkas)
                                    <tr>
                                        <td>{{ strtoupper($berkas->jenis) }}</td>
                                        <td>{{ $berkas->nama_file }}</td>
                                        <td>{{ $berkas->ukuran_kb }} KB</td>
                                        <td>
                                            @php
                                                $berkasStyle = $berkas->valid ? ['bg' => '#28a745', 'text' => '#fff'] : ['bg' => '#ffc107', 'text' => '#000'];
                                            @endphp
                                            <span class="badge" style="background-color: {{ $berkasStyle['bg'] }}; color: {{ $berkasStyle['text'] }}; font-weight: bold;">
                                                {{ $berkas->valid ? 'Valid' : 'Pending' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ asset('storage/' . $berkas->url) }}" target="_blank" class="btn btn-xs btn-primary">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @else
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <i class="ti ti-alert-triangle"></i> Belum ada berkas yang diupload
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<script>
// Real-time monitoring untuk admin
setInterval(function() {
    if (document.visibilityState === 'visible') {
        // Update statistics cards
        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Parse response dan update statistik
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Update cards
            const cards = document.querySelectorAll('.card h4');
            const newCards = doc.querySelectorAll('.card h4');
            
            cards.forEach((card, index) => {
                if (newCards[index] && card.textContent !== newCards[index].textContent) {
                    card.textContent = newCards[index].textContent;
                    card.parentElement.parentElement.classList.add('pulse-update');
                    setTimeout(() => {
                        card.parentElement.parentElement.classList.remove('pulse-update');
                    }, 1000);
                }
            });
        })
        .catch(error => console.log('Update error:', error));
    }
}, 15000);

// Show update indicator
setInterval(function() {
    const indicator = document.createElement('div');
    indicator.className = 'position-fixed top-0 start-50 translate-middle-x mt-3 alert alert-primary alert-dismissible fade show';
    indicator.style.zIndex = '9999';
    indicator.innerHTML = '<i class="ti ti-refresh fa-spin me-2"></i>Memperbarui data monitoring... <button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    document.body.appendChild(indicator);
    
    setTimeout(() => {
        if (indicator.parentNode) {
            indicator.remove();
        }
    }, 2500);
}, 13000);
</script>

<style>
.pulse-update {
    animation: pulseUpdate 1s ease-in-out;
}

@keyframes pulseUpdate {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); background-color: #e3f2fd; }
    100% { transform: scale(1); }
}
</style>
@endsection