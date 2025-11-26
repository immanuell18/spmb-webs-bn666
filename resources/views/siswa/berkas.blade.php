@extends('layouts.siswa')

@section('title', 'Upload Berkas - SPMB')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
    <!-- Page Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Upload Berkas</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="{{ route('siswa.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Upload Berkas</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Upload Content Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Berkas</h6>
                <h1 class="mb-5">Upload Dokumen Pendaftaran</h1>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- Progress Steps -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fa fa-list-ol me-2"></i>Langkah Pendaftaran</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="step completed">
                                        <div class="step-icon bg-success text-white rounded-circle mx-auto mb-2" style="width: 40px; height: 40px; line-height: 40px;">
                                            <i class="fa fa-check"></i>
                                        </div>
                                        <small class="text-success"><strong>1. Formulir</strong><br>Selesai</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="step {{ $berkas->count() >= 4 ? 'completed' : 'active' }}">
                                        <div class="step-icon {{ $berkas->count() >= 4 ? 'bg-success' : 'bg-primary' }} text-white rounded-circle mx-auto mb-2" style="width: 40px; height: 40px; line-height: 40px;">
                                            <i class="fa {{ $berkas->count() >= 4 ? 'fa-check' : 'fa-upload' }}"></i>
                                        </div>
                                        <small class="{{ $berkas->count() >= 4 ? 'text-success' : 'text-primary' }}"><strong>2. Upload Berkas</strong><br>{{ $berkas->count() >= 4 ? 'Selesai' : 'Sedang Proses' }}</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="step {{ $pendaftar->status == 'ADM_PASS' ? 'completed' : '' }}">
                                        <div class="step-icon {{ $pendaftar->status == 'ADM_PASS' ? 'bg-success' : 'bg-secondary' }} text-white rounded-circle mx-auto mb-2" style="width: 40px; height: 40px; line-height: 40px;">
                                            <i class="fa {{ $pendaftar->status == 'ADM_PASS' ? 'fa-check' : 'fa-clock' }}"></i>
                                        </div>
                                        <small class="{{ $pendaftar->status == 'ADM_PASS' ? 'text-success' : 'text-muted' }}"><strong>3. Verifikasi</strong><br>{{ $pendaftar->status == 'ADM_PASS' ? 'Lulus' : 'Menunggu' }}</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="step {{ $pendaftar->status_pembayaran == 'terbayar' ? 'completed' : '' }}">
                                        <div class="step-icon {{ $pendaftar->status_pembayaran == 'terbayar' ? 'bg-success' : 'bg-secondary' }} text-white rounded-circle mx-auto mb-2" style="width: 40px; height: 40px; line-height: 40px;">
                                            <i class="fa {{ $pendaftar->status_pembayaran == 'terbayar' ? 'fa-check' : 'fa-credit-card' }}"></i>
                                        </div>
                                        <small class="{{ $pendaftar->status_pembayaran == 'terbayar' ? 'text-success' : 'text-muted' }}"><strong>4. Pembayaran</strong><br>{{ $pendaftar->status_pembayaran == 'terbayar' ? 'Lunas' : 'Belum' }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fa fa-info-circle me-2"></i>
                        <strong>Petunjuk:</strong> Upload semua berkas dalam format PDF/JPG dengan ukuran maksimal 2MB per file.
                    </div>
                    
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif
                    @if(session('info'))
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle me-2"></i>{{ session('info') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle me-2"></i>{{ session('error') }}
                        </div>
                    @endif
                    
                    @if(!$pendaftar)
                        <div class="alert alert-warning text-center">
                            <h5>Anda belum mengisi formulir pendaftaran</h5>
                            <p>Silakan lengkapi formulir pendaftaran terlebih dahulu sebelum upload berkas.</p>
                            <a href="{{ route('siswa.pendaftaran') }}" class="btn btn-primary">Isi Formulir Pendaftaran</a>
                        </div>
                    @else
                    
                    <!-- Upload Forms untuk Setiap Berkas -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fa fa-upload me-2"></i>Upload Berkas Pendaftaran</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $jenisBerkas = [
                                    'foto' => ['label' => 'Pas Foto 3x4', 'icon' => 'fa-user', 'required' => true],
                                    'ijazah' => ['label' => 'Ijazah/STTB', 'icon' => 'fa-certificate', 'required' => true],
                                    'rapor' => ['label' => 'Rapor Semester 1-5', 'icon' => 'fa-book', 'required' => true],
                                    'kk' => ['label' => 'Kartu Keluarga', 'icon' => 'fa-users', 'required' => true],
                                    'akta' => ['label' => 'Akta Kelahiran', 'icon' => 'fa-id-card', 'required' => false],
                                    'kip' => ['label' => 'KIP/KKS (Opsional)', 'icon' => 'fa-credit-card', 'required' => false]
                                ];
                                $uploadedBerkas = $berkas->keyBy(function($item) {
                                    $jenisMap = [
                                        'IJAZAH' => 'ijazah',
                                        'RAPOR' => 'rapor', 
                                        'KIP' => 'kip',
                                        'KKS' => 'kks',
                                        'AKTA' => 'akta',
                                        'KK' => 'kk',
                                        'LAINNYA' => 'foto'  // LAINNYA untuk foto
                                    ];
                                    return $jenisMap[$item->jenis] ?? strtolower($item->jenis);
                                });
                            @endphp
                            
                            <div class="row g-3">
                                @foreach($jenisBerkas as $jenis => $info)
                                    @php $uploaded = $uploadedBerkas->get($jenis); @endphp
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card h-100 {{ $uploaded ? 'border-success' : ($info['required'] ? 'border-warning' : 'border-secondary') }}">
                                            <div class="card-header {{ $uploaded ? 'bg-success' : ($info['required'] ? 'bg-warning' : 'bg-secondary') }} text-white text-center">
                                                <i class="fa {{ $info['icon'] }} fa-2x mb-2"></i>
                                                <h6 class="mb-0">{{ $info['label'] }}</h6>
                                                @if($info['required'])
                                                    <small class="badge bg-light text-dark">Wajib</small>
                                                @endif
                                            </div>
                                            <div class="card-body">
                                                @if($uploaded)
                                                    <div class="text-center mb-3">
                                                        <i class="fa fa-check-circle fa-3x text-success mb-2"></i>
                                                        <p class="text-success mb-1"><strong>Sudah Diupload</strong></p>
                                                        <small class="text-muted">{{ $uploaded->nama_file }}</small>
                                                        <div class="mt-2">
                                                            <span class="badge bg-{{ $uploaded->valid ? 'success' : 'warning' }}">
                                                                {{ $uploaded->valid ? 'Terverifikasi' : 'Menunggu' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-grid gap-2">
                                                        <button class="btn btn-outline-primary btn-sm" onclick="previewFile('{{ asset('storage/' . $uploaded->url) }}', '{{ $uploaded->nama_file }}')">
                                                            <i class="fa fa-eye me-1"></i>Preview
                                                        </button>
                                                        <button class="btn btn-outline-warning btn-sm" onclick="showUploadForm('{{ $jenis }}')">  
                                                            <i class="fa fa-edit me-1"></i>Ganti File
                                                        </button>
                                                        <button class="btn btn-outline-danger btn-sm" onclick="deleteFile({{ $uploaded->id }}, '{{ $jenis }}')">
                                                            <i class="fa fa-trash me-1"></i>Hapus
                                                        </button>
                                                    </div>
                                                @else
                                                    <div class="text-center mb-3">
                                                        <i class="fa fa-cloud-upload fa-3x text-muted mb-2"></i>
                                                        <p class="text-muted mb-1">Belum Diupload</p>
                                                    </div>
                                                    <button class="btn btn-primary w-100" onclick="showUploadForm('{{ $jenis }}')">
                                                        <i class="fa fa-upload me-1"></i>Upload File
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal Upload -->
                    <div class="modal fade" id="uploadModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Upload <span id="modalJenisBerkas"></span></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" action="{{ route('siswa.berkas.upload') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <input type="hidden" name="jenis_berkas" id="hiddenJenisBerkas">
                                        <div class="mb-3">
                                            <label class="form-label">Pilih File (PDF/JPG/PNG, Max 2MB)</label>
                                            <input type="file" class="form-control" name="file" accept=".pdf,.jpg,.jpeg,.png" onchange="previewSelectedFile(this)" required>
                                        </div>
                                        <div id="filePreview" class="mb-3" style="display: none;">
                                            <div class="alert alert-info">
                                                <strong>File yang dipilih:</strong> <span id="selectedFileName"></span><br>
                                                <strong>Ukuran:</strong> <span id="selectedFileSize"></span>
                                            </div>
                                        </div>
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle me-2"></i>
                                            <strong>Tips:</strong> Pastikan file jelas dan dapat dibaca dengan baik.
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
                    
                    <!-- Modal Preview -->
                    <div class="modal fade" id="previewModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Preview: <span id="previewFileName"></span></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <div id="previewContent"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    <a id="downloadLink" class="btn btn-primary" download>
                                        <i class="fa fa-download me-1"></i>Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Summary -->
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fa fa-chart-pie me-2"></i>Ringkasan Upload Berkas</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert {{ $pendaftar->status_berkas == 'lengkap' ? 'alert-success' : 'alert-warning' }}">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span><i class="fa fa-{{ $pendaftar->status_berkas == 'lengkap' ? 'check-circle' : 'clock' }} me-2"></i><strong>Progress Upload Berkas</strong></span>
                                    <span class="badge bg-{{ $pendaftar->status_berkas == 'lengkap' ? 'success' : 'warning' }}">{{ $berkas->count() }}/4 Berkas Wajib</span>
                                </div>
                                <div class="progress mb-2" style="height: 12px;">
                                    @php 
                                        $wajibCount = $berkas->whereIn('jenis', ['LAINNYA', 'IJAZAH', 'RAPOR', 'KK'])->count();
                                        $progress = ($wajibCount / 4) * 100; 
                                    @endphp
                                    <div class="progress-bar bg-{{ $progress >= 100 ? 'success' : 'warning' }}" style="width: {{ $progress }}%">{{ round($progress) }}%</div>
                                </div>
                                <small>
                                    @if($progress >= 100)
                                        ✅ Semua berkas wajib sudah lengkap! Menunggu verifikasi administrasi.
                                    @else
                                        📋 Upload {{ 4 - $wajibCount }} berkas wajib lagi untuk melengkapi persyaratan.
                                    @endif
                                </small>
                            </div>
                            
                            @if($pendaftar->status_berkas == 'lengkap')
                            <div class="alert alert-success">
                                <h6><i class="fa fa-thumbs-up me-2"></i>Langkah Selanjutnya:</h6>
                                <p class="mb-2">✅ Berkas Anda sudah lengkap dan sedang menunggu verifikasi</p>
                                <p class="mb-0">📞 Kami akan menghubungi Anda jika ada berkas yang perlu diperbaiki</p>
                                <hr>
                                <a href="{{ route('siswa.status') }}" class="btn btn-success btn-sm">
                                    <i class="fa fa-eye me-1"></i>Cek Status Verifikasi
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
    <!-- Upload Content End -->
    
    <script>
    function showUploadForm(jenisBerkas) {
        const jenisLabels = {
            'foto': 'Pas Foto 3x4',
            'ijazah': 'Ijazah/STTB', 
            'rapor': 'Rapor Semester 1-5',
            'kk': 'Kartu Keluarga',
            'akta': 'Akta Kelahiran',
            'kip': 'KIP/KKS'
        };
        
        document.getElementById('modalJenisBerkas').textContent = jenisLabels[jenisBerkas];
        document.getElementById('hiddenJenisBerkas').value = jenisBerkas;
        
        const modal = new bootstrap.Modal(document.getElementById('uploadModal'));
        modal.show();
    }
    
    function previewFile(fileUrl, fileName) {
        document.getElementById('previewFileName').textContent = fileName;
        document.getElementById('downloadLink').href = fileUrl;
        
        const fileExt = fileName.split('.').pop().toLowerCase();
        const previewContent = document.getElementById('previewContent');
        
        if (['jpg', 'jpeg', 'png'].includes(fileExt)) {
            previewContent.innerHTML = `<img src="${fileUrl}" class="img-fluid" style="max-height: 500px;">`;
        } else if (fileExt === 'pdf') {
            previewContent.innerHTML = `<embed src="${fileUrl}" type="application/pdf" width="100%" height="500px">`;
        } else {
            previewContent.innerHTML = `<p class="text-muted">Preview tidak tersedia untuk file ini. Silakan download untuk melihat.</p>`;
        }
        
        const modal = new bootstrap.Modal(document.getElementById('previewModal'));
        modal.show();
    }
    
    function deleteFile(berkasId, jenisBerkas) {
        if (confirm('Yakin ingin menghapus berkas ini?')) {
            fetch(`/siswa/berkas/${berkasId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Gagal menghapus berkas');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            });
        }
    }
    
    function previewSelectedFile(input) {
        const file = input.files[0];
        const preview = document.getElementById('filePreview');
        const fileName = document.getElementById('selectedFileName');
        const fileSize = document.getElementById('selectedFileSize');
        
        if (file) {
            fileName.textContent = file.name;
            fileSize.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    }
    
    // Auto refresh setelah upload berhasil
    @if(session('success'))
        setTimeout(() => {
            location.reload();
        }, 2000);
    @endif
    </script>
@endsection