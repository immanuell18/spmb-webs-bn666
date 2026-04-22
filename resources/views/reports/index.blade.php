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
                            <i class="bi bi-clipboard-data text-warning me-1"></i> Laporan Pendaftar per Periode
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
    <div class="row g-3 mb-4">
        @php
        $reportCards = [
            ['gradient'=>'linear-gradient(135deg,#6366f1,#4f46e5)','shadow'=>'rgba(99,102,241,0.3)','icon'=>'ti-users','label'=>'Total','value'=>\App\Models\Pendaftar::count(),'desc'=>'Total Pendaftar'],
            ['gradient'=>'linear-gradient(135deg,#10b981,#059669)','shadow'=>'rgba(16,185,129,0.3)','icon'=>'ti-credit-card','label'=>'Bayar','value'=>\App\Models\Pendaftar::where('status','PAID')->count(),'desc'=>'Sudah Bayar'],
            ['gradient'=>'linear-gradient(135deg,#f59e0b,#d97706)','shadow'=>'rgba(245,158,11,0.3)','icon'=>'ti-clock','label'=>'Pending','value'=>\App\Models\Pendaftar::where('status','SUBMIT')->count(),'desc'=>'Menunggu Verifikasi'],
            ['gradient'=>'linear-gradient(135deg,#0ea5e9,#0284c7)','shadow'=>'rgba(14,165,233,0.3)','icon'=>'ti-school','label'=>'Kuota','value'=>\App\Models\Jurusan::sum('kuota'),'desc'=>'Total Kuota'],
        ];
        @endphp
        @foreach($reportCards as $rc)
        <div class="col-md-3">
            <div style="background:{{ $rc['gradient'] }};box-shadow:0 8px 24px {{ $rc['shadow'] }};border-radius:16px;padding:24px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                    <div style="width:48px;height:48px;background:rgba(255,255,255,0.25);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                        <i class="ti {{ $rc['icon'] }}" style="font-size:22px;color:#fff !important;"></i>
                    </div>
                    <span style="background:rgba(255,255,255,0.2);color:#fff !important;font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;">{{ $rc['label'] }}</span>
                </div>
                <div style="color:#fff !important;font-size:32px;font-weight:800;line-height:1;margin-bottom:6px;">{{ $rc['value'] }}</div>
                <div style="color:rgba(255,255,255,0.85) !important;font-size:13px;font-weight:500;">{{ $rc['desc'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Export History -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clipboard-data text-warning me-1"></i> Panduan Export</h5>
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