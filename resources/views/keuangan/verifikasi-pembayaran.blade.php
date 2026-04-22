@extends('layouts.admin')

@section('title', 'Verifikasi Pembayaran - Keuangan')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="mb-1">Verifikasi Pembayaran</h3>
            <p class="mb-0" style="color: var(--text3); font-size: 13px;">Validasi bukti pembayaran pendaftar</p>
        </div>
        <span class="badge bg-warning" style="font-size: 12px; padding: 6px 14px;">{{ $pendaftar->total() }} data</span>
    </div>

    @if(session('success'))
    <div class="alert alert-success"><i class="ti ti-check me-1"></i> {{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="m-0 fw-semibold">Menunggu Validasi Pembayaran</h6>
        </div>
        <div class="card-body" style="padding: 0 !important;">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>No Pendaftaran</th>
                            <th>Nama</th>
                            <th>Jurusan</th>
                            <th>Metode</th>
                            <th>Nominal</th>
                            <th>Tanggal</th>
                            <th>Bukti</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendaftar as $p)
                        <tr>
                            <td style="font-weight: 600; color: var(--p);">{{ $p->no_pendaftaran }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width: 30px; height: 30px; background: var(--ok-light); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <span style="font-weight: 600; font-size: 11px; color: #059669;">{{ strtoupper(substr($p->nama, 0, 1)) }}</span>
                                    </div>
                                    <span style="font-weight: 500;">{{ $p->nama }}</span>
                                </div>
                            </td>
                            <td>{{ $p->jurusan->nama ?? '-' }}</td>
                            <td>
                                @php $transaction = $p->paymentTransactions->first(); @endphp
                                @if($transaction)
                                    {!! $transaction->payment_method_badge !!}
                                    @if($transaction->payment_type === 'bank_transfer')
                                        <br><small style="color: var(--text4);">{{ $transaction->bank_info }}</small>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">Manual</span>
                                @endif
                            </td>
                            <td style="font-weight: 600;">Rp {{ number_format($p->biaya_pendaftaran, 0, ',', '.') }}</td>
                            <td style="color: var(--text3);">{{ $p->updated_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @php $buktiBayar = $p->berkas->where('jenis', 'BUKTI_BAYAR')->first(); @endphp
                                @if($buktiBayar)
                                    <button class="btn btn-outline-primary btn-sm" onclick="previewBukti('{{ asset('storage/' . $buktiBayar->url) }}', '{{ $buktiBayar->nama_file }}')">
                                        <i class="ti ti-eye"></i> Lihat
                                    </button>
                                @else
                                    <span style="color: var(--text4); font-size: 12px;">Tidak ada</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal{{ $p->id }}">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                    @if($p->status === 'ADM_PASS')
                                        <button class="btn btn-success btn-sm" onclick="validasiModal({{ $p->id }}, 'terbayar')">
                                            <i class="ti ti-check"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="validasiModal({{ $p->id }}, 'reject')">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    @else
                                        <span class="badge bg-success">Terverifikasi</span>
                                        @if($p->user_verifikasi_payment)
                                            <br><small style="color: var(--text4);">{{ $p->user_verifikasi_payment }}</small>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center" style="padding: 40px 14px !important;">
                                <i class="ti ti-mood-happy" style="font-size: 36px; color: var(--ok);"></i>
                                <p class="mt-2 mb-0" style="color: var(--text3);">Semua pembayaran sudah diverifikasi!</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pendaftar->hasPages())
            <div class="d-flex justify-content-center" style="padding: 16px;">
                {{ $pendaftar->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Validasi -->
<div class="modal fade" id="validasiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-credit-card me-2"></i> Validasi Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="validasiForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="status" id="statusInput">
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Berikan catatan jika diperlukan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
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
                <h5 class="modal-title">Detail — {{ $p->nama }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 style="font-weight: 600; margin-bottom: 12px; font-size: 14px;">Data Pribadi</h6>
                        <div style="font-size: 13px;">
                            <div class="d-flex justify-content-between mb-2"><span style="color: var(--text3);">No. Pendaftaran</span><span style="font-weight: 500;">{{ $p->no_pendaftaran }}</span></div>
                            <div class="d-flex justify-content-between mb-2"><span style="color: var(--text3);">Nama</span><span style="font-weight: 500;">{{ $p->nama }}</span></div>
                            <div class="d-flex justify-content-between mb-2"><span style="color: var(--text3);">Email</span><span style="font-weight: 500;">{{ $p->email }}</span></div>
                            <div class="d-flex justify-content-between mb-2"><span style="color: var(--text3);">Jurusan</span><span style="font-weight: 500;">{{ $p->jurusan->nama ?? 'N/A' }}</span></div>
                            <div class="d-flex justify-content-between"><span style="color: var(--text3);">Gelombang</span><span style="font-weight: 500;">{{ $p->gelombang->nama ?? 'N/A' }}</span></div>
                        </div>

                        @if($p->dataSiswa)
                        <h6 style="font-weight: 600; margin: 16px 0 12px; font-size: 14px;">Data Siswa</h6>
                        <div style="font-size: 13px;">
                            <div class="d-flex justify-content-between mb-2"><span style="color: var(--text3);">NIK</span><span style="font-weight: 500;">{{ $p->dataSiswa->nik ?? '-' }}</span></div>
                            <div class="d-flex justify-content-between mb-2"><span style="color: var(--text3);">JK</span><span style="font-weight: 500;">{{ $p->dataSiswa->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}</span></div>
                            <div class="d-flex justify-content-between mb-2"><span style="color: var(--text3);">TTL</span><span style="font-weight: 500;">{{ $p->dataSiswa->tmp_lahir ?? '-' }}, {{ $p->dataSiswa->tgl_lahir ?? '-' }}</span></div>
                            <div class="d-flex justify-content-between"><span style="color: var(--text3);">Alamat</span><span style="font-weight: 500; text-align: right; max-width: 55%;">{{ $p->dataSiswa->alamat ?? '-' }}</span></div>
                        </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        @if($p->dataOrtu)
                        <h6 style="font-weight: 600; margin-bottom: 12px; font-size: 14px;">Data Orang Tua</h6>
                        <div style="font-size: 13px;">
                            <div class="d-flex justify-content-between mb-2"><span style="color: var(--text3);">Ayah</span><span style="font-weight: 500;">{{ $p->dataOrtu->nama_ayah ?? '-' }}</span></div>
                            <div class="d-flex justify-content-between mb-2"><span style="color: var(--text3);">Pekerjaan</span><span style="font-weight: 500;">{{ $p->dataOrtu->pekerjaan_ayah ?? '-' }}</span></div>
                            <div class="d-flex justify-content-between mb-2"><span style="color: var(--text3);">Ibu</span><span style="font-weight: 500;">{{ $p->dataOrtu->nama_ibu ?? '-' }}</span></div>
                            <div class="d-flex justify-content-between"><span style="color: var(--text3);">Pekerjaan</span><span style="font-weight: 500;">{{ $p->dataOrtu->pekerjaan_ibu ?? '-' }}</span></div>
                        </div>
                        @endif

                        <h6 style="font-weight: 600; margin: 16px 0 12px; font-size: 14px;">Status Pembayaran</h6>
                        <div style="font-size: 13px;">
                            <div class="d-flex justify-content-between mb-2"><span style="color: var(--text3);">Biaya</span><span style="font-weight: 600; color: var(--ok);">Rp {{ number_format($p->biaya_pendaftaran, 0, ',', '.') }}</span></div>
                            <div class="d-flex justify-content-between"><span style="color: var(--text3);">Status</span><span class="badge bg-warning">{{ ucfirst($p->status_pembayaran) }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-photo me-2"></i> <span id="previewFileName"></span></h5>
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
let previewModalInstance, validasiModalInstance;

function validasiModal(id, status) {
    document.getElementById('statusInput').value = status;
    document.getElementById('validasiForm').action = `/keuangan/pembayaran/${id}`;
    if (!validasiModalInstance) validasiModalInstance = new bootstrap.Modal(document.getElementById('validasiModal'));
    validasiModalInstance.show();
}

function previewBukti(fileUrl, fileName) {
    document.getElementById('previewFileName').textContent = fileName;
    document.getElementById('downloadLink').href = fileUrl;
    const ext = fileName.split('.').pop().toLowerCase();
    const el = document.getElementById('previewContent');
    if (['jpg','jpeg','png'].includes(ext)) el.innerHTML = `<img src="${fileUrl}" class="img-fluid" style="max-height: 500px; border-radius: 12px;">`;
    else if (ext === 'pdf') el.innerHTML = `<embed src="${fileUrl}" type="application/pdf" width="100%" height="500px">`;
    else el.innerHTML = `<p style="color: var(--text4);">Preview tidak tersedia.</p>`;
    if (!previewModalInstance) previewModalInstance = new bootstrap.Modal(document.getElementById('previewModal'));
    previewModalInstance.show();
}

function closePreviewModal() { if (previewModalInstance) previewModalInstance.hide(); }

$(document).ready(function() {
    const pm = document.getElementById('previewModal');
    if (pm) previewModalInstance = new bootstrap.Modal(pm);
    const vm = document.getElementById('validasiModal');
    if (vm) validasiModalInstance = new bootstrap.Modal(vm);
});
</script>

<script>
// Auto refresh setiap 20 detik
setInterval(function() {
    if (document.visibilityState === 'visible') location.reload();
}, 20000);
</script>
@endsection