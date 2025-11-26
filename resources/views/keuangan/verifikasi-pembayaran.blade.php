@extends('layouts.admin')

@section('title', 'Verifikasi Pembayaran - Keuangan')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Verifikasi Pembayaran</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Menunggu Validasi Pembayaran</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No Pendaftaran</th>
                            <th>Nama</th>
                            <th>Jurusan</th>
                            <th>Metode Bayar</th>
                            <th>Nominal</th>
                            <th>Tanggal Upload</th>
                            <th>Bukti Bayar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendaftar as $p)
                        <tr>
                            <td>{{ $p->no_pendaftaran }}</td>
                            <td>{{ $p->nama }}</td>
                            <td>{{ $p->jurusan->nama ?? '-' }}</td>
                            <td>
                                @php
                                    $transaction = $p->paymentTransactions->first();
                                @endphp
                                @if($transaction)
                                    {!! $transaction->payment_method_badge !!}
                                    @if($transaction->payment_type === 'bank_transfer')
                                        <br><small class="text-muted">{{ $transaction->bank_info }}</small>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">Manual</span>
                                @endif
                            </td>
                            <td>Rp {{ number_format($p->biaya_pendaftaran, 0, ',', '.') }}</td>
                            <td>{{ $p->updated_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @php
                                    $buktiBayar = $p->berkas->where('jenis', 'BUKTI_BAYAR')->first();
                                @endphp
                                @if($buktiBayar)
                                    <button class="btn btn-outline-primary btn-sm" onclick="previewBukti('{{ asset('storage/' . $buktiBayar->url) }}', '{{ $buktiBayar->nama_file }}')">
                                        <i class="fas fa-eye"></i> Lihat
                                    </button>
                                @else
                                    <span class="text-muted">Tidak ada</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal{{ $p->id }}">
                                    <i class="fas fa-eye"></i> Detail
                                </button>
                                @if($p->status === 'ADM_PASS')
                                    <button class="btn btn-success btn-sm" onclick="validasiModal({{ $p->id }}, 'terbayar')">
                                        <i class="fas fa-check"></i> Terima
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="validasiModal({{ $p->id }}, 'reject')">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                @else
                                    <span class="badge bg-success">✅ Sudah Diverifikasi</span>
                                    @if($p->user_verifikasi_payment)
                                        <br><small>Oleh: {{ $p->user_verifikasi_payment }}</small>
                                        <br><small>{{ $p->tgl_verifikasi_payment->format('d/m/Y H:i') }}</small>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $pendaftar->links() }}
        </div>
    </div>
</div>

<!-- Modal Validasi -->
<div class="modal fade" id="validasiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Validasi Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="validasiForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="status" id="statusInput">
                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Berikan catatan jika diperlukan..."></textarea>
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

<!-- Modal Detail -->
@foreach($pendaftar as $p)
<div class="modal fade" id="detailModal{{ $p->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pendaftar - {{ $p->nama }}</h5>
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
                        
                        <h6>Status Pembayaran</h6>
                        <table class="table table-sm">
                            <tr><td>Biaya</td><td>Rp {{ number_format($p->biaya_pendaftaran, 0, ',', '.') }}</td></tr>
                            <tr><td>Status</td><td><span class="badge bg-warning">{{ ucfirst($p->status_pembayaran) }}</span></td></tr>
                            @if($p->bukti_bayar)
                            <tr><td>Bukti Bayar</td><td><a href="{{ asset('storage/' . $p->bukti_bayar) }}" target="_blank" class="btn btn-xs btn-primary">Lihat</a></td></tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview Bukti Pembayaran: <span id="previewFileName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closePreviewModal()"></button>
            </div>
            <div class="modal-body text-center">
                <div id="previewContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="closePreviewModal()">Tutup</button>
                <a id="downloadLink" class="btn btn-primary" download>
                    <i class="fas fa-download"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>

<script>
let previewModalInstance;
let validasiModalInstance;

function validasiModal(id, status) {
    document.getElementById('statusInput').value = status;
    document.getElementById('validasiForm').action = `/keuangan/pembayaran/${id}`;
    
    if (!validasiModalInstance) {
        validasiModalInstance = new bootstrap.Modal(document.getElementById('validasiModal'));
    }
    validasiModalInstance.show();
}

function previewBukti(fileUrl, fileName) {
    document.getElementById('previewFileName').textContent = fileName;
    document.getElementById('downloadLink').href = fileUrl;
    
    const fileExt = fileName.split('.').pop().toLowerCase();
    const previewContent = document.getElementById('previewContent');
    
    if (['jpg', 'jpeg', 'png'].includes(fileExt)) {
        previewContent.innerHTML = `<img src="${fileUrl}" class="img-fluid" style="max-height: 500px;">`;
    } else if (fileExt === 'pdf') {
        previewContent.innerHTML = `<embed src="${fileUrl}" type="application/pdf" width="100%" height="500px">`;
    } else {
        previewContent.innerHTML = `<p class="text-muted">Preview tidak tersedia untuk file ini. Silakan download untuk melihat.</p>`;
    }
    
    // Use Bootstrap 5 Modal API
    if (!previewModalInstance) {
        previewModalInstance = new bootstrap.Modal(document.getElementById('previewModal'), {
            backdrop: true,
            keyboard: true
        });
    }
    previewModalInstance.show();
}

function closePreviewModal() {
    if (previewModalInstance) {
        previewModalInstance.hide();
    }
}

// Initialize when document is ready
$(document).ready(function() {
    const previewModalElement = document.getElementById('previewModal');
    if (previewModalElement) {
        previewModalInstance = new bootstrap.Modal(previewModalElement, {
            backdrop: true,
            keyboard: true
        });
    }
    
    const validasiModalElement = document.getElementById('validasiModal');
    if (validasiModalElement) {
        validasiModalInstance = new bootstrap.Modal(validasiModalElement, {
            backdrop: true,
            keyboard: true
        });
    }
});
</script>

<script>
// Auto refresh setiap 20 detik untuk keuangan
setInterval(function() {
    if (document.visibilityState === 'visible') {
        location.reload();
    }
}, 20000);

// Real-time notification
setInterval(function() {
    const notification = document.createElement('div');
    notification.className = 'position-fixed bottom-0 end-0 m-3 alert alert-success alert-dismissible fade show';
    notification.style.zIndex = '9999';
    notification.innerHTML = '<i class="fas fa-money-check-alt me-2"></i>Memeriksa pembayaran baru... <button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 3000);
}, 18000);
</script>
@endsection