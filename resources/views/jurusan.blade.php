@extends('layouts.main')

@section('title', 'Jurusan - SMK Bakti Nusantara 666')

@section('content')
    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Program Keahlian</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="{{ route('beranda') }}">Beranda</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Jurusan</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Jurusan Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Program Keahlian</h6>
                <h1 class="mb-5">Pilih Jurusan Sesuai Minatmu</h1>
            </div>
            <div class="row g-4">
                @foreach($jurusan as $index => $j)
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="{{ 0.1 + ($index * 0.2) }}s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x {{ $j->icon ?? 'fa-graduation-cap' }} text-primary mb-4"></i>
                            <h5 class="mb-3">{{ $j->nama }} ({{ $j->kode }})</h5>
                            <p class="mb-4">{{ $j->deskripsi ?? 'Deskripsi jurusan belum tersedia.' }}</p>
                            <div class="row gy-2 gx-4 mb-4">
                                @if($j->kurikulum && is_array($j->kurikulum))
                                    @foreach($j->kurikulum as $materi)
                                    <div class="col-12">
                                        <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>{{ $materi }}</p>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                            <span class="badge bg-primary fs-6">Kuota: {{ $j->kuota }} siswa</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- Jurusan End -->

    <!-- Prospek Karir Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Prospek Karir</h6>
                <h1 class="mb-5">Peluang Kerja Lulusan</h1>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-code text-primary mb-4"></i>
                            <h5 class="mb-3">Lulusan PPLG</h5>
                            <p>Web Developer, Mobile Developer, Software Engineer, Game Developer</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6 wow fadeInUp" data-wow-delay="0.2s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-calculator text-primary mb-4"></i>
                            <h5 class="mb-3">Lulusan AKT</h5>
                            <p>Akuntan, Staff Keuangan, Auditor, Konsultan Pajak</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-play text-primary mb-4"></i>
                            <h5 class="mb-3">Lulusan ANM</h5>
                            <p>Animator 2D/3D, Motion Graphics Designer, Character Designer, VFX Artist</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6 wow fadeInUp" data-wow-delay="0.4s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-paint-brush text-primary mb-4"></i>
                            <h5 class="mb-3">Lulusan DKV</h5>
                            <p>Desainer Grafis, UI/UX Designer, Brand Designer, Creative Director</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-video text-primary mb-4"></i>
                            <h5 class="mb-3">Lulusan BDP</h5>
                            <p>Video Editor, Content Creator, Sutradara, Produser Film</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Prospek Karir End -->

    <!-- CTA Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="bg-light rounded p-5 wow fadeInUp" data-wow-delay="0.1s">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-8">
                        <h4 class="mb-3">Tertarik dengan salah satu jurusan?</h4>
                        <p class="mb-0">Daftar sekarang dan wujudkan impianmu bersama SMK Bakti Nusantara 666. Raih masa depan yang cerah dengan pendidikan berkualitas!</p>
                    </div>
                    <div class="col-lg-4 text-center">
                        <a class="btn btn-primary py-3 px-5" href="{{ route('pendaftaran') }}">Daftar Sekarang</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- CTA End -->
@endsection