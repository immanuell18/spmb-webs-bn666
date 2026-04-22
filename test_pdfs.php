<?php
try {
    echo "1. Siswa - Kartu Pendaftaran: ";
    $pendaftar = App\Models\Pendaftar::first();
    Barryvdh\DomPDF\Facade\Pdf::loadView('siswa.pdf.kartu-pendaftaran', compact('pendaftar'))->output();
    echo "OK\n";
} catch (\Exception $e) { echo "ERROR: " . $e->getMessage() . "\n"; }

try {
    echo "2. Siswa - Bukti Pembayaran: ";
    Barryvdh\DomPDF\Facade\Pdf::loadView('siswa.pdf.bukti-pembayaran', compact('pendaftar'))->output();
    echo "OK\n";
} catch (\Exception $e) { echo "ERROR: " . $e->getMessage() . "\n"; }

try {
    echo "3. Siswa - Surat Pengumuman: ";
    Barryvdh\DomPDF\Facade\Pdf::loadView('siswa.pdf.surat-pengumuman', compact('pendaftar'))->output();
    echo "OK\n";
} catch (\Exception $e) { echo "ERROR: " . $e->getMessage() . "\n"; }

try {
    echo "4. Report - Executive Summary: ";
    $data = ['total_pendaftar' => 10, 'today_pendaftar' => 2];
    Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.executive-summary', compact('data'))->output();
    echo "OK\n";
} catch (\Exception $e) { echo "ERROR: " . $e->getMessage() . "\n"; }

try {
    echo "5. Report - Pendaftar Periode: ";
    $pendaftar_collection = collect([$pendaftar]);
    // The view reports.pdf.pendaftar-periode expects $pendaftar variable!
    Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.pendaftar-periode', ['pendaftar' => $pendaftar_collection])->output();
    echo "OK\n";
} catch (\Exception $e) { echo "ERROR: " . $e->getMessage() . "\n"; }

try {
    echo "6. Report - Laporan Keuangan: ";
    $rekap = collect([]);
    Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.laporan-keuangan', compact('rekap'))->output();
    echo "OK\n";
} catch (\Exception $e) { echo "ERROR: " . $e->getMessage() . "\n"; }

try {
    echo "7. Keuangan - Rekap: ";
    $rows = [];
    Barryvdh\DomPDF\Facade\Pdf::loadView('keuangan.pdf.rekap', ['rows' => $rows, 'judul' => 'Rekap'])->output();
    echo "OK\n";
} catch (\Exception $e) { echo "ERROR: " . $e->getMessage() . "\n"; }

try {
    echo "8. Kepsek - Laporan Eksekutif: ";
    $gelombang = App\Models\Gelombang::first();
    $laporan = ['grafik' => [], 'data' => []];
    Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.laporan-eksekutif', compact('gelombang', 'laporan'))->output();
    echo "OK\n";
} catch (\Exception $e) { echo "ERROR: " . $e->getMessage() . "\n"; }

try {
    echo "9. Dashboard - Executive Dashboard: ";
    $executiveSummary = [
        'period_start' => now()->subDays(7)->format('Y-m-d'),
        'period_end' => now()->format('Y-m-d'),
        'total_pendaftar' => 100,
        'target_pendaftar' => 200,
        'pertumbuhan' => 5,
        'status_dist' => ['LULUS' => 10],
        'lulus_percentage' => 10,
        'kpi_summary' => ['pendaftar' => ['status' => 'good'], 'pembayaran' => ['status' => 'good'], 'conversion' => ['status' => 'good']],
        'financial' => ['total_revenue' => 100000, 'projected_revenue' => 200000, 'completion_rate' => 50],
        'alerts' => [],
        'recommendations' => []
    ];
    Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.executive-dashboard', $executiveSummary)->output();
    echo "OK\n";
} catch (\Exception $e) { echo "ERROR: " . $e->getMessage() . "\n"; }

try {
    echo "10. Audit Log: ";
    $logs = App\Models\AuditLog::limit(5)->get();
    Barryvdh\DomPDF\Facade\Pdf::loadView('admin.audit-logs.pdf', compact('logs'))->output();
    echo "OK\n";
} catch (\Exception $e) { echo "ERROR: " . $e->getMessage() . "\n"; }
