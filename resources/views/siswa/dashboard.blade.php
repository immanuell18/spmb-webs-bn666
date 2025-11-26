@extends('layouts.siswa')

@section('title', 'Dashboard Siswa - SPMB')

@section('content')
    <!-- Page Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Dashboard Siswa</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="{{ route('siswa.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Beranda</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Dashboard Content Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Dashboard</h6>
                <h1 class="mb-5">Selamat Datang, {{ Auth::user()->name }}!</h1>
            </div>
            
            <!-- Gelombang Status Alert -->
            @if(!$gelombangAktif)
                <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                    <i class="fa fa-exclamation-triangle me-2"></i>
                    <strong>Pendaftaran Ditutup!</strong> Saat ini tidak ada gelombang pendaftaran yang aktif. Silakan tunggu pengumuman gelombang berikutnya.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @else
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fa fa-check-circle me-2"></i>
                    <strong>Pendaftaran Dibuka!</strong> {{ $gelombangAktif->nama }} - {{ $gelombangAktif->tahun }} 
                    ({{ $gelombangAktif->tgl_mulai->format('d M Y') }} - {{ $gelombangAktif->tgl_selesai->format('d M Y') }})
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fa fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                    <i class="fa fa-info-circle me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Progress Steps -->
            @if($pendaftar)
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fa fa-list-ol me-2"></i>Progress Pendaftaran</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        @php
                            $hasDataSiswa = $pendaftar->dataSiswa ? true : false;
                            $hasDataOrtu = $pendaftar->dataOrtu ? true : false;
                            $hasDataSekolah = $pendaftar->asalSekolah ? true : false;
                            $isFormComplete = $hasDataSiswa && $hasDataOrtu && $hasDataSekolah;
                            $berkasCount = $pendaftar->berkas ? $pendaftar->berkas->count() : 0;
                            $isBerkasComplete = $berkasCount >= 4;
                        @endphp
                        
                        <div class="col-md-3">
                            <div class="step {{ $isFormComplete ? 'completed' : 'active' }}">
                                <div class="step-icon {{ $isFormComplete ? 'bg-success' : 'bg-warning' }} text-white rounded-circle mx-auto mb-2" style="width: 50px; height: 50px; line-height: 50px;">
                                    <i class="fa {{ $isFormComplete ? 'fa-check' : 'fa-edit' }}"></i>
                                </div>
                                <h6 class="{{ $isFormComplete ? 'text-success' : 'text-warning' }}">Pengisian Data</h6>
                                <small class="{{ $isFormComplete ? 'text-success' : 'text-warning' }}">
                                    {{ $isFormComplete ? 'Selesai' : 'Dalam Proses' }}
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="step {{ $isBerkasComplete ? 'completed' : ($isFormComplete ? 'active' : '') }}">
                                <div class="step-icon {{ $isBerkasComplete ? 'bg-success' : ($isFormComplete ? 'bg-primary' : 'bg-secondary') }} text-white rounded-circle mx-auto mb-2" style="width: 50px; height: 50px; line-height: 50px;">
                                    <i class="fa {{ $isBerkasComplete ? 'fa-check' : 'fa-upload' }}"></i>
                                </div>
                                <h6 class="{{ $isBerkasComplete ? 'text-success' : ($isFormComplete ? 'text-primary' : 'text-muted') }}">Upload Berkas</h6>
                                <small class="{{ $isBerkasComplete ? 'text-success' : ($isFormComplete ? 'text-primary' : 'text-muted') }}">
                                    {{ $isBerkasComplete ? 'Lengkap' : $berkasCount . '/4 berkas' }}
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            @php
                                $isVerified = in_array($pendaftar->status, ['ADM_PASS', 'PAID']);
                            @endphp
                            <div class="step {{ $isVerified ? 'completed' : '' }}">
                                <div class="step-icon {{ $isVerified ? 'bg-success' : 'bg-secondary' }} text-white rounded-circle mx-auto mb-2" style="width: 50px; height: 50px; line-height: 50px;">
                                    <i class="fa {{ $isVerified ? 'fa-check' : 'fa-clock' }}"></i>
                                </div>
                                <h6 class="{{ $isVerified ? 'text-success' : 'text-muted' }}">Verifikasi</h6>
                                <small class="{{ $isVerified ? 'text-success' : 'text-muted' }}">
                                    {{ $isVerified ? 'Lulus' : 'Menunggu' }}
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            @php
                                $isPaid = ($pendaftar->status == 'PAID' || $pendaftar->status_pembayaran == 'terbayar');
                            @endphp
                            <div class="step {{ $isPaid ? 'completed' : '' }}">
                                <div class="step-icon {{ $isPaid ? 'bg-success' : 'bg-secondary' }} text-white rounded-circle mx-auto mb-2" style="width: 50px; height: 50px; line-height: 50px;">
                                    <i class="fa {{ $isPaid ? 'fa-check' : 'fa-credit-card' }}"></i>
                                </div>
                                <h6 class="{{ $isPaid ? 'text-success' : 'text-muted' }}">Pembayaran</h6>
                                <small class="{{ $isPaid ? 'text-success' : 'text-muted' }}">
                                    {{ $isPaid ? 'Lunas' : 'Belum Bayar' }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-info text-center">
                <h5>Belum Ada Data Pendaftaran</h5>
                <p>Silakan mulai proses pendaftaran dengan mengklik tombol "Daftar Sekarang" di bawah.</p>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="row g-4">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="bg-light rounded p-4">
                        <h5 class="mb-3">Aksi Cepat</h5>
                        <div class="d-flex flex-column gap-2">
                            @if($gelombangAktif && !$pendaftar)
                                <a href="{{ route('siswa.pendaftaran') }}" class="btn btn-primary">
                                    <i class="fa fa-edit me-2"></i>Daftar Sekarang
                                </a>
                            @elseif($pendaftar && !($pendaftar->dataSiswa && $pendaftar->dataOrtu && $pendaftar->asalSekolah))
                                <a href="{{ route('siswa.pendaftaran') }}" class="btn btn-warning">
                                    <i class="fa fa-edit me-2"></i>Lengkapi Data Pribadi
                                </a>
                            @elseif($pendaftar && ($pendaftar->berkas->count() < 4))
                                <a href="{{ route('siswa.berkas') }}" class="btn btn-success">
                                    <i class="fa fa-upload me-2"></i>Upload Berkas
                                </a>
                            @endif
                            
                            <a href="{{ route('siswa.profile') }}" class="btn btn-outline-primary">
                                <i class="fa fa-user me-2"></i>Lihat Profil
                            </a>
                            <a href="{{ route('siswa.status') }}" class="btn btn-outline-info">
                                <i class="fa fa-eye me-2"></i>Cek Status
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="bg-light rounded p-4">
                        <h5 class="mb-3">Pengumuman</h5>
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle me-2"></i>
                            Pastikan semua berkas sudah diupload sebelum batas waktu pendaftaran.
                        </div>
                        @if($gelombangAktif)
                            <div class="alert alert-warning">
                                <i class="fa fa-clock me-2"></i>
                                Batas waktu pendaftaran: {{ $gelombangAktif->tgl_selesai->format('d F Y') }}
                            </div>
                        @else
                            <div class="alert alert-secondary">
                                <i class="fa fa-calendar me-2"></i>
                                Gelombang pendaftaran akan segera dibuka. Pantau terus pengumuman terbaru.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Dashboard Content End -->
@endsection