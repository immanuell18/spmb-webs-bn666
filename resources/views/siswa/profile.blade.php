@extends('layouts.siswa')

@section('title', 'Profil Saya - SPMB')

@section('content')
    <!-- Page Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Profil Saya</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="{{ route('siswa.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Profil</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Profile Content Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    @if($pendaftar)
                    <!-- Data Pribadi -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fa fa-user me-2"></i>Data Pribadi</h5>
                        </div>
                        <div class="card-body bg-light">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="text-dark"><strong class="text-primary">Nama Lengkap:</strong> <span class="text-secondary">{{ $pendaftar->dataSiswa->nama ?? $pendaftar->nama ?? '-' }}</span></p>
                                    <p class="text-dark"><strong class="text-primary">NIK:</strong> <span class="text-secondary">{{ $pendaftar->dataSiswa->nik ?? '-' }}</span></p>
                                    <p class="text-dark"><strong class="text-primary">Tempat, Tanggal Lahir:</strong> <span class="text-secondary">
                                        {{ $pendaftar->dataSiswa ? ($pendaftar->dataSiswa->tmp_lahir ?? '-') : '-' }}, 
                                        {{ $pendaftar->dataSiswa && $pendaftar->dataSiswa->tgl_lahir ? \Carbon\Carbon::parse($pendaftar->dataSiswa->tgl_lahir)->format('d M Y') : '-' }}
                                    </span></p>
                                    <p class="text-dark"><strong class="text-primary">Jenis Kelamin:</strong> <span class="text-secondary">{{ $pendaftar->dataSiswa && $pendaftar->dataSiswa->jk == 'L' ? 'Laki-laki' : ($pendaftar->dataSiswa && $pendaftar->dataSiswa->jk == 'P' ? 'Perempuan' : '-') }}</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-dark"><strong class="text-primary">Email:</strong> <span class="text-secondary">{{ $pendaftar->email }}</span></p>
                                    <p class="text-dark"><strong class="text-primary">Alamat:</strong> <span class="text-secondary">{{ $pendaftar->dataSiswa ? ($pendaftar->dataSiswa->alamat ?? '-') : '-' }}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Orang Tua -->
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fa fa-users me-2"></i>Data Orang Tua/Wali</h5>
                        </div>
                        <div class="card-body bg-light">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="text-dark"><strong class="text-success">Nama Ayah:</strong> <span class="text-secondary">{{ $pendaftar->dataOrtu->nama_ayah ?? '-' }}</span></p>
                                    <p class="text-dark"><strong class="text-success">Pekerjaan Ayah:</strong> <span class="text-secondary">{{ $pendaftar->dataOrtu->pekerjaan_ayah ?? '-' }}</span></p>
                                    <p class="text-dark"><strong class="text-success">Nama Ibu:</strong> <span class="text-secondary">{{ $pendaftar->dataOrtu->nama_ibu ?? '-' }}</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-dark"><strong class="text-success">Pekerjaan Ibu:</strong> <span class="text-secondary">{{ $pendaftar->dataOrtu->pekerjaan_ibu ?? '-' }}</span></p>
                                    <p class="text-dark"><strong class="text-success">No. HP Ayah:</strong> <span class="text-secondary">{{ $pendaftar->dataOrtu->hp_ayah ?? '-' }}</span></p>
                                    <p class="text-dark"><strong class="text-success">No. HP Ibu:</strong> <span class="text-secondary">{{ $pendaftar->dataOrtu->hp_ibu ?? '-' }}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Sekolah -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fa fa-school me-2"></i>Data Asal Sekolah</h5>
                        </div>
                        <div class="card-body bg-light">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="text-dark"><strong class="text-info">Nama Sekolah:</strong> <span class="text-secondary">{{ $pendaftar->asalSekolah->nama_sekolah ?? '-' }}</span></p>
                                    <p class="text-dark"><strong class="text-info">Nilai Rata-rata:</strong> <span class="text-secondary">{{ $pendaftar->asalSekolah->nilai_rata ?? '-' }}</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-dark"><strong class="text-info">Alamat:</strong> <span class="text-secondary">{{ $pendaftar->asalSekolah->kabupaten ?? '-' }}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Pendaftaran -->
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-white">
                            <h5 class="mb-0"><i class="fa fa-graduation-cap me-2"></i>Data Pendaftaran</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>No. Pendaftaran:</strong> {{ $pendaftar->no_pendaftaran }}</p>
                                    <p><strong>Jurusan Pilihan:</strong> {{ $pendaftar->jurusan->nama ?? '-' }}</p>
                                    <p><strong>Gelombang:</strong> {{ $pendaftar->gelombang->nama ?? '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Tanggal Daftar:</strong> {{ $pendaftar->created_at->format('d M Y H:i') }}</p>
                                    <p><strong>Status Berkas:</strong> 
                                        <span class="badge badge-{{ $pendaftar->status_berkas == 'lengkap' ? 'success' : 'warning' }}">
                                            {{ ucfirst($pendaftar->status_berkas) }}
                                        </span>
                                    </p>
                                    <p><strong>Status Verifikasi:</strong> 
                                        <span class="badge badge-{{ $pendaftar->status == 'ADM_PASS' ? 'success' : ($pendaftar->status == 'ADM_REJECT' ? 'danger' : 'warning') }}">
                                            {{ $pendaftar->getStatusLabel() }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('siswa.pendaftaran') }}" class="btn btn-primary">
                            <i class="fa fa-edit me-2"></i>Edit Data
                        </a>
                    </div>

                    @else
                    <div class="alert alert-warning text-center">
                        <h5>Belum ada data profil</h5>
                        <p>Silakan lengkapi formulir pendaftaran terlebih dahulu.</p>
                        <a href="{{ route('siswa.pendaftaran') }}" class="btn btn-primary">Isi Formulir Pendaftaran</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- Profile Content End -->
@endsection