@extends('layouts.siswa')

@section('title', 'Pembayaran - SPMB')

@section('content')
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title bg-white text-center text-primary px-3">Pembayaran</h6>
            <h1 class="mb-5">Pembayaran Biaya Pendaftaran</h1>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Payment Status -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fa fa-credit-card me-2"></i>Status Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>No. Pendaftaran:</strong> {{ $pendaftar->no_pendaftaran }}</p>
                                <p><strong>Nama:</strong> {{ $pendaftar->nama }}</p>
                                <p><strong>Jurusan:</strong> {{ $pendaftar->jurusan->nama ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Biaya Pendaftaran:</strong> Rp 250.000</p>
                                <p><strong>Status:</strong> 
                                    @if($pendaftar->status === 'PAID')
                                        <span class="badge bg-success">Sudah Bayar</span>
                                    @else
                                        <span class="badge bg-warning">Belum Bayar</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($pendaftar->status !== 'PAID')
                <!-- Payment Methods -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Pilih Metode Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Online Payment -->
                            <div class="col-md-6 mb-3">
                                <div class="card border-primary h-100">
                                    <div class="card-body text-center">
                                        <i class="fa fa-mobile-alt fa-3x text-primary mb-3"></i>
                                        <h5>Pembayaran Online</h5>
                                        <p class="text-muted">Virtual Account, E-Wallet, QRIS, Credit Card</p>
                                        <button class="btn btn-primary" onclick="createOnlinePayment()">
                                            Bayar Online
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Manual Transfer -->
                            <div class="col-md-6 mb-3">
                                <div class="card border-success h-100">
                                    <div class="card-body text-center">
                                        <i class="fa fa-university fa-3x text-success mb-3"></i>
                                        <h5>Transfer Manual</h5>
                                        <p class="text-muted">Transfer ke rekening sekolah</p>
                                        <form action="{{ route('payment.manual.create') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success">
                                                Transfer Manual
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Transaction History -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Riwayat Transaksi</h5>
                    </div>
                    <div class="card-body">
                        @if($transactions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Metode</th>
                                            <th>Jumlah</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->order_id }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ strtoupper($transaction->gateway) }}</span>
                                            </td>
                                            <td>{{ $transaction->formatted_amount }}</td>
                                            <td>{!! $transaction->status_badge !!}</td>
                                            <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if($transaction->status === 'pending' && !$transaction->isExpired())
                                                    @if($transaction->gateway === 'midtrans')
                                                        <button class="btn btn-sm btn-primary" onclick="payWithMidtrans('{{ $transaction->snap_token }}')">
                                                            Bayar
                                                        </button>
                                                    @elseif($transaction->gateway === 'manual')
                                                        <a href="{{ route('payment.manual.show', $transaction->id) }}" class="btn btn-sm btn-success">
                                                            Lihat Detail
                                                        </a>
                                                    @endif
                                                    <button class="btn btn-sm btn-secondary" onclick="cancelPayment('{{ $transaction->order_id }}')">
                                                        Batal
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fa fa-receipt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada transaksi</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Memproses pembayaran...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('payment.gateways.midtrans.client_key') }}"></script>

<script>
function createOnlinePayment() {
    showLoading();
    
    fetch('{{ route("payment.create") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            payment_method: 'all'
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            payWithMidtrans(data.snap_token);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        hideLoading();
        alert('Error: ' + error.message);
    });
}

function payWithMidtrans(snapToken) {
    snap.pay(snapToken, {
        onSuccess: function(result) {
            alert('Pembayaran berhasil!');
            window.location.reload();
        },
        onPending: function(result) {
            alert('Pembayaran pending. Silakan selesaikan pembayaran.');
            window.location.reload();
        },
        onError: function(result) {
            alert('Pembayaran gagal!');
            console.log(result);
        },
        onClose: function() {
            console.log('Payment popup closed');
        }
    });
}

function cancelPayment(orderId) {
    if (confirm('Yakin ingin membatalkan pembayaran ini?')) {
        fetch(`/payment/cancel/${orderId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Pembayaran berhasil dibatalkan');
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

function showLoading() {
    new bootstrap.Modal(document.getElementById('loadingModal')).show();
}

function hideLoading() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('loadingModal'));
    if (modal) modal.hide();
}
</script>
@endsection