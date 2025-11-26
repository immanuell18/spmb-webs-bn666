@extends('layouts.admin')

@section('title', 'Payment Transactions - SPMB Admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">💳 Payment Transactions</h5>
                    <a href="{{ route('admin.payment.dashboard') }}" class="btn btn-primary">
                        <i class="ti ti-dashboard"></i> Dashboard
                    </a>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="row mb-4">
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="gateway" class="form-select">
                                <option value="">Semua Gateway</option>
                                <option value="midtrans" {{ request('gateway') === 'midtrans' ? 'selected' : '' }}>Midtrans</option>
                                <option value="manual" {{ request('gateway') === 'manual' ? 'selected' : '' }}>Manual</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="Dari">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="Sampai">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.payment.index') }}" class="btn btn-secondary w-100">Reset</a>
                        </div>
                    </form>

                    <!-- Transactions Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Pendaftar</th>
                                    <th>Gateway</th>
                                    <th>Method</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                <tr>
                                    <td>
                                        <code>{{ $transaction->order_id }}</code>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $transaction->pendaftar->nama }}</strong><br>
                                            <small class="text-muted">{{ $transaction->pendaftar->no_pendaftaran }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ strtoupper($transaction->gateway) }}</span>
                                    </td>
                                    <td>{{ $transaction->payment_method }}</td>
                                    <td>{{ $transaction->formatted_amount }}</td>
                                    <td>{!! $transaction->status_badge !!}</td>
                                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.payment.show', $transaction->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            @if($transaction->canBeRefunded())
                                            <button class="btn btn-sm btn-outline-warning" onclick="showRefundModal({{ $transaction->id }}, '{{ $transaction->order_id }}', {{ $transaction->amount }})">
                                                <i class="ti ti-arrow-back"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada transaksi</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    {{ $transactions->links() }}
                </div>
            </div>
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