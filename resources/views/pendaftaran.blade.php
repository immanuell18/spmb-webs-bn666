@extends('layouts.main')

@section('title', 'PPDB - SMK BAKTI NUSANTARA 666')

@section('content')
    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">PPDB 2025/2026</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="{{ route('beranda') }}">Beranda</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">PPDB</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Contact Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">PPDB SMK BAKTI NUSANTARA 666</h6>
                <h1 class="mb-5">Penerimaan Peserta Didik Baru 2025/2026</h1>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <h5>Informasi PPDB</h5>
                    <p class="mb-4">SMK BAKTI NUSANTARA 666 membuka penerimaan peserta didik baru untuk 5 jurusan unggulan dengan fasilitas modern dan tenaga pengajar profesional.</p>
                    <div class="d-flex align-items-center mb-3">
                        <div class="d-flex align-items-center justify-content-center flex-shrink-0 bg-primary" style="width: 50px; height: 50px;">
                            <i class="fa fa-map-marker-alt text-white"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="text-primary">Alamat</h5>
                            <p class="mb-0">Jl. Pendidikan Nusantara No. 666<br>Kota Nusantara, Jawa Barat 12345</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="d-flex align-items-center justify-content-center flex-shrink-0 bg-primary" style="width: 50px; height: 50px;">
                            <i class="fa fa-phone-alt text-white"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="text-primary">Telepon</h5>
                            <p class="mb-0">(021) 666-7777</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center justify-content-center flex-shrink-0 bg-primary" style="width: 50px; height: 50px;">
                            <i class="fa fa-envelope-open text-white"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="text-primary">Email</h5>
                            <p class="mb-0">info@smkbaktinusantara666.sch.id</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="bg-light p-4 rounded">
                        <h5 class="mb-4">Cara Mendaftar PPDB Online</h5>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6><i class="fa fa-info-circle me-2"></i>PPDB SMK BAKTI NUSANTARA 666 - 100% Online</h6>
                                    <p class="mb-0">Daftar mudah, cepat, dan aman. Silakan buat akun untuk memulai pendaftaran ke 5 jurusan unggulan kami.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <span class="fw-bold">1</span>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="mb-1">Buat Akun</h6>
                                        <small class="text-muted">Daftar dengan email valid</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <span class="fw-bold">2</span>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="mb-1">Verifikasi Email</h6>
                                        <small class="text-muted">Masukkan kode OTP</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <span class="fw-bold">3</span>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="mb-1">Isi Data Diri</h6>
                                        <small class="text-muted">Lengkapi formulir pendaftaran</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <span class="fw-bold">4</span>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="mb-1">Upload Berkas</h6>
                                        <small class="text-muted">Upload dokumen persyaratan</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 text-center mt-4">
                                @auth
                                    @if(auth()->user()->role === 'pendaftar')
                                        <a href="{{ route('siswa.dashboard') }}" class="btn btn-primary btn-lg px-5">
                                            <i class="fa fa-user me-2"></i>Dashboard Siswa
                                        </a>
                                    @else
                                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5">
                                            <i class="fa fa-user-plus me-2"></i>Daftar Sebagai Calon Siswa
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5 me-3">
                                        <i class="fa fa-user-plus me-2"></i>Daftar Akun
                                    </a>
                                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg px-5">
                                        <i class="fa fa-sign-in-alt me-2"></i>Login
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Contact End -->

    <!-- Persyaratan Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Persyaratan</h6>
                <h1 class="mb-5">Persyaratan Pendaftaran</h1>
            </div>
            <div class="row g-4">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="service-item bg-light text-center h-100 p-4">
                        <i class="fa fa-3x fa-file-alt text-primary mb-4"></i>
                        <h5 class="mb-3">Dokumen Akademik</h5>
                        <ul class="list-unstyled text-start">
                            <li class="mb-2">• Fotokopi Ijazah/SKHUN yang dilegalisir</li>
                            <li class="mb-2">• Fotokopi Rapor semester 1-6 yang dilegalisir</li>
                            <li class="mb-2">• Surat Keterangan Nilai (SKN) dari sekolah asal</li>
                            <li class="mb-2">• Sertifikat prestasi (jika ada)</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="service-item bg-light text-center h-100 p-4">
                        <i class="fa fa-3x fa-user text-primary mb-4"></i>
                        <h5 class="mb-3">Dokumen Pribadi</h5>
                        <ul class="list-unstyled text-start">
                            <li class="mb-2">• Fotokopi Kartu Keluarga (KK)</li>
                            <li class="mb-2">• Fotokopi Akta Kelahiran</li>
                            <li class="mb-2">• Fotokopi KTP Orang Tua/Wali</li>
                            <li class="mb-2">• Pas foto 3x4 sebanyak 6 lembar</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row g-4 mt-2">
                <div class="col-lg-12 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="bg-primary text-white text-center p-4 rounded">
                        <h4 class="text-white mb-3">Jadwal PPDB 2025/2026</h4>
                        <div class="row">
                            @forelse($gelombang as $g)
                                <div class="col-md-4 mb-3">
                                    <div class="text-center">
                                        <h6 class="text-white">{{ $g->nama }}</h6>
                                        <p class="mb-1">{{ $g->tgl_mulai->format('d M') }} - {{ $g->tgl_selesai->format('d M Y') }}</p>
                                        <small class="text-white-50">Biaya: Rp {{ number_format($g->biaya_daftar ?: \App\Models\SystemSetting::getBiayaPendaftaran(), 0, ',', '.') }}</small>
                                        @if($g->isActive())
                                            <br><span class="badge bg-success mt-1">Sedang Buka</span>
                                        @elseif($g->tgl_mulai > now())
                                            <br><span class="badge bg-warning mt-1">Akan Dibuka</span>
                                        @else
                                            <br><span class="badge bg-secondary mt-1">Sudah Tutup</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center">
                                    <p class="text-white mb-0">Jadwal pendaftaran belum tersedia</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Persyaratan End -->
@endsection