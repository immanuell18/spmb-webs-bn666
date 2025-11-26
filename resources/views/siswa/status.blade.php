@extends('layouts.siswa')

@section('title', 'Status Pendaftaran - SPMB')

@section('content')
    <!-- Page Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Status Pendaftaran</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="{{ route('siswa.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Status</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Status Content Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Status</h6>
                <h1 class="mb-5">Tracking Pendaftaran</h1>
                <div class="mb-3">
                    <div class="d-flex justify-content-center align-items-center gap-3">
                        <div class="text-center">
                            <div id="current-time" class="h5 text-primary mb-0"></div>
                            <small class="text-muted">Waktu Sekarang</small>
                        </div>
                        <div id="status-indicator" class="d-none">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <small class="text-muted">Memperbarui status...</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- Progress Timeline -->
                    <div class="timeline wow fadeInUp" data-wow-delay="0.3s">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success">
                                <i class="fa fa-check text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <h5 class="text-success">Pendaftaran Akun</h5>
                                <p class="text-muted">Akun berhasil dibuat dan diverifikasi</p>
                                <small class="text-muted">{{ Auth::user()->created_at->format('d M Y H:i') }}</small>
                            </div>
                        </div>
                        
                        @php
                            $hasDataSiswa = $pendaftar && $pendaftar->dataSiswa;
                            $hasDataOrtu = $pendaftar && $pendaftar->dataOrtu;
                            $hasDataSekolah = $pendaftar && $pendaftar->asalSekolah;
                            $isFormComplete = $hasDataSiswa && $hasDataOrtu && $hasDataSekolah;
                            
                            $berkasCount = $pendaftar ? $pendaftar->berkas->count() : 0;
                            $isBerkasComplete = $berkasCount >= 4;
                            
                            $isVerified = $pendaftar && ($pendaftar->status === 'ADM_PASS' || $pendaftar->status === 'PAID');
                        @endphp
                        
                        <div class="timeline-item">
                            <div class="timeline-marker {{ $isFormComplete ? 'bg-success' : 'bg-warning' }}">
                                <i class="fa {{ $isFormComplete ? 'fa-check' : 'fa-clock' }} text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <h5 class="{{ $isFormComplete ? 'text-success' : 'text-warning' }}">Pengisian Data Pribadi</h5>
                                <p class="text-muted">{{ $isFormComplete ? 'Data pribadi sudah lengkap' : 'Lengkapi profil dan data pribadi Anda' }}</p>
                                <small class="text-muted">{{ $isFormComplete ? 'Selesai pada ' . ($pendaftar->created_at ?? now())->format('d M Y H:i') : 'Dalam proses' }}</small>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-marker {{ $isBerkasComplete ? 'bg-success' : ($isFormComplete ? 'bg-warning' : 'bg-secondary') }}">
                                <i class="fa {{ $isBerkasComplete ? 'fa-check' : ($isFormComplete ? 'fa-clock' : 'fa-upload') }} text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <h5 class="{{ $isBerkasComplete ? 'text-success' : ($isFormComplete ? 'text-warning' : 'text-secondary') }}">Upload Berkas</h5>
                                <p class="text-muted">{{ $isBerkasComplete ? 'Semua berkas sudah diupload' : 'Upload semua dokumen yang diperlukan' }}</p>
                                <small class="text-muted">{{ $isBerkasComplete ? 'Lengkap (' . $berkasCount . '/4 berkas)' : ($isFormComplete ? 'Siap upload berkas' : 'Menunggu') }}</small>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-marker {{ $isVerified ? 'bg-success' : ($isBerkasComplete ? 'bg-warning' : 'bg-secondary') }}">
                                <i class="fa {{ $isVerified ? 'fa-check' : ($isBerkasComplete ? 'fa-clock' : 'fa-eye') }} text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <h5 class="{{ $isVerified ? 'text-success' : ($isBerkasComplete ? 'text-warning' : 'text-secondary') }}">Verifikasi Berkas</h5>
                                <p class="text-muted">{{ $isVerified ? 'Berkas sudah diverifikasi dan diterima' : 'Tim verifikasi akan memeriksa dokumen Anda' }}</p>
                                <small class="text-muted">{{ $isVerified ? 'Lulus verifikasi pada ' . ($pendaftar->tanggal_verifikasi ?? now())->format('d M Y H:i') : ($isBerkasComplete ? 'Sedang diverifikasi' : 'Menunggu') }}</small>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-marker {{ $pendaftar && $pendaftar->status == 'PAID' ? 'bg-success' : ($pendaftar && $pendaftar->status == 'ADM_PASS' ? 'bg-warning' : 'bg-secondary') }}">
                                <i class="fa {{ $pendaftar && $pendaftar->status == 'PAID' ? 'fa-check' : ($pendaftar && $pendaftar->status == 'ADM_PASS' ? 'fa-clock' : 'fa-credit-card') }} text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <h5 class="{{ $pendaftar && $pendaftar->status == 'PAID' ? 'text-success' : ($pendaftar && $pendaftar->status == 'ADM_PASS' ? 'text-warning' : 'text-secondary') }}">Pembayaran</h5>
                                <p class="text-muted">{{ $pendaftar && $pendaftar->status == 'PAID' ? 'Pembayaran sudah dikonfirmasi' : ($pendaftar && $pendaftar->status == 'ADM_PASS' ? 'Upload bukti pembayaran' : 'Menunggu verifikasi berkas') }}</p>
                                <small class="text-muted">{{ $pendaftar && $pendaftar->status == 'PAID' ? 'Selesai' : ($pendaftar && $pendaftar->status == 'ADM_PASS' ? 'Siap bayar' : 'Menunggu') }}</small>
                                @if($pendaftar && $pendaftar->status == 'ADM_PASS')
                                    <br><a href="{{ route('siswa.bayar') }}" class="btn btn-sm btn-primary mt-2">Bayar Sekarang</a>
                                @endif
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-marker {{ $pendaftar && $pendaftar->status_akhir ? ($pendaftar->status_akhir == 'LULUS' ? 'bg-success' : ($pendaftar->status_akhir == 'CADANGAN' ? 'bg-warning' : 'bg-danger')) : 'bg-secondary' }}">
                                <i class="fa {{ $pendaftar && $pendaftar->status_akhir ? ($pendaftar->status_akhir == 'LULUS' ? 'fa-trophy' : ($pendaftar->status_akhir == 'CADANGAN' ? 'fa-clock' : 'fa-times')) : 'fa-hourglass' }} text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <h5 class="{{ $pendaftar && $pendaftar->status_akhir ? ($pendaftar->status_akhir == 'LULUS' ? 'text-success' : ($pendaftar->status_akhir == 'CADANGAN' ? 'text-warning' : 'text-danger')) : 'text-secondary' }}">Pengumuman Hasil</h5>
                                @if($pendaftar && $pendaftar->status_akhir)
                                    @if($pendaftar->status_akhir == 'LULUS')
                                        <p class="text-success"><strong>🎉 SELAMAT! Anda DITERIMA!</strong></p>
                                        <p class="text-muted">Selamat bergabung dengan keluarga besar sekolah kami!</p>
                                    @elseif($pendaftar->status_akhir == 'CADANGAN')
                                        <p class="text-warning"><strong>📋 Anda masuk daftar CADANGAN</strong></p>
                                        <p class="text-muted">Mohon tunggu pengumuman selanjutnya</p>
                                    @else
                                        <p class="text-danger"><strong>😔 Maaf, Anda belum berhasil kali ini</strong></p>
                                        <p class="text-muted">Jangan menyerah, coba lagi di kesempatan berikutnya</p>
                                    @endif
                                    <small class="text-muted">Diumumkan pada {{ $pendaftar->tgl_pengumuman->format('d M Y H:i') }}</small>
                                @else
                                    <p class="text-muted">{{ $pendaftar && $pendaftar->status == 'PAID' ? 'Menunggu pengumuman hasil seleksi' : 'Selesaikan pembayaran terlebih dahulu' }}</p>
                                    <small class="text-muted">{{ $pendaftar && $pendaftar->status == 'PAID' ? 'Pengumuman akan segera diumumkan' : 'Menunggu' }}</small>
                                @endif
                            </div>
                        </div>
                        

                    </div>
                    
                    <!-- Status Cards -->
                    <div class="row g-4 mt-5">
                        <div class="col-md-4">
                            <div class="card border-{{ $isFormComplete ? 'success' : 'warning' }} status-card">
                                <div class="card-header bg-{{ $isFormComplete ? 'success' : 'warning' }} text-white">
                                    <h6 class="mb-0"><i class="fa fa-user me-2"></i>Data Pribadi</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-1"><strong>Status:</strong> 
                                        <span class="text-{{ $isFormComplete ? 'success' : 'warning' }}">
                                            {{ $isFormComplete ? 'Lengkap ✓' : 'Belum Lengkap' }}
                                        </span>
                                    </p>
                                    <p class="mb-0"><small class="text-muted">{{ $isFormComplete ? 'Semua data sudah diisi' : 'Lengkapi profil Anda' }}</small></p>
                                    @if(!$isFormComplete)
                                        <a href="{{ route('siswa.pendaftaran') }}" class="btn btn-sm btn-warning mt-2">Lengkapi Data</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card border-{{ $isBerkasComplete ? 'success' : 'warning' }} status-card">
                                <div class="card-header bg-{{ $isBerkasComplete ? 'success' : 'warning' }} text-white">
                                    <h6 class="mb-0"><i class="fa fa-file me-2"></i>Berkas</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-1"><strong>Status:</strong> 
                                        <span class="text-{{ $isBerkasComplete ? 'success' : 'warning' }}">
                                            {{ $isBerkasComplete ? 'Lengkap ✓' : 'Belum Lengkap' }}
                                        </span>
                                    </p>
                                    <p class="mb-0"><small class="text-muted berkas-count">{{ $berkasCount }}/4 berkas wajib</small></p>
                                    @if(!$isBerkasComplete && $isFormComplete)
                                        <a href="{{ route('siswa.berkas') }}" class="btn btn-sm btn-warning mt-2">Upload Berkas</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card border-{{ $isVerified ? 'success' : 'info' }} status-card">
                                <div class="card-header bg-{{ $isVerified ? 'success' : 'info' }} text-white">
                                    <h6 class="mb-0"><i class="fa fa-check-circle me-2"></i>Verifikasi</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-1"><strong>Status:</strong> 
                                        <span class="text-{{ $isVerified ? 'success' : 'info' }}">
                                            @if($isVerified)
                                                Lulus ✓
                                            @elseif($isBerkasComplete)
                                                Sedang Diverifikasi
                                            @else
                                                Menunggu
                                            @endif
                                        </span>
                                    </p>
                                    <p class="mb-0"><small class="text-muted">
                                        @if($isVerified)
                                            Berkas sudah diverifikasi
                                        @elseif($isBerkasComplete)
                                            Tim sedang memeriksa berkas
                                        @else
                                            Upload berkas terlebih dahulu
                                        @endif
                                    </small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($pendaftar)
                        <!-- Progress Bar -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Progress Pendaftaran</h6>
                                        @php
                                            $progress = 0;
                                            if($isFormComplete) $progress += 33;
                                            if($isBerkasComplete) $progress += 33;
                                            if($isVerified) $progress += 34;
                                        @endphp
                                        <div class="progress mb-2" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ $progress }}%
                                            </div>
                                        </div>
                                        <small class="text-muted progress-message">
                                            @if($progress == 100)
                                                🎉 Selamat! Semua tahap pendaftaran sudah selesai
                                            @elseif($progress >= 66)
                                                📋 Hampir selesai! Menunggu verifikasi berkas
                                            @elseif($progress >= 33)
                                                📁 Lanjutkan dengan upload berkas
                                            @else
                                                📝 Mulai dengan melengkapi data pribadi
                                            @endif
                                        </small>
                                        
                                        @if($pendaftar && $pendaftar->no_pendaftaran)
                                            <div class="mt-3 p-2 bg-light rounded">
                                                <strong>No. Pendaftaran:</strong> {{ $pendaftar->no_pendaftaran }}<br>
                                                <strong>Jurusan:</strong> {{ $pendaftar->jurusan->nama ?? '-' }}<br>
                                                <strong>Gelombang:</strong> {{ $pendaftar->gelombang->nama ?? '-' }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Belum Terdaftar -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-warning">
                                    <div class="card-body text-center">
                                        <i class="fa fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                        <h5 class="text-warning">Belum Terdaftar</h5>
                                        <p class="text-muted">Anda belum melakukan pendaftaran. Silakan daftar terlebih dahulu.</p>
                                        <a href="{{ route('siswa.pendaftaran') }}" class="btn btn-primary">Mulai Pendaftaran</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- Status Content End -->

    <style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 30px;
    }
    
    .timeline-marker {
        position: absolute;
        left: -22px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid #fff;
        box-shadow: 0 0 0 3px #dee2e6;
        transition: all 0.3s ease;
    }
    
    .timeline-content {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-left: 20px;
        transition: all 0.3s ease;
    }
    
    .status-card {
        transition: all 0.3s ease;
    }
    
    .status-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .progress-bar {
        transition: width 0.5s ease;
    }
    
    .pulse {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
        }
    }
    </style>
    
    <script>
    let lastStatus = null;
    
    document.addEventListener('DOMContentLoaded', function() {
        // Update jam real-time setiap detik
        updateClock();
        setInterval(updateClock, 1000);
        
        // Auto refresh status setiap 15 detik dengan AJAX
        setInterval(function() {
            // Hanya refresh jika user masih di halaman ini
            if (document.visibilityState === 'visible') {
                updateStatusAjax();
            }
        }, 15000); // 15 detik
        
        // Tambahkan efek pulse pada marker yang sudah selesai
        document.querySelectorAll('.timeline-marker.bg-success').forEach(function(marker) {
            marker.classList.add('pulse');
        });
        
        // Notifikasi jika ada perubahan status
        @if(session('success'))
            showNotification('success', '{{ session('success') }}');
        @endif
        
        @if(session('info'))
            showNotification('info', '{{ session('info') }}');
        @endif
        
        // Set initial status
        @if($pendaftar)
            lastStatus = {
                form_complete: {{ $isFormComplete ? 'true' : 'false' }},
                berkas_complete: {{ $isBerkasComplete ? 'true' : 'false' }},
                verified: {{ $isVerified ? 'true' : 'false' }},
                progress: {{ $progress ?? 0 }}
            };
        @endif
    });
    
    function updateClock() {
        const now = new Date();
        const options = {
            timeZone: 'Asia/Makassar',
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        };
        
        const timeString = now.toLocaleString('id-ID', options);
        const clockElement = document.getElementById('current-time');
        if (clockElement) {
            clockElement.textContent = timeString;
        }
    }
    
    function updateStatusAjax() {
        const indicator = document.getElementById('status-indicator');
        if (indicator) {
            indicator.classList.remove('d-none');
        }
        
        fetch('{{ route('siswa.status.ajax') }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                updateStatusDisplay(data.data);
                checkForChanges(data.data);
            }
        })
        .catch(error => {
            console.log('Status update error:', error);
        })
        .finally(() => {
            if (indicator) {
                setTimeout(() => {
                    indicator.classList.add('d-none');
                }, 1000);
            }
        });
    }
    
    function updateStatusDisplay(status) {
        // Update progress bar
        const progressBar = document.querySelector('.progress-bar');
        if (progressBar) {
            progressBar.style.width = status.progress + '%';
            progressBar.textContent = status.progress + '%';
        }
        
        // Update berkas count
        const berkasCount = document.querySelector('.berkas-count');
        if (berkasCount) {
            berkasCount.textContent = status.berkas_count + '/4 berkas wajib';
        }
        
        // Update progress message
        const progressMessage = document.querySelector('.progress-message');
        if (progressMessage) {
            let message = '';
            if (status.progress == 100) {
                message = '🎉 Selamat! Semua tahap pendaftaran sudah selesai';
            } else if (status.progress >= 66) {
                message = '📋 Hampir selesai! Menunggu verifikasi berkas';
            } else if (status.progress >= 33) {
                message = '📁 Lanjutkan dengan upload berkas';
            } else {
                message = '📝 Mulai dengan melengkapi data pribadi';
            }
            progressMessage.textContent = message;
        }
    }
    
    function checkForChanges(newStatus) {
        if (!lastStatus) return;
        
        // Cek perubahan form
        if (!lastStatus.form_complete && newStatus.form_complete) {
            showNotification('success', '✓ Data pribadi sudah lengkap!');
            setTimeout(() => location.reload(), 2000);
        }
        
        // Cek perubahan berkas
        if (!lastStatus.berkas_complete && newStatus.berkas_complete) {
            showNotification('success', '✓ Berkas sudah lengkap!');
            setTimeout(() => location.reload(), 2000);
        }
        
        // Cek perubahan verifikasi
        if (!lastStatus.verified && newStatus.verified) {
            showNotification('success', '🎉 Berkas sudah diverifikasi dan diterima!');
            setTimeout(() => location.reload(), 2000);
        }
        
        lastStatus = newStatus;
    }
    
    function showNotification(type, message) {
        // Buat notifikasi toast
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; animation: slideInRight 0.3s ease;';
        toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(toast);
        
        // Auto hide setelah 5 detik
        setTimeout(() => {
            if (toast.parentNode) {
                toast.classList.add('fade-out');
                setTimeout(() => toast.remove(), 300);
            }
        }, 5000);
    }
    </script>
    
    <style>
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .fade-out {
        animation: fadeOut 0.3s ease;
    }
    
    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }
    
    #current-time {
        font-family: 'Courier New', monospace;
        font-weight: bold;
        background: linear-gradient(45deg, #007bff, #0056b3);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    #current-time:hover {
        transform: scale(1.05);
    }
    </style>
@endsection