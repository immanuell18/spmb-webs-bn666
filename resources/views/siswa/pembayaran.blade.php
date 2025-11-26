@extends('layouts.siswa')

@section('title', 'Pembayaran - SPMB')

@section('content')
    <!-- Page Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Pembayaran</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="{{ route('siswa.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Pembayaran</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Payment Content Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Pembayaran</h6>
                <h1 class="mb-5">Upload Bukti Pembayaran</h1>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle me-2"></i>{{ session('error') }}
                        </div>
                    @endif
                    
                    @if(!$pendaftar)
                        <div class="alert alert-warning text-center">
                            <h5>Anda belum terdaftar</h5>
                            <p>Silakan lengkapi pendaftaran terlebih dahulu.</p>
                            <a href="{{ route('siswa.pendaftaran') }}" class="btn btn-primary">Daftar Sekarang</a>
                        </div>
                    @elseif($pendaftar->status === 'SUBMIT')
                        <div class="alert alert-info text-center">
                            <h5>Menunggu Verifikasi Berkas</h5>
                            <p>Berkas Anda sedang dalam proses verifikasi. Pembayaran akan dibuka setelah berkas diverifikasi.</p>
                            <a href="{{ route('siswa.status') }}" class="btn btn-primary">Cek Status</a>
                        </div>
                    @elseif($pendaftar->status === 'ADM_REJECT')
                        <div class="alert alert-danger text-center">
                            <h5>Berkas Ditolak</h5>
                            <p>Maaf, berkas Anda ditolak. Silakan perbaiki dan upload ulang berkas yang diperlukan.</p>
                            <a href="{{ route('siswa.berkas') }}" class="btn btn-warning">Perbaiki Berkas</a>
                        </div>
                    @elseif($pendaftar->status === 'PAID')
                        <div class="alert alert-success text-center">
                            <h5>Pembayaran Berhasil</h5>
                            <p>Selamat! Pendaftaran Anda sudah lengkap dan pembayaran telah dikonfirmasi.</p>
                            <a href="{{ route('siswa.status') }}" class="btn btn-primary">Cek Status</a>
                        </div>
                    @else
                        <!-- Payment Info -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fa fa-info-circle me-2"></i>Informasi Pembayaran</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr><td><strong>No. Pendaftaran</strong></td><td>{{ $pendaftar->no_pendaftaran }}</td></tr>
                                            <tr><td><strong>Nama</strong></td><td>{{ $pendaftar->nama }}</td></tr>
                                            <tr><td><strong>Jurusan</strong></td><td>{{ $pendaftar->jurusan->nama }}</td></tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr><td><strong>Biaya Pendaftaran</strong></td><td class="text-success"><h5>Rp {{ number_format($pendaftar->biaya_pendaftaran, 0, ',', '.') }}</h5></td></tr>
                                            <tr><td><strong>Status</strong></td><td>
                                                @if($pendaftar->status === 'PAID')
                                                    <span class="badge bg-success">Sudah Bayar</span>
                                                @elseif($pendaftar->bukti_bayar)
                                                    <span class="badge bg-warning">Menunggu Validasi</span>
                                                @else
                                                    <span class="badge bg-danger">Belum Bayar</span>
                                                @endif
                                            </td></tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Methods -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fa fa-credit-card me-2"></i>Pilih Metode Pembayaran</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100 payment-method" data-method="bank">
                                            <div class="card-body text-center">
                                                <i class="fas fa-university fa-3x text-primary mb-3"></i>
                                                <h5>Transfer Bank</h5>
                                                <p class="text-muted">Transfer ke rekening bank sekolah</p>
                                                <button class="btn btn-outline-primary" onclick="selectPaymentMethod('bank')">Pilih</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100 payment-method" data-method="qris">
                                            <div class="card-body text-center">
                                                <i class="fas fa-qrcode fa-3x text-info mb-3"></i>
                                                <h5>QRIS</h5>
                                                <p class="text-muted">Bayar dengan scan QR Code</p>
                                                <button class="btn btn-outline-info" onclick="selectPaymentMethod('qris')">Pilih</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Transfer Options -->
                        <div id="bankOptions" class="card mb-4" style="display: none;">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fa fa-university me-2"></i>Pilih Bank</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <div class="card bank-option" data-bank="bca" onclick="selectBank('bca')">
                                            <div class="card-body text-center">
                                                <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" alt="BCA" style="height: 40px;" class="mb-2">
                                                <h6>BCA</h6>
                                                <small>1234567890</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="card bank-option" data-bank="mandiri" onclick="selectBank('mandiri')">
                                            <div class="card-body text-center">
                                                <img src="https://upload.wikimedia.org/wikipedia/commons/a/ad/Bank_Mandiri_logo_2016.svg" alt="Mandiri" style="height: 40px;" class="mb-2">
                                                <h6>Mandiri</h6>
                                                <small>9876543210</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="card bank-option" data-bank="bni" onclick="selectBank('bni')">
                                            <div class="card-body text-center">
                                                <img src="https://upload.wikimedia.org/wikipedia/id/5/55/BNI_logo.svg" alt="BNI" style="height: 40px;" class="mb-2">
                                                <h6>BNI</h6>
                                                <small>5555666677</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="card bank-option" data-bank="bri" onclick="selectBank('bri')">
                                            <div class="card-body text-center">
                                                <img src="https://upload.wikimedia.org/wikipedia/commons/2/2e/BRI_2020.svg" alt="BRI" style="height: 40px;" class="mb-2">
                                                <h6>BRI</h6>
                                                <small>1111222233</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- QRIS Payment -->
                        <div id="qrisPayment" class="card mb-4" style="display: none;">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fa fa-qrcode me-2"></i>Pembayaran QRIS</h5>
                            </div>
                            <div class="card-body text-center">
                                <div id="qrCodeContainer">
                                    <p>Klik tombol di bawah untuk generate QR Code</p>
                                </div>
                                <button class="btn btn-info" onclick="generateQRIS()">Generate QR Code</button>
                            </div>
                        </div>

                        @if($pendaftar->status === 'PAID')
                            <!-- Payment Success -->
                            <div class="card border-success">
                                <div class="card-header bg-success text-white text-center">
                                    <h5 class="mb-0"><i class="fa fa-check-circle me-2"></i>Pembayaran Berhasil</h5>
                                </div>
                                <div class="card-body text-center">
                                    <i class="fa fa-check-circle fa-5x text-success mb-3"></i>
                                    <h4 class="text-success">Pembayaran Anda Sudah Dikonfirmasi</h4>
                                    <p>Terima kasih! Pembayaran Anda sudah diterima dan dikonfirmasi. Berkas sedang dalam proses verifikasi administrasi.</p>
                                    @if($pendaftar->bukti_bayar)
                                        <a href="{{ asset('storage/' . $pendaftar->bukti_bayar) }}" target="_blank" class="btn btn-outline-primary">
                                            <i class="fa fa-eye me-1"></i>Lihat Bukti Bayar
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @elseif($pendaftar->bukti_bayar)
                            <!-- Waiting Validation -->
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-white text-center">
                                    <h5 class="mb-0"><i class="fa fa-clock me-2"></i>Menunggu Validasi</h5>
                                </div>
                                <div class="card-body text-center">
                                    <i class="fa fa-clock fa-5x text-warning mb-3"></i>
                                    <h4 class="text-warning">Bukti Pembayaran Sedang Divalidasi</h4>
                                    <p>Tim keuangan sedang memverifikasi bukti pembayaran Anda. Mohon tunggu konfirmasi.</p>
                                    @if($pendaftar->bukti_bayar)
                                        <a href="{{ asset('storage/' . $pendaftar->bukti_bayar) }}" target="_blank" class="btn btn-outline-primary">
                                            <i class="fa fa-eye me-1"></i>Lihat Bukti Bayar
                                        </a>
                                        <button class="btn btn-outline-warning" onclick="showUploadForm()">
                                            <i class="fa fa-edit me-1"></i>Ganti Bukti Bayar
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @else
                            <!-- Payment Instructions -->
                            <div id="paymentInstructions" class="card" style="display: none;">
                                <div class="card-header bg-warning text-white">
                                    <h5 class="mb-0"><i class="fa fa-info-circle me-2"></i>Instruksi Pembayaran</h5>
                                </div>
                                <div class="card-body" id="instructionContent">
                                    <!-- Dynamic content -->
                                </div>
                            </div>

                            <!-- Upload Form -->
                            <div id="uploadForm" class="card" style="display: none;">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fa fa-upload me-2"></i>Upload Bukti Pembayaran</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('siswa.pembayaran.upload') }}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" id="paymentMethod" name="payment_method" value="">
                                        <input type="hidden" id="bankCode" name="bank_code" value="">
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Bukti Pembayaran (JPG/PNG/PDF, Max 2MB)</label>
                                            <input type="file" class="form-control @error('bukti_bayar') is-invalid @enderror" 
                                                   name="bukti_bayar" accept=".jpg,.jpeg,.png,.pdf" required>
                                            @error('bukti_bayar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle me-2"></i>
                                            <strong>Petunjuk:</strong>
                                            <ul class="mb-0 mt-2">
                                                <li>Upload foto/scan bukti pembayaran yang jelas</li>
                                                <li>Pastikan nominal dan tanggal pembayaran terlihat</li>
                                                <li>Format file: JPG, PNG, atau PDF</li>
                                                <li>Ukuran maksimal 2MB</li>
                                            </ul>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fa fa-upload me-2"></i>Upload Bukti Pembayaran
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- Payment Content End -->
    
    <!-- Modal Upload (for re-upload) -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ganti Bukti Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('siswa.pembayaran.upload') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Bukti Pembayaran Baru</label>
                            <input type="file" class="form-control" name="bukti_bayar" accept=".jpg,.jpeg,.png,.pdf" required>
                        </div>
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle me-2"></i>
                            Mengganti bukti pembayaran akan mengubah status menjadi "Menunggu Validasi" kembali.
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
    
    <style>
    .payment-method:hover, .bank-option:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .payment-method.selected, .bank-option.selected {
        border: 2px solid #007bff;
        background: #f8f9ff;
    }
    </style>
    
    <script>
    let selectedPaymentMethod = '';
    let selectedBank = '';
    
    function selectPaymentMethod(method) {
        selectedPaymentMethod = method;
        document.getElementById('paymentMethod').value = method;
        
        // Reset selections
        document.querySelectorAll('.payment-method').forEach(el => el.classList.remove('selected'));
        document.querySelector(`[data-method="${method}"]`).classList.add('selected');
        
        // Hide all options
        document.getElementById('bankOptions').style.display = 'none';
        document.getElementById('qrisPayment').style.display = 'none';
        document.getElementById('paymentInstructions').style.display = 'none';
        document.getElementById('uploadForm').style.display = 'none';
        
        if (method === 'bank') {
            document.getElementById('bankOptions').style.display = 'block';
        } else if (method === 'qris') {
            document.getElementById('qrisPayment').style.display = 'block';
        }
    }
    
    function selectBank(bankCode) {
        selectedBank = bankCode;
        document.getElementById('bankCode').value = bankCode;
        
        // Reset selections
        document.querySelectorAll('.bank-option').forEach(el => el.classList.remove('selected'));
        document.querySelector(`[data-bank="${bankCode}"]`).classList.add('selected');
        
        // Show instructions
        showBankInstructions(bankCode);
    }
    
    function showBankInstructions(bankCode) {
        const banks = {
            'bca': {
                name: 'Bank Central Asia (BCA)',
                account: '1234567890',
                accountName: 'SMK Bakti Nusantara 666',
                instructions: [
                    'ATM BCA: Transfer > Rekening BCA > Masukkan nomor rekening',
                    'Mobile Banking: m-BCA > Transfer > BCA Virtual Account',
                    'Internet Banking: KlikBCA > Transfer Dana > Transfer ke BCA'
                ]
            },
            'mandiri': {
                name: 'Bank Mandiri',
                account: '9876543210',
                accountName: 'SMK Bakti Nusantara 666',
                instructions: [
                    'ATM Mandiri: Transfer > Sesama Mandiri > Masukkan nomor rekening',
                    'Livin by Mandiri: Transfer > Sesama Mandiri',
                    'Internet Banking: Mandiri Online > Transfer > Sesama Mandiri'
                ]
            },
            'bni': {
                name: 'Bank Negara Indonesia (BNI)',
                account: '5555666677',
                accountName: 'SMK Bakti Nusantara 666',
                instructions: [
                    'ATM BNI: Menu Lain > Transfer > Rekening BNI',
                    'BNI Mobile Banking: Transfer > Antar Rekening BNI',
                    'Internet Banking: BNI Internet Banking > Transfer > Antar Rekening BNI'
                ]
            },
            'bri': {
                name: 'Bank Rakyat Indonesia (BRI)',
                account: '1111222233',
                accountName: 'SMK Bakti Nusantara 666',
                instructions: [
                    'ATM BRI: Transfer > Sesama BRI > Masukkan nomor rekening',
                    'BRImo: Transfer > Sesama BRI',
                    'Internet Banking: BRI Internet Banking > Transfer > Sesama BRI'
                ]
            }
        };
        
        const bank = banks[bankCode];
        const instructionsHtml = `
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="fas fa-university me-2"></i>Informasi Rekening</h6>
                    <table class="table table-borderless">
                        <tr><td><strong>Bank:</strong></td><td>${bank.name}</td></tr>
                        <tr><td><strong>No. Rekening:</strong></td><td class="text-primary"><strong>${bank.account}</strong></td></tr>
                        <tr><td><strong>Atas Nama:</strong></td><td>${bank.accountName}</td></tr>
                        <tr><td><strong>Jumlah:</strong></td><td class="text-success"><strong>Rp {{ number_format($pendaftar->biaya_pendaftaran ?? 350000, 0, ',', '.') }}</strong></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6><i class="fas fa-list me-2"></i>Cara Transfer</h6>
                    <ol>
                        ${bank.instructions.map(instruction => `<li>${instruction}</li>`).join('')}
                    </ol>
                </div>
            </div>
            <div class="alert alert-warning mt-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Penting:</strong> Transfer dengan nominal yang tepat dan simpan bukti transfer untuk diupload.
            </div>
            <button class="btn btn-success w-100" onclick="showUploadForm()">
                <i class="fas fa-arrow-right me-2"></i>Lanjut Upload Bukti
            </button>
        `;
        
        document.getElementById('instructionContent').innerHTML = instructionsHtml;
        document.getElementById('paymentInstructions').style.display = 'block';
    }
    
    function generateQRIS() {
        const qrContainer = document.getElementById('qrCodeContainer');
        qrContainer.innerHTML = `
            <div class="text-center">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=QRIS_SMK_BAKTI_NUSANTARA_666_350000" 
                     alt="QR Code" class="img-fluid mb-3" style="max-width: 300px;">
                <h6>Scan QR Code dengan aplikasi pembayaran</h6>
                <p class="text-muted">Nominal: <strong class="text-success">Rp {{ number_format($pendaftar->biaya_pendaftaran ?? 350000, 0, ',', '.') }}</strong></p>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-mobile-alt me-2"></i>
                    Gunakan aplikasi: GoPay, OVO, DANA, ShopeePay, atau aplikasi bank
                </div>
                <button class="btn btn-success w-100" onclick="showUploadForm()">
                    <i class="fas fa-arrow-right me-2"></i>Sudah Bayar? Upload Bukti
                </button>
            </div>
        `;
    }
    
    function showUploadForm() {
        document.getElementById('uploadForm').style.display = 'block';
        document.getElementById('uploadForm').scrollIntoView({ behavior: 'smooth' });
    }
    
    function showUploadModal() {
        const modal = new bootstrap.Modal(document.getElementById('uploadModal'));
        modal.show();
    }
    </script>
@endsection