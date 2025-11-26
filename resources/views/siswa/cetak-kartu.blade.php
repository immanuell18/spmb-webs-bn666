@extends('layouts.siswa')

@section('title', 'Cetak Kartu - SPMB')

@section('content')
    <!-- Page Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Cetak Kartu</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="{{ route('siswa.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Cetak Kartu</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Cetak Content Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Cetak Kartu</h6>
                <h1 class="mb-5">Download Kartu Pendaftaran</h1>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    @if(!$pendaftar)
                        <div class="alert alert-warning text-center">
                            <h5>Anda belum terdaftar</h5>
                            <p>Silakan lengkapi pendaftaran terlebih dahulu.</p>
                            <a href="{{ route('siswa.pendaftaran') }}" class="btn btn-primary">Daftar Sekarang</a>
                        </div>
                    @else
                        <!-- Kartu Pendaftaran -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fa fa-id-card me-2"></i>Kartu Pendaftaran</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <table class="table table-borderless">
                                            <tr><td><strong>No. Pendaftaran</strong></td><td>{{ $pendaftar->no_pendaftaran }}</td></tr>
                                            <tr><td><strong>Nama Lengkap</strong></td><td>{{ $pendaftar->nama }}</td></tr>
                                            <tr><td><strong>Email</strong></td><td>{{ $pendaftar->email }}</td></tr>
                                            <tr><td><strong>Jurusan</strong></td><td>{{ $pendaftar->jurusan->nama ?? 'N/A' }}</td></tr>
                                            <tr><td><strong>Gelombang</strong></td><td>{{ $pendaftar->gelombang->nama ?? 'N/A' }}</td></tr>
                                            <tr><td><strong>Status</strong></td><td>
                                                @if($pendaftar->status === 'PAID')
                                                    <span class="badge bg-success">Terbayar</span>
                                                @elseif($pendaftar->status === 'ADM_PASS')
                                                    <span class="badge bg-primary">Terverifikasi</span>
                                                @elseif($pendaftar->status === 'SUBMIT')
                                                    <span class="badge bg-warning">Menunggu Verifikasi</span>
                                                @else
                                                    <span class="badge bg-danger">Ditolak</span>
                                                @endif
                                            </td></tr>
                                        </table>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="border p-3">
                                            <i class="fa fa-user fa-5x text-muted mb-3"></i>
                                            <p class="text-muted">Foto Siswa</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="{{ route('siswa.cetak-kartu.pdf') }}" class="btn btn-primary" target="_blank">
                                        <i class="fa fa-download me-2"></i>Download Kartu PDF
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Bukti Pembayaran -->
                        @if($pendaftar->bukti_bayar)
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fa fa-receipt me-2"></i>Bukti Pembayaran</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr><td><strong>No. Pendaftaran</strong></td><td>{{ $pendaftar->no_pendaftaran }}</td></tr>
                                            <tr><td><strong>Nama</strong></td><td>{{ $pendaftar->nama }}</td></tr>
                                            <tr><td><strong>Biaya Pendaftaran</strong></td><td>Rp {{ number_format($pendaftar->biaya_pendaftaran, 0, ',', '.') }}</td></tr>
                                            <tr><td><strong>Status Pembayaran</strong></td><td>
                                                @if($pendaftar->status === 'PAID')
                                                    <span class="badge bg-success">Terbayar</span>
                                                @else
                                                    <span class="badge bg-warning">Menunggu Validasi</span>
                                                @endif
                                            </td></tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-center">
                                            <img src="{{ asset('storage/' . $pendaftar->bukti_bayar) }}" 
                                                 class="img-fluid border" style="max-height: 200px;" alt="Bukti Bayar">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="{{ route('siswa.cetak-bukti.pdf') }}" class="btn btn-success" target="_blank">
                                        <i class="fa fa-download me-2"></i>Download Bukti Bayar PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Pengumuman Hasil -->
                        @if($pendaftar->status_akhir)
                        <div class="card">
                            <div class="card-header bg-{{ $pendaftar->status_akhir === 'LULUS' ? 'success' : ($pendaftar->status_akhir === 'CADANGAN' ? 'warning' : 'danger') }} text-white">
                                <h5 class="mb-0"><i class="fa fa-trophy me-2"></i>Pengumuman Hasil</h5>
                            </div>
                            <div class="card-body text-center">
                                @if($pendaftar->status_akhir === 'LULUS')
                                    <i class="fa fa-trophy fa-5x text-success mb-3"></i>
                                    <h3 class="text-success">🎉 SELAMAT! ANDA DITERIMA!</h3>
                                    <p class="lead">Selamat bergabung dengan keluarga besar SMK Bali Global Badung</p>
                                @elseif($pendaftar->status_akhir === 'CADANGAN')
                                    <i class="fa fa-clock fa-5x text-warning mb-3"></i>
                                    <h3 class="text-warning">📋 ANDA MASUK DAFTAR CADANGAN</h3>
                                    <p class="lead">Mohon tunggu pengumuman selanjutnya</p>
                                @else
                                    <i class="fa fa-times-circle fa-5x text-danger mb-3"></i>
                                    <h3 class="text-danger">😔 MAAF, ANDA BELUM BERHASIL</h3>
                                    <p class="lead">Jangan menyerah, coba lagi di kesempatan berikutnya</p>
                                @endif
                                <small class="text-muted">Diumumkan pada {{ $pendaftar->tgl_pengumuman->format('d M Y H:i') }}</small>
                                
                                @if($pendaftar->status_akhir === 'LULUS')
                                <div class="mt-4">
                                    <a href="{{ route('siswa.cetak-pengumuman.pdf') }}" class="btn btn-success btn-lg" target="_blank">
                                        <i class="fa fa-download me-2"></i>Download Surat Penerimaan
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- Cetak Content End -->
@endsection