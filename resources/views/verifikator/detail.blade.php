@extends('layouts.admin')

@section('title', 'Detail Verifikasi - SPMB')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="mb-1">Detail Verifikasi Berkas</h3>
            <p class="mb-0" style="color: var(--text3); font-size: 13px;">Periksa kelengkapan berkas pendaftar</p>
        </div>
        <a href="{{ route('verifikator.verifikasi') }}" class="btn btn-secondary btn-sm">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <!-- Data Pendaftar -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="m-0 fw-semibold">Data Pendaftar</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3 pb-3" style="border-bottom: 1px solid var(--border2);">
                        <div style="width: 48px; height: 48px; background: var(--p-100); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <span style="font-weight: 700; font-size: 18px; color: var(--p);">{{ strtoupper(substr($pendaftar->nama, 0, 1)) }}</span>
                        </div>
                        <div>
                            <div style="font-weight: 600; color: var(--text);">{{ $pendaftar->nama }}</div>
                            <div style="font-size: 12px; color: var(--text3);">{{ $pendaftar->no_pendaftaran }}</div>
                        </div>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span style="color: var(--text3); font-size: 13px;">Email</span>
                        <span style="font-weight: 500; font-size: 13px;">{{ $pendaftar->email }}</span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span style="color: var(--text3); font-size: 13px;">Jurusan</span>
                        <span style="font-weight: 500; font-size: 13px;">{{ $pendaftar->jurusan->nama ?? '-' }}</span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span style="color: var(--text3); font-size: 13px;">Gelombang</span>
                        <span style="font-weight: 500; font-size: 13px;">{{ $pendaftar->gelombang->nama ?? '-' }}</span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span style="color: var(--text3); font-size: 13px;">Status</span>
                        @php
                            $st = $pendaftar->status;
                            $stBg = match($st) { 'SUBMIT' => 'var(--warn-light)', 'ADM_PASS' => 'var(--ok-light)', 'ADM_REJECT' => 'var(--err-light)', 'PAID' => 'var(--info-light)', default => 'var(--bg2)' };
                            $stTx = match($st) { 'SUBMIT' => '#92400e', 'ADM_PASS' => '#065f46', 'ADM_REJECT' => '#991b1b', 'PAID' => '#155e75', default => 'var(--text3)' };
                        @endphp
                        <span class="badge" style="background: {{ $stBg }}; color: {{ $stTx }};">{{ $pendaftar->getStatusLabel() ?? $st ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color: var(--text3); font-size: 13px;">Tanggal Daftar</span>
                        <span style="font-weight: 500; font-size: 13px;">{{ $pendaftar->created_at->format('d M Y H:i') }}</span>
                    </div>
                </div>
            </div>

            @if($pendaftar->dataSiswa)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="m-0 fw-semibold">Data Pribadi</h6>
                </div>
                <div class="card-body" style="font-size: 13px;">
                    <div class="mb-2 d-flex justify-content-between">
                        <span style="color: var(--text3);">NIK</span>
                        <span style="font-weight: 500;">{{ $pendaftar->dataSiswa->nik }}</span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span style="color: var(--text3);">TTL</span>
                        <span style="font-weight: 500;">{{ $pendaftar->dataSiswa->tmp_lahir }}, {{ $pendaftar->dataSiswa->tgl_lahir ? \Carbon\Carbon::parse($pendaftar->dataSiswa->tgl_lahir)->format('d M Y') : '-' }}</span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span style="color: var(--text3);">Jenis Kelamin</span>
                        <span style="font-weight: 500;">{{ $pendaftar->dataSiswa->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span style="color: var(--text3);">Agama</span>
                        <span style="font-weight: 500;">{{ $pendaftar->dataSiswa->agama }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color: var(--text3);">Alamat</span>
                        <span style="font-weight: 500; text-align: right; max-width: 60%;">{{ $pendaftar->dataSiswa->alamat }}</span>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Berkas Upload -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-semibold">Berkas yang Diupload</h6>
                    <span class="badge bg-primary">{{ $pendaftar->berkas->count() }} berkas</span>
                </div>
                <div class="card-body">
                    @if($pendaftar->berkas->count() > 0)
                        <div class="row g-3">
                            @foreach($pendaftar->berkas as $berkas)
                            <div class="col-md-6">
                                <div style="border: 1px solid var(--border); border-radius: 12px; padding: 16px; transition: var(--t);" onmouseover="this.style.borderColor='var(--p-200)'" onmouseout="this.style.borderColor='var(--border)'">
                                    @php
                                        $jenisLabels = [
                                            'LAINNYA' => 'Pas Foto 3x4', 'IJAZAH' => 'Ijazah/STTB',
                                            'RAPOR' => 'Rapor Semester 1-5', 'KK' => 'Kartu Keluarga',
                                            'AKTA' => 'Akta Kelahiran', 'KIP' => 'KIP/KKS', 'KKS' => 'KIP/KKS'
                                        ];
                                        $iconMap = [
                                            'LAINNYA' => 'ti-photo', 'IJAZAH' => 'ti-certificate',
                                            'RAPOR' => 'ti-notebook', 'KK' => 'ti-id',
                                            'AKTA' => 'ti-file-text', 'KIP' => 'ti-card', 'KKS' => 'ti-card'
                                        ];
                                        $isValid = $berkas->valid || in_array($pendaftar->status, ['ADM_PASS', 'PAID', 'LULUS', 'TIDAK_LULUS', 'CADANGAN']);
                                        $isRejected = $pendaftar->status == 'ADM_REJECT';
                                    @endphp
                                    <div class="d-flex align-items-start gap-3">
                                        <div style="width: 40px; height: 40px; background: {{ $isValid ? 'var(--ok-light)' : ($isRejected ? 'var(--err-light)' : 'var(--warn-light)') }}; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <i class="ti {{ $iconMap[$berkas->jenis] ?? 'ti-file' }}" style="font-size: 18px; color: {{ $isValid ? '#059669' : ($isRejected ? '#dc2626' : '#d97706') }};"></i>
                                        </div>
                                        <div style="flex: 1; min-width: 0;">
                                            <div style="font-weight: 600; font-size: 13px; color: var(--text);">{{ $jenisLabels[$berkas->jenis] ?? $berkas->jenis }}</div>
                                            <div style="font-size: 11px; color: var(--text4); margin-top: 2px;">{{ $berkas->nama_file }} · {{ $berkas->ukuran_kb }} KB</div>
                                            <div class="d-flex align-items-center gap-2 mt-2">
                                                <span class="badge" style="background: {{ $isValid ? 'var(--ok-light)' : ($isRejected ? 'var(--err-light)' : 'var(--warn-light)') }}; color: {{ $isValid ? '#065f46' : ($isRejected ? '#dc2626' : '#92400e') }};">
                                                    {{ $isValid ? '✓ Valid' : ($isRejected ? '✖ Ditolak' : '⏳ Belum Diverifikasi') }}
                                                </span>
                                            </div>
                                            @if($berkas->catatan)
                                            <div style="font-size: 12px; color: var(--text3); margin-top: 6px; padding: 6px 8px; background: var(--bg2); border-radius: 6px;">
                                                {{ $berkas->catatan }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2 mt-3">
                                        <button class="btn btn-primary btn-sm" onclick="previewFile('{{ asset('storage/' . $berkas->url) }}', '{{ $berkas->nama_file }}')">
                                            <i class="ti ti-eye"></i> Preview
                                        </button>
                                        <a href="{{ asset('storage/' . $berkas->url) }}" class="btn btn-secondary btn-sm" download>
                                            <i class="ti ti-download"></i> Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center" style="padding: 40px 0;">
                            <i class="ti ti-folder-off" style="font-size: 40px; color: var(--text4);"></i>
                            <p class="mt-2 mb-0" style="color: var(--text3);">Belum ada berkas yang diupload</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Form Verifikasi -->
            @if($pendaftar->status == 'SUBMIT')
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="m-0 fw-semibold">Verifikasi Berkas</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('verifikator.proses', $pendaftar->id) }}" method="POST" onsubmit="return confirmVerification(event)">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Catatan Verifikasi</label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="3" placeholder="Berikan catatan untuk pendaftar..."></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" name="status" value="lulus" class="btn btn-success">
                                <i class="ti ti-check"></i> Lulus Verifikasi
                            </button>
                            <button type="submit" name="status" value="tolak" class="btn btn-danger">
                                <i class="ti ti-x"></i> Tolak Berkas
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
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-photo me-2"></i> Preview: <span id="previewFileName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="closePreviewModal()"></button>
            </div>
            <div class="modal-body text-center">
                <div id="previewContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" onclick="closePreviewModal()">Tutup</button>
                <a id="downloadLink" class="btn btn-primary btn-sm" download><i class="ti ti-download"></i> Download</a>
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
    if (['jpg','jpeg','png'].includes(fileExt)) {
        previewContent.innerHTML = `<img src="${fileUrl}" class="img-fluid" style="max-height: 500px; border-radius: 12px;">`;
    } else if (fileExt === 'pdf') {
        previewContent.innerHTML = `<embed src="${fileUrl}" type="application/pdf" width="100%" height="500px">`;
    } else {
        previewContent.innerHTML = `<p style="color: var(--text4);">Preview tidak tersedia. Silakan download.</p>`;
    }
    if (!previewModalInstance) {
        previewModalInstance = new bootstrap.Modal(document.getElementById('previewModal'));
    }
    previewModalInstance.show();
}

function closePreviewModal() { if (previewModalInstance) previewModalInstance.hide(); }

function confirmVerification(event) {
    const status = event.submitter.value;
    const catatan = event.target.catatan.value;
    if (catatan.trim() === '' && status === 'tolak') {
        alert('Harap berikan catatan untuk berkas yang ditolak!');
        return false;
    }
    return confirm(status === 'lulus'
        ? 'Yakin menyetujui berkas pendaftar ini?'
        : 'Yakin menolak berkas pendaftar ini?');
}
</script>
@endsection