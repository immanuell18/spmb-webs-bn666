@extends('layouts.main')

@section('title', 'Tentang - SMK BAKTI NUSANTARA 666')

@section('content')
    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Tentang SMK BAKTI NUSANTARA 666</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="{{ route('beranda') }}">Beranda</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Tentang</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- About Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s" style="min-height: 400px;">
                    <div class="position-relative h-100">
                        <img class="img-fluid position-absolute w-100 h-100" src="{{ asset('assets/images/gedung-sekolah.jpg') }}" alt="Gedung SMK Bakti Nusantara 666" style="object-fit: cover;">
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                    <h6 class="section-title bg-white text-start text-primary pe-3">Profil Sekolah</h6>
                    <h1 class="mb-4">SMK BAKTI NUSANTARA 666</h1>
                    <p class="mb-4">SMK Bakti Nusantara 666 adalah sekolah menengah kejuruan terakreditasi A yang berkomitmen menghasilkan lulusan berkarakter, kompeten, dan siap kerja di era digital dengan 5 jurusan unggulan.</p>
                    <p class="mb-4">Berlokasi di Jl. Pendidikan Nusantara No. 666, Kota Nusantara, Jawa Barat, sekolah kami dipimpin oleh Drs. H. Budi Santoso, M.Pd dengan NPSN 20666777.</p>
                    <div class="row gy-2 gx-4 mb-4">
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Akreditasi A (Sangat Baik)</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>5 Jurusan Unggulan</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Lab Modern Lengkap</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Sertifikat BNSP</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Link & Match Industri</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Sertifikat ISO 9001:2015</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->

    <!-- Service Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Keunggulan</h6>
                <h1 class="mb-5">Mengapa Memilih SMK BAKTI NUSANTARA 666?</h1>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-graduation-cap text-primary mb-4"></i>
                            <h5 class="mb-3">Pendidikan Kejuruan Berkualitas</h5>
                            <p>Kurikulum sesuai kebutuhan industri 4.0 dengan sertifikasi kompetensi BNSP</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-users text-primary mb-4"></i>
                            <h5 class="mb-3">Guru Profesional Berpengalaman</h5>
                            <p>Tenaga pengajar bersertifikat dan berpengalaman industri yang kompeten</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-building text-primary mb-4"></i>
                            <h5 class="mb-3">Fasilitas Lab Lengkap</h5>
                            <p>Lab komputer, studio DKV, lab akuntansi dengan peralatan terbaru</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.7s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-trophy text-primary mb-4"></i>
                            <h5 class="mb-3">Prestasi Membanggakan</h5>
                            <p>Juara LKS Provinsi, Kompetisi Desain Nasional, Business Plan Competition</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Service End -->

    <!-- Team Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Visi & Misi</h6>
                <h1 class="mb-5">Visi dan Misi SMK BAKTI NUSANTARA 666</h1>
            </div>
            <div class="row g-4">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="team-item bg-light">
                        <div class="overflow-hidden">
                            <i class="fa fa-5x fa-eye text-primary d-block text-center py-4"></i>
                        </div>
                        <div class="position-relative d-flex justify-content-center" style="margin-top: -23px;">
                            <div class="bg-light d-flex justify-content-center pt-2 px-1">
                                <h4 class="text-primary">VISI</h4>
                            </div>
                        </div>
                        <div class="text-center p-4">
                            <p class="mb-0">"Menjadi SMK unggulan yang menghasilkan lulusan berkarakter, kompeten, dan siap kerja di era digital"</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="team-item bg-light">
                        <div class="overflow-hidden">
                            <i class="fa fa-5x fa-bullseye text-primary d-block text-center py-4"></i>
                        </div>
                        <div class="position-relative d-flex justify-content-center" style="margin-top: -23px;">
                            <div class="bg-light d-flex justify-content-center pt-2 px-1">
                                <h4 class="text-primary">MISI</h4>
                            </div>
                        </div>
                        <div class="text-center p-4">
                            <ul class="list-unstyled mb-0 text-start">
                                <li class="mb-2">• Menyelenggarakan pendidikan kejuruan berkualitas tinggi</li>
                                <li class="mb-2">• Mengembangkan kompetensi siswa sesuai kebutuhan industri</li>
                                <li class="mb-2">• Membangun karakter siswa yang berakhlak mulia</li>
                                <li class="mb-2">• Menyediakan fasilitas pembelajaran modern dan relevan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Team End -->
@endsection