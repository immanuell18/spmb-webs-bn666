@extends('layouts.siswa')

@section('title', 'Pembayaran - SPMB')

@section('content')
    <!-- Page Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Pembayaran</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="{{ route('siswa.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Pembayaran</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Payment Content Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Pembayaran</h6>
                <h1 class="mb-5">Bayar Biaya Pendaftaran</h1>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle me-2"></i>{{ session('error') }}
                        </div>
                    @endif
                    
                    @if(!$pendaftar)
                        <div class="alert alert-warning text-center">
                            <h5>Anda belum terdaftar</h5>
                            <p>Silakan lengkapi pendaftaran terlebih dahulu.</p>
                            <a href="{{ route('siswa.pendaftaran') }}" class="btn btn-primary">Daftar Sekarang</a>
                        </div>
                    @elseif($pendaftar->status === 'SUBMIT')
                        <div class="alert alert-info text-center">
                            <h5>Menunggu Verifikasi Berkas</h5>
                            <p>Berkas Anda sedang dalam proses verifikasi. Pembayaran akan dibuka setelah berkas diverifikasi.</p>
                            <a href="{{ route('siswa.status') }}" class="btn btn-primary">Cek Status</a>
                        </div>
                    @elseif($pendaftar->status === 'ADM_REJECT')
                        <div class="alert alert-danger text-center">
                            <h5>Berkas Ditolak</h5>
                            <p>Maaf, berkas Anda ditolak. Silakan perbaiki dan upload ulang berkas yang diperlukan.</p>
                            <a href="{{ route('siswa.berkas') }}" class="btn btn-warning">Perbaiki Berkas</a>
                        </div>
                    @elseif($pendaftar->status === 'PAID')
                        <div class="alert alert-success text-center">
                            <h5>Pembayaran Berhasil</h5>
                            <p>Selamat! Pendaftaran Anda sudah lengkap dan pembayaran telah dikonfirmasi.</p>
                            @php
                                $buktiBayarPaid = $pendaftar->berkas->where('jenis', 'BUKTI_BAYAR')->first();
                            @endphp
                            @if($buktiBayarPaid)
                                <a href="{{ asset('storage/' . $buktiBayarPaid->url) }}" target="_blank" class="btn btn-outline-primary me-2">
                                    <i class="fa fa-eye me-1"></i>Lihat Bukti Bayar
                                </a>
                            @endif
                            <a href="{{ route('siswa.status') }}" class="btn btn-primary">Cek Status</a>
                        </div>
                    @elseif($pendaftar->status === 'ADM_PASS')
                        <!-- Payment Info -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fa fa-check-circle me-2"></i>Berkas Terverifikasi - Silakan Bayar</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr><td><strong>No. Pendaftaran</strong></td><td>{{ $pendaftar->no_pendaftaran }}</td></tr>
                                            <tr><td><strong>Nama</strong></td><td>{{ $pendaftar->nama }}</td></tr>
                                            <tr><td><strong>Jurusan</strong></td><td>{{ $pendaftar->jurusan->nama }}</td></tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr><td><strong>Biaya Pendaftaran</strong></td><td class="text-success"><h5>Rp {{ number_format($pendaftar->biaya_pendaftaran, 0, ',', '.') }}</h5></td></tr>
                                            <tr><td><strong>Status</strong></td><td>
                                                @php
                                                    $buktiBayar = $pendaftar->berkas->where('jenis', 'BUKTI_BAYAR')->first();
                                                @endphp
                                                @if($buktiBayar)
                                                    <span class="badge bg-warning">Menunggu Validasi</span>
                                                @else
                                                    <span class="badge bg-danger">Belum Bayar</span>
                                                @endif
                                            </td></tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Info -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fa fa-university me-2"></i>Informasi Rekening</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <h6>Bank BRI</h6>
                                        <p><strong>1234-5678-9012-3456</strong><br>
                                        a.n. SMK Bali Global Badung</p>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <h6>Bank BCA</h6>
                                        <p><strong>9876-5432-1098-7654</strong><br>
                                        a.n. SMK Bali Global Badung</p>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <h6>Bank Mandiri</h6>
                                        <p><strong>1111-2222-3333-4444</strong><br>
                                        a.n. SMK Bali Global Badung</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @php
                            $buktiBayar = $pendaftar->berkas->where('jenis', 'BUKTI_BAYAR')->first();
                        @endphp
                        @if($buktiBayar)
                            <!-- Waiting Validation -->
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-white text-center">
                                    <h5 class="mb-0"><i class="fa fa-clock me-2"></i>Menunggu Validasi</h5>
                                </div>
                                <div class="card-body text-center">
                                    <i class="fa fa-clock fa-5x text-warning mb-3"></i>
                                    <h4 class="text-warning">Bukti Pembayaran Sedang Divalidasi</h4>
                                    <p>Tim keuangan sedang memverifikasi bukti pembayaran Anda. Mohon tunggu konfirmasi.</p>
                                    <a href="{{ asset('storage/' . $buktiBayar->url) }}" target="_blank" class="btn btn-outline-primary me-2">
                                        <i class="fa fa-eye me-1"></i>Lihat Bukti Bayar
                                    </a>
                                    <button class="btn btn-outline-warning" onclick="showUploadForm()">
                                        <i class="fa fa-edit me-1"></i>Ganti Bukti Bayar
                                    </button>
                                </div>
                            </div>
                        @else
                            <!-- Upload Form -->
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fa fa-upload me-2"></i>Upload Bukti Pembayaran</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('siswa.bayar.upload') }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">Bukti Pembayaran (JPG/PNG/PDF, Max 2MB)</label>
                                            <input type="file" class="form-control @error('bukti_bayar') is-invalid @enderror" 
                                                   name="bukti_bayar" accept=".jpg,.jpeg,.png,.pdf" required>
                                            @error('bukti_bayar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle me-2"></i>
                                            <strong>Petunjuk:</strong>
                                            <ul class="mb-0 mt-2">
                                                <li>Upload foto/scan bukti transfer yang jelas</li>
                                                <li>Pastikan nominal dan tanggal transfer terlihat</li>
                                                <li>Format file: JPG, PNG, atau PDF</li>
                                                <li>Ukuran maksimal 2MB</li>
                                            </ul>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fa fa-upload me-2"></i>Upload Bukti Pembayaran
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- Payment Content End -->
    
    <!-- Modal Upload (for re-upload) -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ganti Bukti Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('siswa.bayar.upload') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Bukti Pembayaran Baru</label>
                            <input type="file" class="form-control" name="bukti_bayar" accept=".jpg,.jpeg,.png,.pdf" required>
                        </div>
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle me-2"></i>
                            Mengganti bukti pembayaran akan mengubah status menjadi "Menunggu Validasi" kembali.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-upload me-1"></i>Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
    function showUploadForm() {
        const modal = new bootstrap.Modal(document.getElementById('uploadModal'));
        modal.show();
    }
    </script>
@endsection