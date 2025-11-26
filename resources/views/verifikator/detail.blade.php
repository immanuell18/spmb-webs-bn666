@extends('layouts.admin')

@section('title', 'Detail Verifikasi - SPMB')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Verifikasi Berkas</h1>
        <a href="{{ route('verifikator.verifikasi') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <!-- Data Pendaftar -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data Pendaftar</h6>
                </div>
                <div class="card-body">
                    <p><strong>No. Pendaftaran:</strong> {{ $pendaftar->no_pendaftaran }}</p>
                    <p><strong>Nama:</strong> {{ $pendaftar->nama }}</p>
                    <p><strong>Email:</strong> {{ $pendaftar->email }}</p>
                    <p><strong>Jurusan:</strong> {{ $pendaftar->jurusan->nama ?? '-' }}</p>
                    <p><strong>Gelombang:</strong> {{ $pendaftar->gelombang->nama ?? '-' }}</p>
                    <p><strong>Status:</strong> 
                        @php
                            $detailStatusColors = [
                                'SUBMIT' => ['bg' => '#ffc107', 'text' => '#000'],
                                'ADM_PASS' => ['bg' => '#28a745', 'text' => '#fff'],
                                'ADM_REJECT' => ['bg' => '#dc3545', 'text' => '#fff'],
                                'PAID' => ['bg' => '#17a2b8', 'text' => '#fff'],
                                'default' => ['bg' => '#6c757d', 'text' => '#fff']
                            ];
                            $detailStyle = $detailStatusColors[$pendaftar->status] ?? $detailStatusColors['default'];
                        @endphp
                        <span class="badge" style="background-color: {{ $detailStyle['bg'] }}; color: {{ $detailStyle['text'] }}; font-weight: bold;">
                            {{ $pendaftar->getStatusLabel() ?? $pendaftar->status ?? 'Belum Ada Status' }}
                        </span>
                    </p>
                    <p><strong>Tanggal Daftar:</strong> {{ $pendaftar->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>

            <!-- Data Pribadi -->
            @if($pendaftar->dataSiswa)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Data Pribadi</h6>
                </div>
                <div class="card-body">
                    <p><strong>NIK:</strong> {{ $pendaftar->dataSiswa->nik }}</p>
                    <p><strong>Tempat, Tgl Lahir:</strong> {{ $pendaftar->dataSiswa->tmp_lahir }}, {{ $pendaftar->dataSiswa->tgl_lahir ? \Carbon\Carbon::parse($pendaftar->dataSiswa->tgl_lahir)->format('d M Y') : '-' }}</p>
                    <p><strong>Jenis Kelamin:</strong> {{ $pendaftar->dataSiswa->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                    <p><strong>Agama:</strong> {{ $pendaftar->dataSiswa->agama }}</p>
                    <p><strong>Alamat:</strong> {{ $pendaftar->dataSiswa->alamat }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Berkas Upload -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Berkas yang Diupload</h6>
                </div>
                <div class="card-body">
                    @if($pendaftar->berkas->count() > 0)
                        <div class="row">
                            @foreach($pendaftar->berkas as $berkas)
                            <div class="col-md-6 mb-3">
                                <div class="card border-left-{{ $berkas->valid ? 'success' : 'warning' }}">
                                    <div class="card-body">
                                        @php
                                            $jenisLabels = [
                                                'LAINNYA' => 'Pas Foto 3x4',
                                                'IJAZAH' => 'Ijazah/STTB',
                                                'RAPOR' => 'Rapor Semester 1-5',
                                                'KK' => 'Kartu Keluarga',
                                                'AKTA' => 'Akta Kelahiran',
                                                'KIP' => 'KIP/KKS',
                                                'KKS' => 'KIP/KKS'
                                            ];
                                        @endphp
                                        <h6 class="card-title">{{ $jenisLabels[$berkas->jenis] ?? $berkas->jenis }}</h6>
                                        <p class="card-text">
                                            <small class="text-muted">{{ $berkas->nama_file }}</small><br>
                                            <small class="text-muted">{{ $berkas->ukuran_kb }} KB</small>
                                        </p>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-primary btn-sm" onclick="previewFile('{{ asset('storage/' . $berkas->url) }}', '{{ $berkas->nama_file }}')">
                                                <i class="fas fa-eye"></i> Preview
                                            </button>
                                            <a href="{{ asset('storage/' . $berkas->url) }}" class="btn btn-success btn-sm" download>
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </div>
                                        <div class="mt-2">
                                            @php
                                                $berkasDetailStyle = $berkas->valid ? ['bg' => '#28a745', 'text' => '#fff'] : ['bg' => '#ffc107', 'text' => '#000'];
                                            @endphp
                                            <span class="badge" style="background-color: {{ $berkasDetailStyle['bg'] }}; color: {{ $berkasDetailStyle['text'] }}; font-weight: bold;">
                                                {{ $berkas->valid ? 'Valid' : 'Belum Diverifikasi' }}
                                            </span>
                                        </div>
                                        @if($berkas->catatan)
                                        <div class="mt-2">
                                            <small class="text-muted">Catatan: {{ $berkas->catatan }}</small>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Belum ada berkas yang diupload
                        </div>
                    @endif
                </div>
            </div>

            <!-- Form Verifikasi -->
            @if($pendaftar->status == 'SUBMIT')
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Verifikasi Berkas</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('verifikator.proses', $pendaftar->id) }}" method="POST" onsubmit="return confirmVerification(event)">
                        @csrf
                        <div class="form-group">
                            <label for="catatan">Catatan Verifikasi</label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="3" placeholder="Berikan catatan untuk pendaftar..."></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="status" value="lulus" class="btn btn-success">
                                <i class="fas fa-check"></i> Lulus Verifikasi
                            </button>
                            <button type="submit" name="status" value="tolak" class="btn btn-danger">
                                <i class="fas fa-times"></i> Tolak Berkas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Preview: <span id="previewFileName"></span></h5>
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

function previewFile(fileUrl, fileName) {
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

// Function to close modal
function closePreviewModal() {
    if (previewModalInstance) {
        previewModalInstance.hide();
    }
}

// Confirmation function for verification
function confirmVerification(event) {
    const form = event.target;
    const status = event.submitter.value;
    const catatan = form.catatan.value;
    
    let message = '';
    if (status === 'lulus') {
        message = 'Apakah Anda yakin ingin MENYETUJUI berkas pendaftar ini?\n\nSetelah disetujui, pendaftar dapat melanjutkan ke tahap pembayaran.';
    } else if (status === 'tolak') {
        message = 'Apakah Anda yakin ingin MENOLAK berkas pendaftar ini?\n\nPendaftar perlu memperbaiki berkas yang ditolak.';
    }
    
    if (catatan.trim() === '' && status === 'tolak') {
        alert('Harap berikan catatan untuk berkas yang ditolak!');
        return false;
    }
    
    return confirm(message);
}

// Initialize when document is ready
$(document).ready(function() {
    // Initialize Bootstrap 5 modal
    const modalElement = document.getElementById('previewModal');
    if (modalElement) {
        previewModalInstance = new bootstrap.Modal(modalElement, {
            backdrop: true,
            keyboard: true
        });
    }
    
    // Add ESC key handler
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && modalElement && modalElement.classList.contains('show')) {
            closePreviewModal();
        }
    });
});
</script>
@endsection