@extends('layouts.siswa')

@section('title', 'Transfer Manual - SPMB')

@section('content')
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title bg-white text-center text-primary px-3">Transfer Manual</h6>
            <h1 class="mb-5">Instruksi Transfer Manual</h1>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Transaction Info -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fa fa-info-circle me-2"></i>Informasi Transaksi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Order ID:</strong> {{ $transaction->order_id }}</p>
                                <p><strong>Nama:</strong> {{ $transaction->pendaftar->nama }}</p>
                                <p><strong>No. Pendaftaran:</strong> {{ $transaction->pendaftar->no_pendaftaran }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Jumlah:</strong> {{ $transaction->formatted_amount }}</p>
                                <p><strong>Status:</strong> {!! $transaction->status_badge !!}</p>
                                <p><strong>Batas Waktu:</strong> {{ $transaction->expires_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank Accounts -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fa fa-university me-2"></i>Rekening Tujuan</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fa fa-exclamation-triangle me-2"></i>
                            <strong>Penting:</strong> Transfer tepat sesuai jumlah {{ $transaction->formatted_amount }} dan gunakan Order ID <strong>{{ $transaction->order_id }}</strong> sebagai berita transfer.
                        </div>
                        
                        @foreach($bankAccounts as $bank)
                        <div class="card border-success mb-3">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center">
                                        <h4 class="text-success mb-0">{{ $bank['bank'] }}</h4>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>No. Rekening:</strong></p>
                                        <h5 class="text-primary mb-1">{{ $bank['account_number'] }}</h5>
                                        <p class="mb-0"><strong>Atas Nama:</strong> {{ $bank['account_name'] }}</p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <button class="btn btn-outline-success btn-sm" onclick="copyToClipboard('{{ $bank['account_number'] }}')">
                                            <i class="fa fa-copy me-1"></i>Copy Rekening
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Instructions -->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fa fa-list-ol me-2"></i>Langkah-langkah Transfer</h5>
                    </div>
                    <div class="card-body">
                        <ol class="list-group list-group-numbered">
                            <li class="list-group-item">Transfer tepat sejumlah <strong>{{ $transaction->formatted_amount }}</strong> ke salah satu rekening di atas</li>
                            <li class="list-group-item">Gunakan Order ID <strong>{{ $transaction->order_id }}</strong> sebagai berita transfer</li>
                            <li class="list-group-item">Simpan bukti transfer (screenshot/foto struk)</li>
                            <li class="list-group-item">Upload bukti transfer melalui menu "Upload Berkas" di dashboard</li>
                            <li class="list-group-item">Tunggu verifikasi dari admin (maksimal 1x24 jam)</li>
                        </ol>
                    </div>
                </div>

                <!-- Important Notes -->
                <div class="card mb-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fa fa-exclamation-triangle me-2"></i>Perhatian Penting</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fa fa-check text-success me-2"></i>Transfer harus dilakukan sebelum <strong>{{ $transaction->expires_at->format('d/m/Y H:i') }}</strong></li>
                            <li class="mb-2"><i class="fa fa-check text-success me-2"></i>Jumlah transfer harus tepat <strong>{{ $transaction->formatted_amount }}</strong></li>
                            <li class="mb-2"><i class="fa fa-check text-success me-2"></i>Wajib mencantumkan Order ID dalam berita transfer</li>
                            <li class="mb-2"><i class="fa fa-times text-danger me-2"></i>Transfer dari rekening atas nama orang lain tidak akan diproses</li>
                            <li class="mb-2"><i class="fa fa-times text-danger me-2"></i>Biaya admin bank ditanggung oleh pendaftar</li>
                        </ul>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center">
                    <a href="{{ route('siswa.berkas') }}" class="btn btn-success btn-lg me-3">
                        <i class="fa fa-upload me-2"></i>Upload Bukti Transfer
                    </a>
                    <a href="{{ route('payment.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fa fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Copy Success Toast -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="copyToast" class="toast" role="alert">
        <div class="toast-header">
            <i class="fa fa-check-circle text-success me-2"></i>
            <strong class="me-auto">Berhasil</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            Nomor rekening berhasil disalin!
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        const toast = new bootstrap.Toast(document.getElementById('copyToast'));
        toast.show();
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        alert('Gagal menyalin nomor rekening');
    });
}

// Auto refresh status setiap 30 detik
setInterval(function() {
    if (document.visibilityState === 'visible') {
        fetch(`/payment/status/{{ $transaction->order_id }}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.transaction_status === 'settlement') {
                    location.reload();
                }
            })
            .catch(error => console.log('Status check error:', error));
    }
}, 30000);
</script>
@endsection