@extends('layouts.admin')

@section('title', 'Payment Dashboard - SPMB Admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">💳 Payment Dashboard</h5>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['total_transactions'] }}</h3>
                                    <p class="mb-0">Total Transaksi</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['paid_transactions'] }}</h3>
                                    <p class="mb-0">Transaksi Lunas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['pending_transactions'] }}</h3>
                                    <p class="mb-0">Transaksi Pending</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3>Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
                                    <p class="mb-0">Total Pendapatan</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.payment.index') }}" class="btn btn-primary">
                                    <i class="ti ti-list"></i> Semua Transaksi
                                </a>
                                <a href="{{ route('admin.payment.index', ['status' => 'pending']) }}" class="btn btn-warning">
                                    <i class="ti ti-clock"></i> Pending
                                </a>
                                <a href="{{ route('admin.payment.index', ['status' => 'paid']) }}" class="btn btn-success">
                                    <i class="ti ti-check"></i> Lunas
                                </a>
                                <a href="{{ route('admin.payment.index', ['gateway' => 'manual']) }}" class="btn btn-info">
                                    <i class="ti ti-upload"></i> Transfer Manual
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Transactions -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Transaksi Terbaru</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Pendaftar</th>
                                            <th>Gateway</th>
                                            <th>Jumlah</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentTransactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->order_id }}</td>
                                            <td>
                                                <div>
                                                    <strong>{{ $transaction->pendaftar->nama }}</strong><br>
                                                    <small class="text-muted">{{ $transaction->pendaftar->no_pendaftaran }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ strtoupper($transaction->gateway) }}</span>
                                            </td>
                                            <td>{{ $transaction->formatted_amount }}</td>
                                            <td>{!! $transaction->status_badge !!}</td>
                                            <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('admin.monitoring-berkas') }}?search={{ $transaction->pendaftar->no_pendaftaran }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="ti ti-eye"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Belum ada transaksi</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection