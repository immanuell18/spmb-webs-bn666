<?php

namespace App\Http\Controllers;

use App\Exports\SPMBMultiSheetExport;
use App\Jobs\ExportReportJob;
use App\Models\Pendaftar;
use App\Models\Jurusan;
use App\Models\Gelombang;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        $jurusan = Jurusan::all();
        $gelombang = Gelombang::all();
        
        return view('reports.index', compact('jurusan', 'gelombang'));
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->only(['jurusan_id', 'gelombang_id', 'status', 'periode']);
        
        // Jika request background processing (tapi tetap direct download)
        if ($request->background) {
            $fileName = 'SPMB_Report_' . date('Y-m-d_H-i-s') . '.xlsx';
            return Excel::download(new SPMBMultiSheetExport($filters), $fileName);
        }
        
        // Direct download untuk tombol biasa
        $fileName = 'SPMB_Report_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new SPMBMultiSheetExport($filters), $fileName);
    }

    public function exportPdf(Request $request)
    {
        $type = $request->get('type', 'executive_summary');
        
        switch ($type) {
            case 'executive_summary':
                return $this->exportExecutiveSummary($request);
            case 'pendaftar_periode':
                return $this->exportPendaftarPeriode($request);
            case 'laporan_keuangan':
                return $this->exportLaporanKeuangan($request);
            default:
                return back()->with('error', 'Tipe laporan tidak valid');
        }
    }

    private function exportExecutiveSummary($request)
    {
        $data = [
            'total_pendaftar' => Pendaftar::count(),
            'rasio_terverifikasi' => Pendaftar::whereIn('status', ['ADM_PASS', 'PAID'])->count() / max(Pendaftar::count(), 1) * 100,
            'progress_kuota' => Pendaftar::count() / max(Jurusan::sum('kuota'), 1) * 100,
            'total_pemasukan' => Pendaftar::where('status', 'PAID')->count() * \App\Models\SystemSetting::getBiayaPendaftaran(),
            'status_distribusi' => Pendaftar::selectRaw('status, COUNT(*) as jumlah')->groupBy('status')->get(),
            'jurusan_stats' => Jurusan::withCount('pendaftar')->get()->map(function($j) {
                $j->rasio = $j->kuota > 0 ? round(($j->pendaftar_count / $j->kuota) * 100, 1) : 0;
                return $j;
            }),
            'asal_sekolah' => Pendaftar::join('pendaftar_asal_sekolah', 'pendaftar.id', '=', 'pendaftar_asal_sekolah.pendaftar_id')
                ->selectRaw('nama_sekolah, kabupaten, COUNT(*) as jumlah')
                ->groupBy('nama_sekolah', 'kabupaten')
                ->orderByDesc('jumlah')
                ->get(),
        ];
        
        $pdf = Pdf::loadView('reports.pdf.executive-summary', compact('data'));
        return $pdf->download('Executive_Summary_' . date('Y-m-d') . '.pdf');
    }

    private function exportPendaftarPeriode($request)
    {
        $pendaftar = Pendaftar::with(['jurusan', 'gelombang', 'dataSiswa'])
            ->when($request->jurusan_id, fn($q) => $q->where('jurusan_id', $request->jurusan_id))
            ->when($request->gelombang_id, fn($q) => $q->where('gelombang_id', $request->gelombang_id))
            ->orderBy('created_at', 'desc')
            ->get();
        
        $pdf = Pdf::loadView('reports.pdf.pendaftar-periode', compact('pendaftar'));
        return $pdf->download('Laporan_Pendaftar_' . date('Y-m-d') . '.pdf');
    }

    private function exportLaporanKeuangan($request)
    {
        $rekap = Gelombang::withCount([
            'pendaftar',
            'pendaftar as sudah_bayar' => fn($q) => $q->where('status', 'PAID')
        ])->with([
            'pendaftar.paymentTransactions' => fn($q) => $q->where('status', 'paid')
        ])->get();
        
        $pdf = Pdf::loadView('reports.pdf.laporan-keuangan', compact('rekap'));
        return $pdf->download('Laporan_Keuangan_' . date('Y-m-d') . '.pdf');
    }

    public function exportBackground(Request $request)
    {
        $filters = $request->only(['jurusan_id', 'gelombang_id', 'status']);
        
        ExportReportJob::dispatch(auth()->id(), 'multi_sheet', $filters);
        
        return response()->json([
            'success' => true,
            'message' => 'Export sedang diproses di background. Anda akan menerima email notifikasi saat selesai.'
        ]);
    }
}