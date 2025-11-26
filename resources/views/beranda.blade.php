@extends('layouts.main')

@section('title', 'Beranda - PPDB SMK BAKTI NUSANTARA 666')

@section('content')
    <!-- Carousel Start -->
    <div class="container-fluid p-0 mb-5">
        <div class="owl-carousel header-carousel position-relative">
            <div class="owl-carousel-item position-relative">
                <img class="img-fluid" src="{{ asset('assets/images/gedung-sekolah.jpg') }}" alt="Gedung SMK Bakti Nusantara 666">
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center" style="background: rgba(24, 29, 56, .7);">
                    <div class="container">
                        <div class="row justify-content-start">
                            <div class="col-sm-10 col-lg-8">
                                <h5 class="text-primary text-uppercase mb-3 animated slideInDown">SMK BAKTI NUSANTARA 666</h5>
                                <h1 class="display-3 text-white animated slideInDown">PPDB 2025/2026</h1>
                                <p class="fs-5 text-white mb-4 pb-2">Bergabunglah dengan SMK unggulan yang menghasilkan lulusan berkarakter, kompeten, dan siap kerja di era digital. Wujudkan impian kariermu bersama kami!</p>
                                <div class="d-flex flex-column flex-sm-row gap-2">
                                    <a href="{{ route('tentang') }}" class="btn btn-primary py-2 py-md-3 px-3 px-md-5 animated slideInLeft">Tentang Kami</a>
                                    <a href="{{ route('pendaftaran') }}" class="btn btn-light py-2 py-md-3 px-3 px-md-5 animated slideInRight">Daftar Sekarang</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="owl-carousel-item position-relative">
                <img class="img-fluid" src="{{ asset('assets/images/gerbang-sekolah.jpg') }}" alt="Gerbang SMK Bakti Nusantara 666">
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center" style="background: rgba(24, 29, 56, .7);">
                    <div class="container">
                        <div class="row justify-content-start">
                            <div class="col-sm-10 col-lg-8">
                                <h5 class="text-primary text-uppercase mb-3 animated slideInDown">Pendidikan Kejuruan Berkualitas</h5>
                                <h1 class="display-3 text-white animated slideInDown">5 Jurusan Unggulan</h1>
                                <p class="fs-5 text-white mb-4 pb-2">PPLG • AKT • DKV • ANM • BDP - Fasilitas modern, guru berpengalaman, dan kurikulum sesuai kebutuhan industri 4.0</p>
                                <div class="d-flex flex-column flex-sm-row gap-2">
                                    <a href="{{ route('tentang') }}" class="btn btn-primary py-2 py-md-3 px-3 px-md-5 animated slideInLeft">Tentang Kami</a>
                                    <a href="{{ route('pendaftaran') }}" class="btn btn-light py-2 py-md-3 px-3 px-md-5 animated slideInRight">Daftar Sekarang</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Carousel End -->

    <!-- Alur Pendaftaran Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Cara Mendaftar</h6>
                <h1 class="mb-5">Alur Pendaftaran PPDB 2025/2026</h1>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="team-item bg-light text-center">
                        <div class="overflow-hidden">
                            <div class="d-flex justify-content-center align-items-center" style="height: 200px; background: #06BBCC;">
                                <div class="text-center text-white">
                                    <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                        <i class="fas fa-user-plus fa-2x text-primary"></i>
                                    </div>
                                    <h3 class="text-white">01</h3>
                                </div>
                            </div>
                        </div>
                        <div class="position-relative d-flex justify-content-center" style="margin-top: -23px;">
                            <div class="bg-light d-flex justify-content-center pt-2 px-1" style="border-radius: 50px;">
                                <a class="btn btn-sm-square btn-primary mx-1" href="{{ route('register') }}"><i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                        <div class="text-center p-4">
                            <h5 class="mb-0">Daftar Akun</h5>
                            <small>Buat akun dengan email aktif dan verifikasi OTP</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="team-item bg-light text-center">
                        <div class="overflow-hidden">
                            <div class="d-flex justify-content-center align-items-center" style="height: 200px; background: #06BBCC;">
                                <div class="text-center text-white">
                                    <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                        <i class="fas fa-edit fa-2x text-primary"></i>
                                    </div>
                                    <h3 class="text-white">02</h3>
                                </div>
                            </div>
                        </div>
                        <div class="position-relative d-flex justify-content-center" style="margin-top: -23px;">
                            <div class="bg-light d-flex justify-content-center pt-2 px-1" style="border-radius: 50px;">
                                <a class="btn btn-sm-square btn-primary mx-1" href="{{ route('login') }}"><i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                        <div class="text-center p-4">
                            <h5 class="mb-0">Isi Formulir</h5>
                            <small>Lengkapi data pribadi, orang tua, dan asal sekolah</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="team-item bg-light text-center">
                        <div class="overflow-hidden">
                            <div class="d-flex justify-content-center align-items-center" style="height: 200px; background: #06BBCC;">
                                <div class="text-center text-white">
                                    <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                        <i class="fas fa-upload fa-2x text-primary"></i>
                                    </div>
                                    <h3 class="text-white">03</h3>
                                </div>
                            </div>
                        </div>
                        <div class="position-relative d-flex justify-content-center" style="margin-top: -23px;">
                            <div class="bg-light d-flex justify-content-center pt-2 px-1" style="border-radius: 50px;">
                                <a class="btn btn-sm-square btn-primary mx-1" href="{{ route('login') }}"><i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                        <div class="text-center p-4">
                            <h5 class="mb-0">Upload Berkas</h5>
                            <small>Upload ijazah, rapor, KK, akta kelahiran, dan foto</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.7s">
                    <div class="team-item bg-light text-center">
                        <div class="overflow-hidden">
                            <div class="d-flex justify-content-center align-items-center" style="height: 200px; background: #06BBCC;">
                                <div class="text-center text-white">
                                    <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                        <i class="fas fa-credit-card fa-2x text-primary"></i>
                                    </div>
                                    <h3 class="text-white">04</h3>
                                </div>
                            </div>
                        </div>
                        <div class="position-relative d-flex justify-content-center" style="margin-top: -23px;">
                            <div class="bg-light d-flex justify-content-center pt-2 px-1" style="border-radius: 50px;">
                                <a class="btn btn-sm-square btn-primary mx-1" href="{{ route('login') }}"><i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                        <div class="text-center p-4">
                            <h5 class="mb-0">Bayar & Tunggu</h5>
                            <small>Bayar biaya pendaftaran Rp 350.000 dan tunggu pengumuman</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- CTA Section -->
            <div class="row justify-content-center mt-5">
                <div class="col-lg-8 text-center">
                    <div class="bg-primary rounded p-5 wow fadeInUp" data-wow-delay="0.1s">
                        <h3 class="text-white mb-3">Siap Memulai Pendaftaran?</h3>
                        <p class="text-white mb-4">Jangan sampai terlewat! Kuota terbatas untuk setiap jurusan. Daftar sekarang dan wujudkan impian kariermu di SMK Bakti Nusantara 666.</p>
                        <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                            <a href="{{ route('register') }}" class="btn btn-light py-2 py-md-3 px-3 px-md-5">
                                <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                            </a>
                            <a href="{{ route('jurusan') }}" class="btn btn-outline-light py-2 py-md-3 px-3 px-md-5">
                                <i class="fas fa-info-circle me-2"></i>Info Jurusan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Alur Pendaftaran End -->

    <!-- Service Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-graduation-cap text-primary mb-4"></i>
                            <h5 class="mb-3">Guru Profesional</h5>
                            <p>Tenaga pengajar bersertifikat dan berpengalaman industri yang siap membimbing siswa</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-globe text-primary mb-4"></i>
                            <h5 class="mb-3">Lab Lengkap</h5>
                            <p>Lab komputer, studio DKV, lab akuntansi dengan peralatan dan software terbaru</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-home text-primary mb-4"></i>
                            <h5 class="mb-3">Kurikulum Industri</h5>
                            <p>Program keahlian sesuai kebutuhan dunia kerja dan perkembangan teknologi</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.7s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-book-open text-primary mb-4"></i>
                            <h5 class="mb-3">Sertifikasi Profesi</h5>
                            <p>Siswa mendapat sertifikat kompetensi dari BNSP dan sertifikat internasional</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Service End -->

    <!-- About Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s" style="min-height: 300px;">
                    <div class="position-relative h-100">
                        <img class="img-fluid position-absolute w-100 h-100 d-none d-lg-block" src="{{ asset('assets/images/siswa-belajar.jpg') }}" alt="Siswa SMK Bakti Nusantara 666" style="object-fit: cover;">
                        <img class="img-fluid w-100 d-block d-lg-none rounded" src="{{ asset('assets/images/siswa-belajar.jpg') }}" alt="Siswa SMK Bakti Nusantara 666" style="height: 250px; object-fit: cover;">
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                    <h6 class="section-title bg-white text-start text-primary pe-3">Tentang Kami</h6>
                    <h1 class="mb-4">SMK BAKTI NUSANTARA 666</h1>
                    <p class="mb-4">SMK Bakti Nusantara 666 adalah sekolah menengah kejuruan terakreditasi A yang berkomitmen menghasilkan lulusan berkarakter, kompeten, dan siap kerja di era digital.</p>
                    <p class="mb-4">Dengan 5 jurusan unggulan (PPLG, AKT, DKV, ANM, BDP), fasilitas modern, dan tenaga pengajar profesional, kami telah mencetak ribuan alumni yang sukses di dunia kerja dan wirausaha.</p>
                    <div class="row gy-2 gx-4 mb-4">
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Akreditasi A (Sangat Baik)</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>5 Jurusan Unggulan</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Sertifikat BNSP</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Lab Modern Lengkap</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Link & Match Industri</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Alumni Sukses</p>
                        </div>
                    </div>
                    <a class="btn btn-primary py-3 px-5 mt-2" href="{{ route('tentang') }}">Selengkapnya</a>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->

    <!-- Info Penting Start -->
    <div class="container-xxl py-5 bg-light">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-light text-center text-primary px-3">Informasi Penting</h6>
                <h1 class="mb-5">Yang Perlu Kamu Ketahui</h1>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-5 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="course-item bg-light h-100">
                        <div class="position-relative overflow-hidden">
                            <div class="text-center py-5" style="background: linear-gradient(135deg, #06BBCC 0%, #05a3b8 100%);">
                                <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="fas fa-calendar-alt fa-2x text-primary"></i>
                                </div>
                                <h4 class="text-white mb-0">Jadwal Pendaftaran</h4>
                            </div>
                        </div>
                        <div class="text-center p-4 pb-0">
                            @forelse($gelombang as $index => $item)
                            <div class="d-flex align-items-center {{ !$loop->last ? 'mb-3' : 'mb-4' }} p-3 rounded" style="background: #f8f9fa;">
                                <div class="{{ $item->status === 'aktif' ? 'bg-success' : 'bg-secondary' }} rounded-circle me-3" style="width: 8px; height: 8px;"></div>
                                <div class="text-start">
                                    <h6 class="mb-0 {{ $item->status !== 'aktif' ? 'text-muted' : '' }}">{{ $item->nama }}</h6>
                                    <small class="text-muted">{{ $item->tgl_mulai->format('d M') }} - {{ $item->tgl_selesai->format('d M Y') }}</small>
                                    @if($item->status !== 'aktif')
                                    <small class="badge bg-secondary ms-2">Tidak Aktif</small>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <div class="text-center p-4">
                                <i class="fas fa-info-circle text-muted mb-2" style="font-size: 2rem;"></i>
                                <p class="text-muted mb-4">Belum ada jadwal pendaftaran</p>
                            </div>
                            @endforelse
                        </div>
                        <div class="d-flex border-top">
                            <small class="flex-fill text-center border-end py-2"><i class="fa fa-clock text-primary me-2"></i>Pendaftaran</small>
                            <small class="flex-fill text-center py-2"><i class="fa fa-calendar text-primary me-2"></i>2025</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="course-item bg-light h-100">
                        <div class="position-relative overflow-hidden">
                            <div class="text-center py-5" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                                <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="fas fa-file-alt fa-2x text-warning"></i>
                                </div>
                                <h4 class="text-white mb-0">Berkas Diperlukan</h4>
                            </div>
                        </div>
                        <div class="text-center p-4 pb-0">
                            <div class="d-flex align-items-center mb-3 p-2 rounded" style="background: #f8f9fa;">
                                <i class="fas fa-file-pdf text-danger me-3"></i>
                                <span class="text-start">Ijazah SMP/MTs</span>
                            </div>
                            <div class="d-flex align-items-center mb-3 p-2 rounded" style="background: #f8f9fa;">
                                <i class="fas fa-file-pdf text-danger me-3"></i>
                                <span class="text-start">Rapor semester 1-5</span>
                            </div>
                            <div class="d-flex align-items-center mb-3 p-2 rounded" style="background: #f8f9fa;">
                                <i class="fas fa-file-pdf text-danger me-3"></i>
                                <span class="text-start">Kartu Keluarga</span>
                            </div>
                            <div class="d-flex align-items-center mb-3 p-2 rounded" style="background: #f8f9fa;">
                                <i class="fas fa-file-pdf text-danger me-3"></i>
                                <span class="text-start">Akta Kelahiran</span>
                            </div>
                            <div class="d-flex align-items-center mb-4 p-2 rounded" style="background: #f8f9fa;">
                                <i class="fas fa-image text-primary me-3"></i>
                                <span class="text-start">Pas foto 3x4</span>
                            </div>
                        </div>
                        <div class="d-flex border-top">
                            <small class="flex-fill text-center border-end py-2"><i class="fa fa-file text-primary me-2"></i>5 Berkas</small>
                            <small class="flex-fill text-center py-2"><i class="fa fa-upload text-primary me-2"></i>Digital</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Info Penting End -->

    <!-- Jurusan Unggulan Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Program Keahlian</h6>
                <h1 class="mb-5">5 Jurusan Unggulan</h1>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="course-item bg-light">
                        <div class="position-relative overflow-hidden">
                            <img class="img-fluid" src="{{ asset('assets/images/lab-komputer.jpg') }}" alt="PPLG">
                            <div class="w-100 d-flex justify-content-center position-absolute bottom-0 start-0 mb-4">
                                <a href="{{ route('jurusan') }}" class="flex-shrink-0 btn btn-sm btn-primary px-3 border-end" style="border-radius: 30px 0 0 30px;">Info Lengkap</a>
                                <a href="{{ route('register') }}" class="flex-shrink-0 btn btn-sm btn-primary px-3" style="border-radius: 0 30px 30px 0;">Daftar</a>
                            </div>
                        </div>
                        <div class="text-center p-4 pb-0">
                            <h3 class="mb-0">PPLG</h3>
                            <div class="mb-3">
                                <small class="fa fa-star text-warning"></small>
                                <small class="fa fa-star text-warning"></small>
                                <small class="fa fa-star text-warning"></small>
                                <small class="fa fa-star text-warning"></small>
                                <small class="fa fa-star text-warning"></small>
                                <small>(4.9)</small>
                            </div>
                            <h5 class="mb-4">Pengembangan Perangkat Lunak & Gim</h5>
                        </div>
                        <div class="d-flex border-top">
                            <small class="flex-fill text-center border-end py-2"><i class="fa fa-user-tie text-primary me-2"></i>Web Developer</small>
                            <small class="flex-fill text-center py-2"><i class="fa fa-clock text-primary me-2"></i>3 Tahun</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="course-item bg-light">
                        <div class="position-relative overflow-hidden">
                            <img class="img-fluid" src="{{ asset('assets/images/lab-akuntansi.jpg') }}" alt="AKT">
                            <div class="w-100 d-flex justify-content-center position-absolute bottom-0 start-0 mb-4">
                                <a href="{{ route('jurusan') }}" class="flex-shrink-0 btn btn-sm btn-primary px-3 border-end" style="border-radius: 30px 0 0 30px;">Info Lengkap</a>
                                <a href="{{ route('register') }}" class="flex-shrink-0 btn btn-sm btn-primary px-3" style="border-radius: 0 30px 30px 0;">Daftar</a>
                            </div>
                        </div>
                        <div class="text-center p-4 pb-0">
                            <h3 class="mb-0">AKT</h3>
                            <div class="mb-3">
                                <small class="fa fa-star text-warning"></small>
                                <small class="fa fa-star text-warning"></small>
                                <small class="fa fa-star text-warning"></small>
                                <small class="fa fa-star text-warning"></small>
                                <small class="fa fa-star text-warning"></small>
                                <small>(4.8)</small>
                            </div>
                            <h5 class="mb-4">Akuntansi & Keuangan Lembaga</h5>
                        </div>
                        <div class="d-flex border-top">
                            <small class="flex-fill text-center border-end py-2"><i class="fa fa-user-tie text-primary me-2"></i>Akuntan</small>
                            <small class="flex-fill text-center py-2"><i class="fa fa-clock text-primary me-2"></i>3 Tahun</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="course-item bg-light">
                        <div class="position-relative overflow-hidden">
                            <img class="img-fluid" src="{{ asset('assets/images/studio-dkv.jpg') }}" alt="DKV">
                            <div class="w-100 d-flex justify-content-center position-absolute bottom-0 start-0 mb-4">
                                <a href="{{ route('jurusan') }}" class="flex-shrink-0 btn btn-sm btn-primary px-3 border-end" style="border-radius: 30px 0 0 30px;">Info Lengkap</a>
                                <a href="{{ route('register') }}" class="flex-shrink-0 btn btn-sm btn-primary px-3" style="border-radius: 0 30px 30px 0;">Daftar</a>
                            </div>
                        </div>
                        <div class="text-center p-4 pb-0">
                            <h3 class="mb-0">DKV</h3>
                            <div class="mb-3">
                                <small class="fa fa-star text-warning"></small>
                                <small class="fa fa-star text-warning"></small>
                                <small class="fa fa-star text-warning"></small>
                                <small class="fa fa-star text-warning"></small>
                                <small class="fa fa-star text-warning"></small>
                                <small>(4.9)</small>
                            </div>
                            <h5 class="mb-4">Desain Komunikasi Visual</h5>
                        </div>
                        <div class="d-flex border-top">
                            <small class="flex-fill text-center border-end py-2"><i class="fa fa-user-tie text-primary me-2"></i>Desainer</small>
                            <small class="flex-fill text-center py-2"><i class="fa fa-clock text-primary me-2"></i>3 Tahun</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('jurusan') }}" class="btn btn-primary py-3 px-5">Lihat Semua Jurusan</a>
            </div>
        </div>
    </div>
    <!-- Jurusan Unggulan End -->
@endsection