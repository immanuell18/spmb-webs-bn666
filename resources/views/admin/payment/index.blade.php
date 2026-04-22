@extends('layouts.admin')

@section('title', 'Payment Transactions - SPMB Admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold text-gray-800"><i class="ti ti-credit-card text-primary me-2"></i> Payment Transactions</h4>
        <a href="{{ route('admin.payment.dashboard') }}" class="btn btn-primary shadow-sm">
            <i class="ti ti-dashboard"></i> Dashboard
        </a>
    </div>

    <!-- Filter Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold">Status</label>
                    <select name="status" class="form-select bg-light border-0">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold">Gateway</label>
                    <select name="gateway" class="form-select bg-light border-0">
                        <option value="">Semua Gateway</option>
                        <option value="midtrans" {{ request('gateway') === 'midtrans' ? 'selected' : '' }}>Midtrans</option>
                        <option value="manual" {{ request('gateway') === 'manual' ? 'selected' : '' }}>Manual</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold">Dari Tanggal</label>
                    <input type="date" name="date_from" class="form-control bg-light border-0" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold">Sampai Tanggal</label>
                    <input type="date" name="date_to" class="form-control bg-light border-0" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4"><i class="ti ti-filter pe-1"></i> Filter Data</button>
                        <a href="{{ route('admin.payment.index') }}" class="btn btn-light border px-4"><i class="ti ti-refresh pe-1"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Transactions Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Order ID</th>
                            <th>Pendaftar</th>
                            <th>Gateway</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr>
                            <td class="ps-4">
                                <code>{{ rtrim($transaction->order_id ?? '-') }}</code>
                            </td>
                            <td>
                                <div>
                                    <strong class="text-dark">{{ $transaction->pendaftar->nama ?? 'Unknown' }}</strong><br>
                                    <small class="text-muted">{{ $transaction->pendaftar->no_pendaftaran ?? '-' }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info fw-semibold">{{ strtoupper($transaction->gateway ?? '-') }}</span>
                            </td>
                            <td><span class="text-muted">{{ $transaction->payment_method ?? '-' }}</span></td>
                            <td><strong>{{ rtrim($transaction->formatted_amount ?? '-') }}</strong></td>
                            <td>{!! $transaction->status_badge ?? '<span class="badge bg-secondary">Unknown</span>' !!}</td>
                            <td><small class="text-muted">{{ rtrim($transaction->created_at ? $transaction->created_at->format('d/m/Y H:i') : '-') }}</small></td>
                            <td class="pe-4">
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.payment.show', rtrim($transaction->id)) }}" class="btn btn-sm btn-primary" title="Detail">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    @if(isset($transaction->is_refundable) && $transaction->is_refundable)
                                    <button class="btn btn-sm btn-warning" onclick="showRefundModal({{ $transaction->id ?? 0 }}, '{{ $transaction->order_id ?? '' }}', {{ $transaction->amount ?? 0 }})" title="Refund">
                                        <i class="ti ti-arrow-back"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="ti ti-folder-off fs-1 d-block mb-2"></i>
                                Tidak ada data transaksi yang ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($transactions->hasPages())
            <div class="border-top p-3 d-flex justify-content-center">
                {{ $transactions->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Refund Modal -->
<div class="modal fade" id="refundModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="refundForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Refund Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Order ID</label>
                        <input type="text" class="form-control" id="refundOrderId" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Refund Amount</label>
                        <input type="number" class="form-control" name="amount" id="refundAmount" step="0.01">
                        <small class="text-muted">Kosongkan untuk refund penuh</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea class="form-control" name="reason" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Process Refund</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function showRefundModal(transactionId, orderId, amount) {
    document.getElementById('refundForm').action = `/admin/payment/${transactionId}/refund`;
    document.getElementById('refundOrderId').value = orderId;
    document.getElementById('refundAmount').max = amount;
    
    new bootstrap.Modal(document.getElementById('refundModal')).show();
}
</script>
@endsection