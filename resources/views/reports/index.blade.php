@extends('layouts.admin')

@section('title', 'Sistem Laporan SPMB')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">📊 Sistem Laporan SPMB</h1>
        </div>
    </div>

    <!-- Export Options -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">📈 Multi-Sheet Excel Export</h5>
                </div>
                <div class="card-body">
                    <p>Export lengkap dengan 4 sheet:</p>
                    <ul class="mb-3">
                        <li>Data pendaftar lengkap</li>
                        <li>Statistik per jurusan</li>
                        <li>Sebaran geografis</li>
                        <li>Rekap pembayaran</li>
                    </ul>
                    
                    <form id="excelExportForm">
                        <div class="row">
                            <div class="col-md-6">
                                <select name="jurusan_id" class="form-select form-select-sm mb-2">
                                    <option value="">Semua Jurusan</option>
                                    @foreach($jurusan as $j)
                                        <option value="{{ $j->id }}">{{ $j->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select name="gelombang_id" class="form-select form-select-sm mb-2">
                                    <option value="">Semua Gelombang</option>
                                    @foreach($gelombang as $g)
                                        <option value="{{ $g->id }}">{{ $g->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success" onclick="exportExcel(false)">
                                📥 Download Langsung
                            </button>
                            <button type="button" class="btn btn-outline-success" onclick="exportExcel(true)">
                                ⏳ Proses Background + Email
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">📄 PDF Reports</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-danger" onclick="exportPdf('executive_summary')">
                            👔 Executive Summary (Kepsek)
                        </button>
                        <button class="btn btn-outline-danger" onclick="exportPdf('pendaftar_periode')">
                            📋 Laporan Pendaftar per Periode
                        </button>
                        <button class="btn btn-outline-danger" onclick="exportPdf('laporan_keuangan')">
                            💰 Laporan Keuangan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4>{{ \App\Models\Pendaftar::count() }}</h4>
                    <small>Total Pendaftar</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4>{{ \App\Models\Pendaftar::where('status', 'PAID')->count() }}</h4>
                    <small>Sudah Bayar</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4>{{ \App\Models\Pendaftar::where('status', 'SUBMIT')->count() }}</h4>
                    <small>Menunggu Verifikasi</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4>{{ \App\Models\Jurusan::sum('kuota') }}</h4>
                    <small>Total Kuota</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Export History -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">📋 Panduan Export</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h6>📥 Export Laporan</h6>
                            <ul>
                                <li>Export data pendaftar dalam format Excel dan PDF</li>
                                <li>Filter berdasarkan jurusan dan gelombang</li>
                                <li>Download langsung tanpa menunggu</li>
                                <li>Data selalu up-to-date</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function exportExcel(background = false) {
    const form = document.getElementById('excelExportForm');
    const formData = new FormData(form);
    
    if (background) {
        formData.append('background', '1');
    }
    
    // Semua langsung download
    const params = new URLSearchParams(formData);
    window.location.href = '{{ route("reports.export.excel") }}?' + params.toString();
}

function exportPdf(type) {
    const form = document.getElementById('excelExportForm');
    const formData = new FormData(form);
    formData.append('type', type);
    
    const params = new URLSearchParams(formData);
    window.open('{{ route("reports.export.pdf") }}?' + params.toString(), '_blank');
}
</script>
@endsection