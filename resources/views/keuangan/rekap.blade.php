@extends('layouts.admin')

@section('title', 'Rekap Keuangan - Keuangan')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Rekap Keuangan</h1>
        <div>
            <a href="{{ route('keuangan.export.excel') }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <a href="{{ route('keuangan.export.pdf') }}" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <label>Gelombang</label>
                        <select name="gelombang" class="form-control">
                            <option value="">Semua Gelombang</option>
                            @foreach($gelombang as $g)
                            <option value="{{ $g->id }}" {{ request('gelombang') == $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Jurusan</label>
                        <select name="jurusan" class="form-control">
                            <option value="">Semua Jurusan</option>
                            @foreach($jurusan as $j)
                            <option value="{{ $j->id }}" {{ request('jurusan') == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Periode</label>
                        <input type="month" name="periode" class="form-control" value="{{ request('periode') }}">
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary form-control">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Rekap Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Laporan Pemasukan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Gelombang</th>
                            <th>Jurusan</th>
                            <th>Total Pendaftar</th>
                            <th>Sudah Bayar</th>
                            <th>Total Pemasukan</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total_pemasukan = 0; @endphp
                        @foreach($rekap as $r)
                        <tr>
                            <td>{{ $r->gelombang->nama ?? '-' }}</td>
                            <td>{{ $r->jurusan->nama ?? '-' }}</td>
                            <td>{{ $r->total_pendaftar }}</td>
                            <td>{{ $r->sudah_bayar }}</td>
                            <td>Rp {{ number_format($r->total_pemasukan, 0, ',', '.') }}</td>
                            <td>
                                @php 
                                    $persentase = $r->total_pendaftar > 0 ? ($r->sudah_bayar / $r->total_pendaftar) * 100 : 0;
                                    $total_pemasukan += $r->total_pemasukan;
                                @endphp
                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width: {{ $persentase }}%">
                                        {{ number_format($persentase, 1) }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-light">
                            <th colspan="4">Total Keseluruhan</th>
                            <th>Rp {{ number_format($total_pemasukan, 0, ',', '.') }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Pemasukan per Jurusan</h6>
                </div>
                <div class="card-body">
                    <canvas id="jurusanChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Pembayaran</h6>
                </div>
                <div class="card-body">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart implementation
const jurusanData = @json($rekap->pluck('jurusan.nama'));
const pemasukanData = @json($rekap->pluck('total_pemasukan'));

// Jurusan Chart
new Chart(document.getElementById('jurusanChart'), {
    type: 'bar',
    data: {
        labels: jurusanData,
        datasets: [{
            label: 'Pemasukan',
            data: pemasukanData,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Status Pembayaran Chart
const totalSudahBayar = @json($rekap->sum('sudah_bayar'));
const totalPendaftar = @json($rekap->sum('total_pendaftar'));
const belumBayar = totalPendaftar - totalSudahBayar;

new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Sudah Bayar', 'Belum Bayar'],
        datasets: [{
            data: [totalSudahBayar, belumBayar],
            backgroundColor: [
                'rgba(40, 167, 69, 0.8)',
                'rgba(220, 53, 69, 0.8)'
            ],
            borderColor: [
                'rgba(40, 167, 69, 1)',
                'rgba(220, 53, 69, 1)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endsection