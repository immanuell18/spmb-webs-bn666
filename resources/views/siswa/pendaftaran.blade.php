@extends('layouts.siswa')

@section('title', 'Form Pendaftaran - SPMB')

@section('content')
    <!-- Page Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Form Pendaftaran</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="{{ route('siswa.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Pendaftaran</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Registration Form Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Pendaftaran</h6>
                <h1 class="mb-5">Formulir Pendaftaran Siswa Baru</h1>
                <div class="alert alert-info">
                    <i class="fa fa-info-circle me-2"></i>
                    <strong>Draft Otomatis:</strong> Data yang Anda input akan tersimpan otomatis di browser ini dan dapat dilanjutkan kapan saja.
                    <div class="mt-2">
                        <small>Status Draft: <span id="draftStatus" class="badge bg-secondary">Belum ada draft</span></small>
                        <button type="button" class="btn btn-sm btn-outline-info ms-2" onclick="checkDraft()">Cek Draft</button>
                    </div>
                </div>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <form class="wow fadeInUp" data-wow-delay="0.3s" method="POST" action="{{ route('siswa.pendaftaran.store') }}">
                        <meta name="csrf-token" content="{{ csrf_token() }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        
                        <!-- Data Pribadi -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fa fa-user me-2"></i>Data Pribadi</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" name="nama_lengkap" id="nama_lengkap" placeholder="Nama Lengkap" value="{{ old('nama_lengkap', $pendaftar->dataSiswa->nama_lengkap ?? '') }}" required>
                                            <label for="nama_lengkap">Nama Lengkap</label>
                                            @error('nama_lengkap')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('nik') is-invalid @enderror" name="nik" id="nik" placeholder="NIK" value="{{ old('nik', $pendaftar->dataSiswa->nik ?? '') }}" maxlength="16" required>
                                            <label for="nik">NIK</label>
                                            @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="nisn" name="nisn" placeholder="NISN" value="{{ old('nisn') }}" maxlength="10">
                                            <label for="nisn">NISN (Opsional)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
                                            <label for="tanggal_lahir">Tanggal Lahir</label>
                                            @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror" name="tempat_lahir" id="tempat_lahir" placeholder="Tempat Lahir" value="{{ old('tempat_lahir') }}" required>
                                            <label for="tempat_lahir">Tempat Lahir</label>
                                            @error('tempat_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select @error('jenis_kelamin') is-invalid @enderror" name="jenis_kelamin" id="jenis_kelamin" required>
                                                <option value="">Pilih Jenis Kelamin</option>
                                                <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                                <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                            </select>
                                            <label for="jenis_kelamin">Jenis Kelamin</label>
                                            @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select @error('agama') is-invalid @enderror" name="agama" id="agama" required>
                                                <option value="">Pilih Agama</option>
                                                <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                                                <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                                <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                                <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                                <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                                <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                                            </select>
                                            <label for="agama">Agama</label>
                                            @error('agama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('no_hp') is-invalid @enderror" name="no_hp" id="telepon" placeholder="No. Telepon" value="{{ old('no_hp') }}" required>
                                            <label for="telepon">No. Telepon</label>
                                            @error('no_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select" id="provinsi" name="provinsi" required>
                                                <option value="">Pilih Provinsi</option>
                                            </select>
                                            <label for="provinsi">Provinsi</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select" id="kabupaten" name="kabupaten" required disabled>
                                                <option value="">Pilih Kabupaten</option>
                                            </select>
                                            <label for="kabupaten">Kabupaten/Kota</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select" id="kecamatan" name="kecamatan" required disabled>
                                                <option value="">Pilih Kecamatan</option>
                                            </select>
                                            <label for="kecamatan">Kecamatan</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select" id="kelurahan" name="kelurahan" required disabled>
                                                <option value="">Pilih Kelurahan</option>
                                            </select>
                                            <label for="kelurahan">Kelurahan/Desa</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control @error('alamat') is-invalid @enderror" name="alamat" placeholder="Alamat" id="alamat" style="height: 100px" required>{{ old('alamat') }}</textarea>
                                            <label for="alamat">Alamat Lengkap (Jalan, RT/RW, No. Rumah)</label>
                                            @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    
                                    <!-- Enhanced Coordinate Picker -->
                                    <div class="col-12">
                                        <div class="card border-info">
                                            <div class="card-header bg-info text-white">
                                                <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Lokasi Koordinat (Opsional - Membantu Pemetaan Sebaran)</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    <strong>Tips:</strong> Koordinat membantu sekolah dalam analisis sebaran geografis siswa. 
                                                    Gunakan tombol "Lokasi Saya" untuk akurasi terbaik atau klik pada peta.
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                                </div>
                                                
                                                <div class="row g-3">
                                                    <div class="col-md-3">
                                                        <div class="form-floating">
                                                            <input type="number" class="form-control @error('latitude') is-invalid @enderror" 
                                                                   name="latitude" id="latitude" placeholder="Latitude" 
                                                                   step="0.000001" min="-11" max="6" 
                                                                   value="{{ old('latitude') }}">
                                                            <label for="latitude">Latitude</label>
                                                            @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-floating">
                                                            <input type="number" class="form-control @error('longitude') is-invalid @enderror" 
                                                                   name="longitude" id="longitude" placeholder="Longitude" 
                                                                   step="0.000001" min="95" max="141" 
                                                                   value="{{ old('longitude') }}">
                                                            <label for="longitude">Longitude</label>
                                                            @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 d-flex align-items-end gap-2">
                                                        <button type="button" class="btn btn-info" onclick="window.getCurrentLocation()" id="btnCurrentLocation">
                                                            <i class="fas fa-crosshairs"></i> Lokasi Saya
                                                        </button>
                                                        <button type="button" class="btn btn-warning" onclick="window.clearCoordinates()">
                                                            <i class="fas fa-times"></i> Hapus
                                                        </button>
                                                    </div>
                                                    
                                                    <!-- Coordinate Status -->
                                                    <div class="col-12">
                                                        <div id="coordinate-status" class="alert alert-secondary d-none" role="alert">
                                                            <i class="fas fa-map-marker-alt me-2"></i>
                                                            <span id="coordinate-text">Belum ada koordinat</span>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Interactive Map -->
                                                    <div class="col-12">
                                                        <div class="border rounded" style="height: 350px; position: relative;">
                                                            <div id="coordinate-map" style="height: 100%; border-radius: 8px;"></div>
                                                            <div id="map-loading" class="position-absolute top-50 start-50 translate-middle d-none">
                                                                <div class="spinner-border text-primary" role="status">
                                                                    <span class="visually-hidden">Loading...</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Validation Info -->
                                                    <div class="col-12">
                                                        <div class="row text-center">
                                                            <div class="col-md-4">
                                                                <small class="text-success">
                                                                    <i class="fas fa-check-circle"></i> 
                                                                    Latitude: -11.0 s/d 6.0
                                                                </small>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <small class="text-success">
                                                                    <i class="fas fa-check-circle"></i> 
                                                                    Longitude: 95.0 s/d 141.0
                                                                </small>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <small class="text-info">
                                                                    <i class="fas fa-globe-asia"></i> 
                                                                    Wilayah Indonesia
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    @error('coordinates')
                                                    <div class="col-12">
                                                        <div class="alert alert-danger" role="alert">
                                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                                            {{ $message }}
                                                        </div>
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Orang Tua -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fa fa-users me-2"></i>Data Orang Tua/Wali</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('nama_ayah') is-invalid @enderror" name="nama_ayah" id="nama_ayah" placeholder="Nama Ayah" value="{{ old('nama_ayah') }}" required>
                                            <label for="nama_ayah">Nama Ayah</label>
                                            @error('nama_ayah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('nama_ibu') is-invalid @enderror" name="nama_ibu" id="nama_ibu" placeholder="Nama Ibu" value="{{ old('nama_ibu') }}" required>
                                            <label for="nama_ibu">Nama Ibu</label>
                                            @error('nama_ibu')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('pekerjaan_ayah') is-invalid @enderror" name="pekerjaan_ayah" id="pekerjaan_ayah" placeholder="Pekerjaan Ayah" value="{{ old('pekerjaan_ayah') }}" required>
                                            <label for="pekerjaan_ayah">Pekerjaan Ayah</label>
                                            @error('pekerjaan_ayah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('pekerjaan_ibu') is-invalid @enderror" name="pekerjaan_ibu" id="pekerjaan_ibu" placeholder="Pekerjaan Ibu" value="{{ old('pekerjaan_ibu') }}" required>
                                            <label for="pekerjaan_ibu">Pekerjaan Ibu</label>
                                            @error('pekerjaan_ibu')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('no_hp_ortu') is-invalid @enderror" name="no_hp_ortu" id="telepon_ortu" placeholder="No. Telepon Orang Tua" value="{{ old('no_hp_ortu') }}" required>
                                            <label for="telepon_ortu">No. Telepon Orang Tua</label>
                                            @error('no_hp_ortu')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select @error('penghasilan_ortu') is-invalid @enderror" name="penghasilan_ortu" id="penghasilan_ortu" required>
                                                <option value="">Pilih Penghasilan</option>
                                                <option value="< 1 juta" {{ old('penghasilan_ortu') == '< 1 juta' ? 'selected' : '' }}>< Rp 1.000.000</option>
                                                <option value="1-3 juta" {{ old('penghasilan_ortu') == '1-3 juta' ? 'selected' : '' }}>Rp 1.000.000 - 3.000.000</option>
                                                <option value="3-5 juta" {{ old('penghasilan_ortu') == '3-5 juta' ? 'selected' : '' }}>Rp 3.000.000 - 5.000.000</option>
                                                <option value="> 5 juta" {{ old('penghasilan_ortu') == '> 5 juta' ? 'selected' : '' }}>> Rp 5.000.000</option>
                                            </select>
                                            <label for="penghasilan_ortu">Penghasilan Orang Tua</label>
                                            @error('penghasilan_ortu')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Pendidikan -->
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fa fa-school me-2"></i>Data Pendidikan</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('nama_sekolah') is-invalid @enderror" name="nama_sekolah" id="asal_sekolah" placeholder="Asal Sekolah" value="{{ old('nama_sekolah') }}" required>
                                            <label for="asal_sekolah">Nama Sekolah</label>
                                            @error('nama_sekolah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="npsn" name="npsn" placeholder="NPSN" value="{{ old('npsn') }}" maxlength="8">
                                            <label for="npsn">NPSN Sekolah (Opsional)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="number" class="form-control @error('tahun_lulus') is-invalid @enderror" name="tahun_lulus" id="tahun_lulus" placeholder="Tahun Lulus" value="{{ old('tahun_lulus') }}" required>
                                            <label for="tahun_lulus">Tahun Lulus</label>
                                            @error('tahun_lulus')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" id="nilai_rata" name="nilai_rata" placeholder="Nilai Rata-rata" step="0.01" value="{{ old('nilai_rata') }}" required>
                                            <label for="nilai_rata">Nilai Rata-rata</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select @error('jurusan_id') is-invalid @enderror" name="jurusan_id" id="jurusan_pilihan" required onchange="validateJurusanSelection(this)">
                                                <option value="">Pilih Jurusan</option>
                                                @foreach($jurusan as $j)
                                                    @php
                                                        $sisaKuota = $j->kuota - $j->pendaftar_count;
                                                        $isPenuh = $sisaKuota <= 0;
                                                    @endphp
                                                    <option value="{{ $j->id }}" 
                                                            {{ old('jurusan_id') == $j->id ? 'selected' : '' }}
                                                            {{ $isPenuh ? 'disabled' : '' }}>
                                                        {{ $j->nama }} 
                                                        ({{ $sisaKuota }}/{{ $j->kuota }} tersisa)
                                                        {{ $isPenuh ? ' - PENUH' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label for="jurusan_pilihan">Jurusan Pilihan</label>
                                            @error('jurusan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">Kuota tersedia untuk setiap jurusan:</small>
                                            @foreach($jurusan as $j)
                                                @php
                                                    $sisaKuota = $j->kuota - $j->pendaftar_count;
                                                    $isPenuh = $sisaKuota <= 0;
                                                @endphp
                                                <div class="d-flex justify-content-between align-items-center mt-1">
                                                    <span class="badge bg-{{ $isPenuh ? 'danger' : 'success' }}">{{ $j->kode }}</span>
                                                    <small class="{{ $isPenuh ? 'text-danger' : 'text-success' }}">
                                                        {{ $sisaKuota }}/{{ $j->kuota }} tersisa
                                                        {{ $isPenuh ? ' (PENUH)' : '' }}
                                                    </small>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select @error('gelombang_id') is-invalid @enderror" id="gelombang_id" name="gelombang_id" required>
                                                <option value="">Pilih Gelombang</option>
                                                @foreach($gelombangAktif as $g)
                                                    <option value="{{ $g->id }}" {{ old('gelombang_id') == $g->id ? 'selected' : '' }}>
                                                        {{ $g->nama }} - {{ $g->tahun }} 
                                                        ({{ $g->tgl_mulai->format('d/m/Y') }} - {{ $g->tgl_selesai->format('d/m/Y') }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label>Gelombang Pendaftaran</label>
                                            @error('gelombang_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control @error('alamat_sekolah') is-invalid @enderror" id="alamat_sekolah" name="alamat_sekolah" placeholder="Alamat Sekolah" style="height: 80px" required>{{ old('alamat_sekolah') }}</textarea>
                                            <label>Alamat Sekolah</label>
                                            @error('alamat_sekolah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button class="btn btn-warning py-3 px-4 me-2" type="button" onclick="clearDraft()">
                                <i class="fa fa-trash me-2"></i>Hapus Draft
                            </button>
                            <button class="btn btn-primary py-3 px-5" type="submit" id="submitBtn">
                                <i class="fa fa-save me-2"></i>Simpan & Lanjut ke Upload Berkas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Registration Form End -->
@endsection

@section('scripts')
    @parent
    <script>
    $(document).ready(function() {
        const userId = {{ Auth::id() }};
        const draftKey = 'pendaftaran_draft_' + userId;
        
        // Load draft data with proper cascade loading
        function loadDraft() {
            const draft = localStorage.getItem(draftKey);
            if (draft) {
                const data = JSON.parse(draft);
                console.log('Loading draft:', data);
                
                // Load non-cascade fields first
                Object.keys(data).forEach(function(key) {
                    if (!['provinsi', 'kabupaten', 'kecamatan', 'kelurahan'].includes(key)) {
                        let element = $('#' + key);
                        if (!element.length) {
                            element = $('[name="' + key + '"]');
                        }
                        if (element.length) {
                            element.val(data[key]);
                        }
                    }
                });
                
                if (data.latitude && data.longitude && typeof updateMapFromInputs === 'function') {
                    updateMapFromInputs(data.latitude, data.longitude);
                }
                
                // Load cascade fields with delays
                if (data.provinsi) {
                    $('#provinsi').val(data.provinsi).trigger('change');
                    
                    setTimeout(() => {
                        if (data.kabupaten) {
                            $('#kabupaten').val(data.kabupaten).trigger('change');
                            
                            setTimeout(() => {
                                if (data.kecamatan) {
                                    $('#kecamatan').val(data.kecamatan).trigger('change');
                                    
                                    setTimeout(() => {
                                        if (data.kelurahan) {
                                            $('#kelurahan').val(data.kelurahan);
                                        }
                                    }, 500);
                                }
                            }, 500);
                        }
                    }, 500);
                }
            }
        }
        
        // Save draft data
        function saveDraft() {
            const formData = {};
            $('input, select, textarea').each(function() {
                const id = $(this).attr('id') || $(this).attr('name');
                if (id && $(this).val()) {
                    formData[id] = $(this).val();
                }
            });
            localStorage.setItem(draftKey, JSON.stringify(formData));
            console.log('Draft saved:', formData);
        }
        
        // Auto save on input change - use event delegation for dynamic elements
        $(document).on('input change', 'input, select, textarea', function() {
            setTimeout(saveDraft, 100);
        });
        
        // Specific handler for kelurahan to ensure it saves
        $(document).on('change', '#kelurahan', function() {
            console.log('Kelurahan changed:', $(this).val());
            saveDraft();
        });
        
        // ================================================
        // WILAYAH CASCADE DROPDOWN
        // Sumber data: emsifa API via Laravel proxy + cache
        // https://github.com/emsifa/api-wilayah-indonesia
        // ================================================

        function setDropdownLoading(id, isLoading) {
            const $el = $('#' + id);
            const $label = $('label[for="' + id + '"]');
            if (isLoading) {
                $el.prop('disabled', true);
                $label.html($label.text().replace(/ ⏳/g, '') + ' ⏳');
            } else {
                $label.html($label.text().replace(/ ⏳/g, ''));
            }
        }

        function populateDropdown(id, data, emptyText) {
            const $el = $('#' + id);
            $el.empty().append('<option value="">' + emptyText + '</option>');
            if (!data || data.length === 0) {
                $el.append('<option value="" disabled>Tidak ada data</option>');
                $el.prop('disabled', true);
                return;
            }
            $.each(data, function(i, item) {
                $el.append('<option value="' + item.id + '">' + item.name + '</option>');
            });
            $el.prop('disabled', false);
        }

        // Load Provinsi saat halaman dibuka
        setDropdownLoading('provinsi', true);
        $.get('/api/wilayah/provinsi', function(data) {
            populateDropdown('provinsi', data, 'Pilih Provinsi');
            setDropdownLoading('provinsi', false);
            // Load draft setelah provinsi ready
            setTimeout(loadDraft, 300);
        }).fail(function() {
            $('#provinsi').empty().append('<option value="">⚠ Gagal memuat data provinsi</option>');
            setDropdownLoading('provinsi', false);
            console.error('Gagal load provinsi dari API');
        });

        // Load Kabupaten saat Provinsi dipilih
        $('#provinsi').on('change', function() {
            const provinsiId = $(this).val();
            saveDraft();

            // Reset dropdown di bawahnya
            $('#kabupaten').empty().append('<option value="">Pilih Kabupaten/Kota</option>').prop('disabled', true);
            $('#kecamatan').empty().append('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
            $('#kelurahan').empty().append('<option value="">Pilih Kelurahan/Desa</option>').prop('disabled', true);

            if (!provinsiId) return;

            setDropdownLoading('kabupaten', true);
            $.get('/api/wilayah/kabupaten/' + provinsiId, function(data) {
                populateDropdown('kabupaten', data, 'Pilih Kabupaten/Kota');
                setDropdownLoading('kabupaten', false);
            }).fail(function() {
                $('#kabupaten').empty().append('<option value="">⚠ Gagal memuat kabupaten</option>');
                setDropdownLoading('kabupaten', false);
            });
        });

        // Load Kecamatan saat Kabupaten dipilih
        $('#kabupaten').on('change', function() {
            const provinsiId  = $('#provinsi').val();
            const kabupatenId = $(this).val();
            saveDraft();

            // Reset dropdown di bawahnya
            $('#kecamatan').empty().append('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
            $('#kelurahan').empty().append('<option value="">Pilih Kelurahan/Desa</option>').prop('disabled', true);

            if (!kabupatenId) return;

            setDropdownLoading('kecamatan', true);
            $.get('/api/wilayah/kecamatan/' + provinsiId + '/' + kabupatenId, function(data) {
                populateDropdown('kecamatan', data, 'Pilih Kecamatan');
                setDropdownLoading('kecamatan', false);
            }).fail(function() {
                $('#kecamatan').empty().append('<option value="">⚠ Gagal memuat kecamatan</option>');
                setDropdownLoading('kecamatan', false);
            });
        });

        // Load Kelurahan saat Kecamatan dipilih
        $('#kecamatan').on('change', function() {
            const provinsiId  = $('#provinsi').val();
            const kabupatenId = $('#kabupaten').val();
            const kecamatanId = $(this).val();
            saveDraft();

            $('#kelurahan').empty().append('<option value="">Pilih Kelurahan/Desa</option>').prop('disabled', true);

            if (!kecamatanId) return;

            setDropdownLoading('kelurahan', true);
            $.get('/api/wilayah/kelurahan/' + provinsiId + '/' + kabupatenId + '/' + kecamatanId, function(data) {
                populateDropdown('kelurahan', data, 'Pilih Kelurahan/Desa');
                setDropdownLoading('kelurahan', false);
            }).fail(function() {
                $('#kelurahan').empty().append('<option value="">⚠ Gagal memuat kelurahan</option>');
                setDropdownLoading('kelurahan', false);
            });
        });

        // Simpan kelurahan saat berubah
        $(document).on('change', '#kelurahan', function() {
            saveDraft();
        });

        
        // Clear draft function
        window.clearDraft = function() {
            if (confirm('Apakah Anda yakin ingin menghapus draft?')) {
                localStorage.removeItem(draftKey);
                $('form')[0].reset();
                $('#kabupaten, #kecamatan, #kelurahan').prop('disabled', true).empty().append('<option value="">Pilih...</option>');
                $('#draftStatus').removeClass('bg-success bg-warning').addClass('bg-secondary').text('Belum ada draft');
                alert('Draft berhasil dihapus!');
            }
        };
        
        // Check draft function
        window.checkDraft = function() {
            const draft = localStorage.getItem(draftKey);
            if (draft) {
                const data = JSON.parse(draft);
                const fieldCount = Object.keys(data).length;
                alert(`Draft ditemukan dengan ${fieldCount} field tersimpan di browser ini`);
            } else {
                alert('Tidak ada draft yang tersimpan di browser ini.');
            }
        };
        
        // Update draft status on page load
        setTimeout(() => {
            const draft = localStorage.getItem(draftKey);
            if (draft) {
                const data = JSON.parse(draft);
                const fieldCount = Object.keys(data).length;
                $('#draftStatus').removeClass('bg-secondary').addClass('bg-warning').text(`${fieldCount} field tersimpan`);
            }
        }, 1000);
        

        
        // Form submission with quota validation
        $('form').on('submit', function(e) {
            const selectedJurusan = $('#jurusan_pilihan option:selected');
            if (selectedJurusan.is(':disabled')) {
                e.preventDefault();
                alert('Jurusan yang dipilih sudah penuh! Silakan pilih jurusan lain.');
                return false;
            }
            $('#submitBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>Menyimpan...');
        });
        
        // Prevent selecting disabled options
        $('#jurusan_pilihan').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            if (selectedOption.is(':disabled')) {
                $(this).val('');
                alert('Jurusan ini sudah penuh! Silakan pilih jurusan lain.');
            }
        });
        
        // Clear draft on successful submission
        @if(session('success'))
            localStorage.removeItem(draftKey);
            console.log('Draft cleared after successful submission');
        @endif
    });
        
        // Validate jurusan selection
        window.validateJurusanSelection = function(select) {
            const selectedOption = select.options[select.selectedIndex];
            if (selectedOption && selectedOption.disabled) {
                select.value = '';
                alert('Jurusan ini sudah penuh! Silakan pilih jurusan lain.');
                return false;
            }
            return true;
        };
    </script>
    
    <!-- Map Script -->
    <script>
    // Enhanced coordinate picker
    let coordinateMap, currentMarker;
    
    $(document).ready(function() {
        if (typeof L === 'undefined') {
            const leafletCSS = document.createElement('link');
            leafletCSS.rel = 'stylesheet';
            leafletCSS.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            document.head.appendChild(leafletCSS);

            const leafletJS = document.createElement('script');
            leafletJS.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            leafletJS.onload = () => setTimeout(initEnhancedCoordinatePicker, 500);
            document.head.appendChild(leafletJS);
        } else {
            setTimeout(initEnhancedCoordinatePicker, 500);
        }
    });
    
    function initEnhancedCoordinatePicker() {
        coordinateMap = L.map('coordinate-map').setView([-2.5, 118], 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(coordinateMap);
        
        coordinateMap.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            
            // Isi input
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lng.toFixed(6);
            $('#latitude, #longitude').trigger('input');
            
            // Hapus marker lama
            if (currentMarker) {
                coordinateMap.removeLayer(currentMarker);
            }
            
            // Tambah marker baru
            currentMarker = L.marker([lat, lng]).addTo(coordinateMap);
        });

        // Check initial coordinates from draft or old input
        const initLat = document.getElementById('latitude').value;
        const initLng = document.getElementById('longitude').value;
        if (initLat && initLng) {
            updateMapFromInputs(initLat, initLng);
        }
    }

    function updateMapFromInputs(lat, lng) {
        if (!coordinateMap) return;
        
        let pLat = parseFloat(lat);
        let pLng = parseFloat(lng);
        
        if (!isNaN(pLat) && !isNaN(pLng)) {
            if (currentMarker) {
                coordinateMap.removeLayer(currentMarker);
            }
            currentMarker = L.marker([pLat, pLng]).addTo(coordinateMap);
            
            // Render tiles properly and animate zoom to location
            setTimeout(() => {
                coordinateMap.invalidateSize();
                coordinateMap.flyTo([pLat, pLng], 16, {
                    animate: true,
                    duration: 1.5
                });
            }, 100);
        } else {
            if (currentMarker) {
                coordinateMap.removeLayer(currentMarker);
                currentMarker = null;
            }
            setTimeout(() => {
                coordinateMap.invalidateSize();
                coordinateMap.flyTo([-2.5, 118], 5, {
                    animate: true,
                    duration: 1.5
                });
            }, 100);
        }
    }

    // Listener interaktif form inputs
    $('#latitude, #longitude').on('input change', function() {
        const lat = $('#latitude').val();
        const lng = $('#longitude').val();
        if (typeof updateMapFromInputs === 'function') {
            updateMapFromInputs(lat, lng);
        }
    });
    
    window.getCurrentLocation = function() {
        if (!navigator.geolocation) {
            alert('Browser tidak mendukung GPS');
            return;
        }
        
        const btn = $('#btnCurrentLocation');
        const originalText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin"></i> Mencari...');
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                // Isi input koordinat
                document.getElementById('latitude').value = lat.toFixed(6);
                document.getElementById('longitude').value = lng.toFixed(6);
                $('#latitude, #longitude').trigger('input');
                
                if (typeof updateMapFromInputs === 'function') updateMapFromInputs(lat, lng);
                btn.html(originalText);
            },
            function(error) {
                let msg = 'Gagal mendapatkan lokasi: ';
                if (error.code === 1) msg += 'Akses ditolak';
                else if (error.code === 2) msg += 'Lokasi tidak tersedia';
                else if (error.code === 3) msg += 'Timeout';
                
                alert(msg);
                btn.html(originalText);
            },
            { enableHighAccuracy: true, timeout: 15000 }
        );
    };
    

    
    function setCoordinates(lat, lng, source) {
        $('#latitude').val(parseFloat(lat).toFixed(6));
        $('#longitude').val(parseFloat(lng).toFixed(6));
        
        if (currentMarker) coordinateMap.removeLayer(currentMarker);
        currentMarker = L.marker([lat, lng]).addTo(coordinateMap);
        
        coordinateMap.flyTo([lat, lng], 15, {
            animate: true,
            duration: 1.5
        });
        
        showStatus(`Koordinat: ${parseFloat(lat).toFixed(6)}, ${parseFloat(lng).toFixed(6)}`, 'success');
    }
    
    window.clearCoordinates = function() {
        // Kosongkan input
        document.getElementById('latitude').value = '';
        document.getElementById('longitude').value = '';
        $('#latitude, #longitude').trigger('input');
        
        if (typeof updateMapFromInputs === 'function') updateMapFromInputs(null, null);
    };
    
    function showStatus(message, type) {
        const statusDiv = $('#coordinate-status');
        statusDiv.removeClass('alert-success alert-warning alert-danger alert-secondary')
                .addClass('alert-' + type).removeClass('d-none');
        $('#coordinate-text').text(message);
    }
    </script>
@endsection