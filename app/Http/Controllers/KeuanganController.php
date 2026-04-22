<?php

namespace App\Http\Controllers;

use App\Models\Gelombang;
use App\Models\Jurusan;
use App\Models\Pendaftar;
use App\Services\KeuanganService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

/**
 * KeuanganController
 *
 * Responsibility: Koordinasi request-response untuk role keuangan.
 * Logika kalkulasi dan query sudah dipindah ke KeuanganService.
 */
class KeuanganController extends Controller
{
    public function __construct(
        private readonly KeuanganService $keuanganService
    ) {}

    // =============================================
    // DASHBOARD
    // =============================================

    public function dashboard()
    {
        $rekapGelombang = $this->keuanganService->getRekapGelombang();
        $rekapJurusan   = $this->keuanganService->getRekapJurusan();
        $stats          = $this->keuanganService->getStats($rekapGelombang);

        // Tren pembayaran 7 hari terakhir
        $trenPembayaran = Pendaftar::selectRaw('DATE(tgl_verifikasi_payment) as tanggal, COUNT(*) as jumlah')
            ->where('status', 'PAID')
            ->where('tgl_verifikasi_payment', '>=', now()->subDays(7))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // Pembayaran yang menunggu verifikasi
        $pembayaranPending = Pendaftar::with(['jurusan', 'gelombang', 'berkas' => fn($q) => $q->where('jenis', 'BUKTI_BAYAR')])
            ->where('status', 'ADM_PASS')
            ->whereHas('berkas', fn($q) => $q->where('jenis', 'BUKTI_BAYAR'))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('keuangan.dashboard', compact(
            'rekapGelombang', 'rekapJurusan', 'stats',
            'trenPembayaran', 'pembayaranPending'
        ) + ['gelombangAktif' => Gelombang::getActive()]);
    }

    // =============================================
    // VERIFIKASI PEMBAYARAN
    // =============================================

    public function verifikasiPembayaran()
    {
        $pendaftar = Pendaftar::with(['jurusan', 'gelombang', 'berkas', 'paymentTransactions'])
            ->where('status', 'ADM_PASS')
            ->whereHas('berkas', fn($q) => $q->where('jenis', 'BUKTI_BAYAR'))
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('keuangan.verifikasi-pembayaran', compact('pendaftar'));
    }

    public function prosesPembayaran(Request $request, $id)
    {
        $request->validate([
            'status'  => 'required|in:terbayar,reject,terima,tolak',
            'catatan' => 'nullable|string|max:500',
        ]);

        $pendaftar = Pendaftar::findOrFail($id);

        if ($pendaftar->status !== 'ADM_PASS') {
            return redirect()->back()->with('error', 'Pendaftar belum lulus verifikasi administrasi');
        }

        $diterima = in_array($request->status, ['terbayar', 'terima']);

        if ($diterima) {
            $pendaftar->update([
                'status'                  => Pendaftar::STATUS_PAID,
                'status_pembayaran'       => 'terbayar',
                'user_verifikasi_payment' => auth()->user()->name,
                'tgl_verifikasi_payment'  => now(),
                'catatan_admin'           => $request->catatan ?? 'Pembayaran diverifikasi — Menunggu pengumuman',
            ]);
            $message = 'Pembayaran berhasil diverifikasi — Status: PAID';
        } else {
            $pendaftar->update([
                'status_pembayaran' => 'belum_bayar',
                'catatan_admin'     => $request->catatan ?? 'Pembayaran ditolak, silakan upload ulang bukti pembayaran yang valid',
            ]);
            $message = 'Pembayaran ditolak — Siswa perlu upload ulang';
        }

        return redirect()->back()->with('success', $message);
    }

    // =============================================
    // REKAP KEUANGAN
    // =============================================

    public function rekapKeuangan(Request $request)
    {
        $rekap = $this->keuanganService->getRekapKeuangan(
            $request->gelombang,
            $request->jurusan,
            $request->periode
        );

        return view('keuangan.rekap', [
            'rekap'    => $rekap,
            'gelombang' => Gelombang::all(),
            'jurusan'  => Jurusan::all(),
        ]);
    }

    // =============================================
    // EXPORT
    // =============================================

    public function exportExcel(Request $request)
    {
        $rekap    = $this->keuanganService->getRekapKeuangan($request->gelombang, $request->jurusan, null);
        $filename = 'rekap_keuangan_' . date('Y-m-d_H-i-s') . '.xlsx';

        try {
            return Excel::download(new \App\Exports\RekapKeuanganExport($rekap), $filename, \Maatwebsite\Excel\Excel::XLSX);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal export Excel: ' . $e->getMessage());
        }
    }

    public function exportPdf(Request $request)
    {
        $rekap = $this->keuanganService->getRekapKeuangan($request->gelombang, $request->jurusan, null);

        $rows = $rekap->map(fn($r) => [
            'gelombang'      => $r->gelombang->nama ?? '-',
            'jurusan'        => $r->jurusan->nama ?? '-',
            'total_pendaftar' => $r->total_pendaftar,
            'sudah_bayar'    => $r->sudah_bayar,
            'total_pemasukan' => 'Rp ' . number_format($r->total_pemasukan, 0, ',', '.'),
        ]);

        $pdf = Pdf::loadView('keuangan.pdf.rekap', ['rows' => $rows, 'judul' => 'Rekap Keuangan SPMB']);
        return $pdf->download('rekap_keuangan_' . date('Y-m-d') . '.pdf');
    }
}