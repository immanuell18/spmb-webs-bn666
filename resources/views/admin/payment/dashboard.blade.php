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
                    <div class="row g-3 mb-4">
                        @php
                        $cards = [
                            ['color'=>'primary','icon'=>'ti-list','label'=>'Total Transaksi','value'=>$stats['total_transactions'],'desc'=>'Total Pendaftar'],
                            ['color'=>'success','icon'=>'ti-check','label'=>'Transaksi Lunas','value'=>$stats['paid_transactions'],'desc'=>'Sudah Lunas (PAID)'],
                            ['color'=>'warning','icon'=>'ti-clock','label'=>'Transaksi Pending','value'=>$stats['pending_transactions'],'desc'=>'Menunggu Pembayaran'],
                            ['color'=>'info','icon'=>'ti-currency-dollar','label'=>'Total Pendapatan','value'=>'Rp '.number_format($stats['total_revenue'],0,',','.'),'desc'=>'Revenue Gelombang'],
                        ];
                        @endphp
                        @foreach($cards as $card)
                        <div class="col-md-3">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="text-xs text-uppercase fw-bold text-{{ $card['color'] }}">{{ $card['label'] }}</div>
                                        <div style="width:36px;height:36px;background:var(--{{ $card['color'] }}-bg, #f3f4f6);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                                            <i class="ti {{ $card['icon'] }} text-{{ $card['color'] }}" style="font-size:18px;"></i>
                                        </div>
                                    </div>
                                    <div class="h5" style="color:var(--text);font-size:28px;font-weight:800;margin:0;">{{ $card['value'] }}</div>
                                    <div class="text-muted mt-2" style="font-size:12px;">{{ $card['desc'] }}</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Quick Actions -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted fw-bold mb-3 text-uppercase" style="font-size: 12px;">Quick Filter</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.payment.index') }}" class="btn btn-primary shadow-sm bg-primary border-0">
                                    <i class="ti ti-list"></i> Semua Transaksi
                                </a>
                                <a href="{{ route('admin.payment.index', ['status' => 'pending']) }}" class="btn btn-warning shadow-sm border-0 text-white">
                                    <i class="ti ti-clock"></i> Pending
                                </a>
                                <a href="{{ route('admin.payment.index', ['status' => 'paid']) }}" class="btn btn-success shadow-sm border-0">
                                    <i class="ti ti-check"></i> Lunas
                                </a>
                                <a href="{{ route('admin.payment.index', ['gateway' => 'manual']) }}" class="btn btn-info shadow-sm border-0 text-white">
                                    <i class="ti ti-upload"></i> Transfer Manual
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Transactions -->
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="card-title fw-bold mb-0">Transaksi Terbaru</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">Order ID</th>
                                            <th>Pendaftar</th>
                                            <th>Gateway</th>
                                            <th>Jumlah</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                            <th class="pe-4">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentTransactions as $transaction)
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
                                            <td><strong>{{ rtrim($transaction->formatted_amount ?? '-') }}</strong></td>
                                            <td>{!! $transaction->status_badge ?? '<span class="badge bg-secondary">Unknown</span>' !!}</td>
                                            <td><small class="text-muted">{{ rtrim($transaction->created_at ? $transaction->created_at->format('d/m/Y H:i') : '-') }}</small></td>
                                            <td class="pe-4">
                                                <a href="{{ route('admin.monitoring-berkas') }}?search={{ rtrim($transaction->pendaftar->no_pendaftaran) }}" class="btn btn-sm btn-primary" title="Detail Pendaftar">
                                                    <i class="ti ti-eye"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                <i class="ti ti-folder-off fs-1 d-block mb-2"></i>
                                                Belum ada transaksi.
                                            </td>
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