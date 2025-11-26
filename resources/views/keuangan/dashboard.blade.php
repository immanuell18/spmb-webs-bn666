@extends('layouts.admin')

@section('title', 'Dashboard Keuangan - SPMB')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Keuangan</h1>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> Export Laporan
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pemasukan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($stats['total_pemasukan'], 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Sudah Bayar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['sudah_bayar'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Menunggu Validasi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pembayaranPending->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Belum Bayar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['belum_bayar'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs" id="keuanganTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="verifikasi-tab" data-toggle="tab" href="#verifikasi" role="tab">Verifikasi Pembayaran</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="rekap-tab" data-toggle="tab" href="#rekap" role="tab">Rekap Keuangan</a>
        </li>
    </ul>

    <div class="tab-content" id="keuanganTabContent">
        <!-- Verifikasi Pembayaran Tab -->
        <div class="tab-pane fade show active" id="verifikasi" role="tabpanel">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Verifikasi Pembayaran</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Jurusan</th>
                                    <th>Nominal</th>
                                    <th>Tanggal Upload</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pembayaranPending as $index => $p)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $p->nama }}</td>
                                    <td>{{ $p->jurusan->nama ?? '-' }}</td>
                                    <td>Rp {{ number_format($p->gelombang->biaya_daftar ?? \App\Models\SystemSetting::getBiayaPendaftaran(), 0, ',', '.') }}</td>
                                    <td>{{ $p->created_at->format('d M Y') }}</td>
                                    <td><span class="badge badge-warning text-dark">Menunggu</span></td>
                                    <td>
                                        <a href="{{ route('keuangan.verifikasi') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> Validasi
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada pembayaran yang perlu diverifikasi</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rekap Keuangan Tab -->
        <div class="tab-pane fade" id="rekap" role="tabpanel">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Rekap Keuangan</h6>
                    <div>
                        <button class="btn btn-success btn-sm me-2">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                        <button class="btn btn-danger btn-sm">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-control">
                                <option>Semua Gelombang</option>
                                <option>Gelombang 1</option>
                                <option>Gelombang 2</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control">
                                <option>Semua Jurusan</option>
                                <option>RPL</option>
                                <option>TKJ</option>
                                <option>MM</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Periode</th>
                                    <th>Gelombang</th>
                                    <th>Jurusan</th>
                                    <th>Jumlah Pendaftar</th>
                                    <th>Total Pemasukan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rekapGelombang as $gelombang)
                                <tr>
                                    <td>{{ $gelombang->created_at->format('F Y') }}</td>
                                    <td>{{ $gelombang->nama }}</td>
                                    <td>-</td>
                                    <td>{{ $gelombang->sudah_bayar }}</td>
                                    <td>Rp {{ number_format($gelombang->total_pemasukan, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada data pemasukan</td>
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

<!-- Validasi Modal -->
<div class="modal fade" id="validasiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Validasi Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Data Pembayaran</h6>
                        <p><strong>Nama:</strong> Ahmad Rizki</p>
                        <p><strong>Nominal:</strong> Rp 250.000</p>
                        <p><strong>Tanggal:</strong> 2024-11-14</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Bukti Pembayaran</h6>
                        <img src="#" class="img-fluid" alt="Bukti Bayar" style="max-height: 200px;">
                    </div>
                </div>
                <hr>
                <div class="form-group">
                    <label>Catatan</label>
                    <textarea class="form-control" id="catatanBayar" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="validasiBayar('terima')">
                    <i class="fas fa-check"></i> Terima
                </button>
                <button type="button" class="btn btn-danger" onclick="validasiBayar('tolak')">
                    <i class="fas fa-times"></i> Tolak
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function validasiModal(id) {
    $('#validasiModal').modal('show');
}

function validasiBayar(status) {
    alert('Pembayaran ' + status);
    $('#validasiModal').modal('hide');
    location.reload();
}
</script>
@endsection