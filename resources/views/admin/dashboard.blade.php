@extends('layouts.admin')

@section('title', 'Dashboard - SPMB Admin')

@section('content')
<div class="container-fluid">
    <!--  Row 1 -->
    <div class="row">
        <div class="col-lg-8 d-flex align-items-strech">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
                        <div class="mb-3 mb-sm-0">
                            <h5 class="card-title fw-semibold">Ringkasan Pendaftaran Harian</h5>
                        </div>
                        <div>
                            <select class="form-select" id="gelombangFilter">
                                @foreach($statistikGelombang as $gel)
                                <option value="{{ $gel->id }}" {{ $gelombangAktif && $gelombangAktif->id == $gel->id ? 'selected' : '' }}>
                                    {{ $gel->nama }} ({{ $gel->status }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div id="chart"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Yearly Breakup -->
                    <div class="card overflow-hidden">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-9 fw-semibold">Total Pendaftar</h5>
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h4 class="fw-semibold mb-3 total-pendaftar">{{ $totalPendaftar }}</h4>
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="me-1 rounded-circle bg-light-success round-20 d-flex align-items-center justify-content-center">
                                            <i class="ti ti-arrow-up-left text-success"></i>
                                        </span>
                                        <p class="text-dark me-1 fs-3 mb-0">+15%</p>
                                        <p class="fs-3 mb-0">dari bulan lalu</p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="me-4">
                                            <span class="round-8 bg-primary rounded-circle me-2 d-inline-block"></span>
                                            <span class="fs-2">Terverifikasi</span>
                                        </div>
                                        <div>
                                            <span class="round-8 bg-light-primary rounded-circle me-2 d-inline-block"></span>
                                            <span class="fs-2">Pending</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="d-flex justify-content-center">
                                        <div id="breakup"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <!-- Monthly Earnings -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row alig n-items-start">
                                <div class="col-8">
                                    <h5 class="card-title mb-9 fw-semibold">Pembayaran</h5>
                                    <h4 class="fw-semibold mb-3 total-pembayaran">Rp {{ number_format($sudahBayar * \App\Models\SystemSetting::getBiayaPendaftaran(), 0, ',', '.') }}</h4>
                                    <div class="d-flex align-items-center pb-1">
                                        <span class="me-2 rounded-circle bg-light-danger round-20 d-flex align-items-center justify-content-center">
                                            <i class="ti ti-arrow-down-right text-danger"></i>
                                        </span>
                                        <p class="text-dark me-1 fs-3 mb-0">+9%</p>
                                        <p class="fs-3 mb-0">dari bulan lalu</p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="d-flex justify-content-end">
                                        <div class="text-white bg-secondary rounded-circle p-6 d-flex align-items-center justify-content-center">
                                            <i class="ti ti-currency-dollar fs-6"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="earning"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Row 2 -->
    <div class="row">
        <div class="col-lg-4 d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body p-4">
                    <div class="mb-4">
                        <h5 class="card-title fw-semibold">Pendaftar per Jurusan</h5>
                    </div>
                    <ul class="list-unstyled">
                        <li class="d-flex align-items-center pb-1 mb-2">
                            <div class="me-3 rounded-circle bg-light-primary round-20 d-flex align-items-center justify-content-center">
                                <i class="ti ti-point text-primary"></i>
                            </div>
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <div>
                                    <h6 class="mb-1 fw-semibold">Terverifikasi</h6>
                                    <p class="fs-2 mb-0 text-muted">{{ $sudahVerifikasi }} pendaftar</p>
                                </div>
                                <h6 class="mb-0 fw-semibold">36.5%</h6>
                            </div>
                        </li>
                        <li class="d-flex align-items-center pb-1 mb-2">
                            <div class="me-3 rounded-circle bg-light-warning round-20 d-flex align-items-center justify-content-center">
                                <i class="ti ti-point text-warning"></i>
                            </div>
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <div>
                                    <h6 class="mb-1 fw-semibold">Sudah Bayar</h6>
                                    <p class="fs-2 mb-0 text-muted">{{ $sudahBayar }} pendaftar</p>
                                </div>
                                <h6 class="mb-0 fw-semibold">41.9%</h6>
                            </div>
                        </li>
                        <li class="d-flex align-items-center pb-1 mb-2">
                            <div class="me-3 rounded-circle bg-light-secondary round-20 d-flex align-items-center justify-content-center">
                                <i class="ti ti-point text-secondary"></i>
                            </div>
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <div>
                                    <h6 class="mb-1 fw-semibold">Menunggu</h6>
                                    <p class="fs-2 mb-0 text-muted">{{ $menungguVerifikasi }} pendaftar</p>
                                </div>
                                <h6 class="mb-0 fw-semibold">21.5%</h6>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-8 d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body p-4">
                    <h5 class="card-title fw-semibold mb-4">Pendaftar Terbaru</h5>
                    <div class="table-responsive">
                        <table class="table text-nowrap mb-0 align-middle">
                            <thead class="text-dark fs-4">
                                <tr>
                                    <th class="border-bottom-0">
                                        <h6 class="fw-semibold mb-0">No. Pendaftaran</h6>
                                    </th>
                                    <th class="border-bottom-0">
                                        <h6 class="fw-semibold mb-0">Nama</h6>
                                    </th>
                                    <th class="border-bottom-0">
                                        <h6 class="fw-semibold mb-0">Jurusan</h6>
                                    </th>
                                    <th class="border-bottom-0">
                                        <h6 class="fw-semibold mb-0">Status</h6>
                                    </th>
                                    <th class="border-bottom-0">
                                        <h6 class="fw-semibold mb-0">Tanggal</h6>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendaftarTerbaru as $p)
                                <tr>
                                    <td class="border-bottom-0">
                                        <h6 class="fw-semibold mb-0">{{ $p->no_pendaftaran }}</h6>
                                    </td>
                                    <td class="border-bottom-0">
                                        <h6 class="fw-semibold mb-1">{{ $p->nama }}</h6>
                                        <span class="fw-normal">{{ $p->email }}</span>
                                    </td>
                                    <td class="border-bottom-0">
                                        <p class="mb-0 fw-normal">{{ $p->jurusan->nama ?? 'N/A' }}</p>
                                    </td>
                                    <td class="border-bottom-0">
                                        <div class="d-flex align-items-center gap-2">
                                            @php
                                                $statusColors = [
                                                    'PAID' => ['bg' => '#28a745', 'text' => '#fff', 'label' => 'Terbayar'],
                                                    'ADM_PASS' => ['bg' => '#007bff', 'text' => '#fff', 'label' => 'Terverifikasi'],
                                                    'SUBMIT' => ['bg' => '#ffc107', 'text' => '#000', 'label' => 'Pending'],
                                                    'default' => ['bg' => '#dc3545', 'text' => '#fff', 'label' => 'Ditolak']
                                                ];
                                                $statusStyle = $statusColors[$p->status] ?? $statusColors['default'];
                                            @endphp
                                            <span class="badge rounded-3 fw-semibold" style="background-color: {{ $statusStyle['bg'] }}; color: {{ $statusStyle['text'] }};">{{ $statusStyle['label'] }}</span>
                                        </div>
                                    </td>
                                    <td class="border-bottom-0">
                                        <h6 class="fw-semibold mb-0 fs-4">{{ $p->created_at->format('d M Y') }}</h6>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada pendaftar</td>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data dari controller
    const trenHarian = @json($trenHarian);
    const chartData = trenHarian.map(item => item.jumlah);
    const chartLabels = trenHarian.map(item => {
        const date = new Date(item.tanggal);
        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
    });
    
    if (typeof ApexCharts === 'undefined') {
        console.error('ApexCharts not loaded');
        return;
    }
    
    const chartElement = document.querySelector('#chart');
    if (!chartElement) {
        console.error('Chart element not found');
        return;
    }
    
    try {
        const options = {
            series: [{
                name: 'Pendaftar',
                data: chartData
            }],
            chart: {
                type: 'area',
                height: 350
            },
            colors: ['#5D87FF'],
            xaxis: {
                categories: chartLabels
            },
            yaxis: {
                min: 0
            }
        };
        
        const chart = new ApexCharts(chartElement, options);
        chart.render();
        
        // Handle gelombang filter change with AJAX
        document.getElementById('gelombangFilter').addEventListener('change', function() {
            const gelombangId = this.value;
            
            // Show loading state
            chartElement.innerHTML = '<div class="text-center p-4"><div class="spinner-border" role="status"></div></div>';
            
            // Fetch new data
            fetch(`{{ route('admin.dashboard') }}?ajax=1&gelombang=${gelombangId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    // Update chart
                    let newChartData, newChartLabels;
                    
                    if (data.trenHarian && data.trenHarian.length > 0) {
                        newChartData = data.trenHarian.map(item => item.jumlah);
                        newChartLabels = data.trenHarian.map(item => {
                            const date = new Date(item.tanggal);
                            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
                        });
                    } else {
                        // No data, show empty chart
                        newChartData = [0, 0, 0, 0, 0, 0, 0];
                        newChartLabels = ['Tidak ada data'];
                    }
                    
                    chart.updateOptions({
                        series: [{
                            name: 'Pendaftar',
                            data: newChartData
                        }],
                        xaxis: {
                            categories: newChartLabels
                        }
                    });
                    
                    // Update KPI numbers
                    document.querySelector('.total-pendaftar').textContent = data.totalPendaftar || 0;
                    document.querySelector('.total-pembayaran').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.totalPembayaran || 0);
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    chartElement.innerHTML = '<div class="text-center p-4 text-danger">Error loading data</div>';
                });
        });
        
    } catch (error) {
        console.error('Chart error:', error);
    }
});
</script>

<style>
.pulse-update {
    animation: pulseUpdate 1s ease-in-out;
}

@keyframes pulseUpdate {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); color: #28a745; }
    100% { transform: scale(1); }
}
</style>
@endsection