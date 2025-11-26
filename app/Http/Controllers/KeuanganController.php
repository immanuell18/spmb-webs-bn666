<?php

namespace App\Http\Controllers;

use App\Models\Pendaftar;
use App\Models\Jurusan;
use App\Models\Gelombang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class KeuanganController extends Controller
{
    public function dashboard()
    {
        $gelombangAktif = Gelombang::getActive();
        
        // Rekap pemasukan per gelombang/jurusan
        $rekapGelombang = Gelombang::withCount([
            'pendaftar',
            'pendaftar as sudah_bayar' => function($q) { $q->where('status', 'PAID'); }
        ])
        ->with(['pendaftar' => function($q) {
            $q->where('status', 'PAID');
        }])
        ->get()
        ->map(function($item) {
            $item->total_pemasukan = $item->pendaftar->sum(function($p) {
                return $p->gelombang->biaya_daftar ?: \App\Models\SystemSetting::getBiayaPendaftaran();
            });
            return $item;
        });
        
        $rekapJurusan = Jurusan::withCount([
            'pendaftar',
            'pendaftar as sudah_bayar' => function($q) { $q->where('status', 'PAID'); }
        ])
        ->with(['pendaftar' => function($q) {
            $q->where('status', 'PAID')->with('gelombang');
        }])
        ->get()
        ->map(function($item) {
            $item->total_pemasukan = $item->pendaftar->sum(function($p) {
                return $p->gelombang->biaya_daftar ?: \App\Models\SystemSetting::getBiayaPendaftaran();
            });
            return $item;
        });
        
        // Status pembayaran real-time
        $stats = [
            'menunggu_verifikasi' => Pendaftar::where('status', 'ADM_PASS')->count(),
            'sudah_bayar' => Pendaftar::where('status', 'PAID')->count(),
            'belum_bayar' => Pendaftar::where('status', 'ADM_PASS')->count(),
            'total_pemasukan' => $rekapGelombang->sum('total_pemasukan'),
        ];
        
        // Tren pembayaran harian
        $trenPembayaran = Pendaftar::selectRaw('DATE(tgl_verifikasi_payment) as tanggal, COUNT(*) as jumlah')
            ->where('status', 'PAID')
            ->where('tgl_verifikasi_payment', '>=', now()->subDays(7))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();
        
        // Pembayaran yang perlu diverifikasi
        $pembayaranPending = Pendaftar::with(['jurusan', 'gelombang', 'berkas' => function($q) {
            $q->where('jenis', 'BUKTI_BAYAR');
        }])
        ->where('status', 'ADM_PASS')
        ->whereHas('berkas', function($q) {
            $q->where('jenis', 'BUKTI_BAYAR');
        })
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

        return view('keuangan.dashboard', compact(
            'rekapGelombang', 'rekapJurusan', 'stats', 
            'trenPembayaran', 'pembayaranPending', 'gelombangAktif'
        ));
    }

    public function verifikasiPembayaran()
    {
        $pendaftar = Pendaftar::with(['jurusan', 'gelombang', 'berkas', 'paymentTransactions'])
            ->where('status', 'ADM_PASS')
            ->whereHas('berkas', function($q) {
                $q->where('jenis', 'BUKTI_BAYAR');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('keuangan.verifikasi-pembayaran', compact('pendaftar'));
    }

    public function prosesPembayaran(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:terbayar,reject,terima,tolak',
            'catatan' => 'nullable|string|max:500'
        ]);

        $pendaftar = Pendaftar::findOrFail($id);
        
        // Hanya bisa verifikasi pembayaran jika status ADM_PASS
        if ($pendaftar->status !== 'ADM_PASS') {
            return redirect()->back()->with('error', 'Pendaftar belum lulus verifikasi administrasi');
        }
        
        if (in_array($request->status, ['terbayar', 'terima'])) {
            $pendaftar->update([
                'status' => 'PAID',
                'status_pembayaran' => 'terbayar',
                'user_verifikasi_payment' => auth()->user()->name,
                'tgl_verifikasi_payment' => now(),
                'catatan_admin' => $request->catatan ?? 'Pembayaran diverifikasi - Menunggu pengumuman'
            ]);
            
            $message = 'Pembayaran berhasil diverifikasi - Status: PAID';
        } else {
            $pendaftar->update([
                'status_pembayaran' => 'belum_bayar',
                'catatan_admin' => $request->catatan ?? 'Pembayaran ditolak, silakan upload ulang bukti pembayaran yang valid'
            ]);
            
            $message = 'Pembayaran ditolak - Siswa perlu upload ulang';
        }
        
        return redirect()->back()->with('success', $message);
    }

    public function rekapKeuangan(Request $request)
    {
        $gelombang = Gelombang::all();
        $jurusan = Jurusan::all();
        
        $query = Pendaftar::select(
            'gelombang_id',
            'jurusan_id',
            DB::raw('COUNT(*) as total_pendaftar'),
            DB::raw('SUM(CASE WHEN status_pembayaran = "terbayar" THEN biaya_pendaftaran ELSE 0 END) as total_pemasukan'),
            DB::raw('COUNT(CASE WHEN status_pembayaran = "terbayar" THEN 1 END) as sudah_bayar')
        );
        
        // Apply filters
        if ($request->filled('gelombang')) {
            $query->where('gelombang_id', $request->gelombang);
        }
        
        if ($request->filled('jurusan')) {
            $query->where('jurusan_id', $request->jurusan);
        }
        
        if ($request->filled('periode')) {
            $periode = $request->periode;
            $query->whereYear('created_at', substr($periode, 0, 4))
                  ->whereMonth('created_at', substr($periode, 5, 2));
        }
        
        $rekap = $query->with(['gelombang', 'jurusan'])
                      ->groupBy('gelombang_id', 'jurusan_id')
                      ->get();

        return view('keuangan.rekap', compact('rekap', 'gelombang', 'jurusan'));
    }

    public function exportExcel(Request $request)
    {
        $query = Pendaftar::select(
            'gelombang_id',
            'jurusan_id',
            DB::raw('COUNT(*) as total_pendaftar'),
            DB::raw('SUM(CASE WHEN status_pembayaran = "terbayar" THEN biaya_pendaftaran ELSE 0 END) as total_pemasukan'),
            DB::raw('COUNT(CASE WHEN status_pembayaran = "terbayar" THEN 1 END) as sudah_bayar')
        );
        
        if ($request->filled('gelombang')) {
            $query->where('gelombang_id', $request->gelombang);
        }
        
        if ($request->filled('jurusan')) {
            $query->where('jurusan_id', $request->jurusan);
        }
        
        $rekap = $query->with(['gelombang', 'jurusan'])
                      ->groupBy('gelombang_id', 'jurusan_id')
                      ->get();
        
        $filename = 'rekap_keuangan_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        try {
            return Excel::download(new \App\Exports\RekapKeuanganExport($rekap), $filename, \Maatwebsite\Excel\Excel::XLSX);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal export Excel: ' . $e->getMessage());
        }
    }

    public function exportPdf(Request $request)
    {
        $query = Pendaftar::select(
            'gelombang_id',
            'jurusan_id',
            DB::raw('COUNT(*) as total_pendaftar'),
            DB::raw('SUM(CASE WHEN status_pembayaran = "terbayar" THEN biaya_pendaftaran ELSE 0 END) as total_pemasukan'),
            DB::raw('COUNT(CASE WHEN status_pembayaran = "terbayar" THEN 1 END) as sudah_bayar')
        );
        
        if ($request->filled('gelombang')) {
            $query->where('gelombang_id', $request->gelombang);
        }
        
        if ($request->filled('jurusan')) {
            $query->where('jurusan_id', $request->jurusan);
        }
        
        $rekap = $query->with(['gelombang', 'jurusan'])
                      ->groupBy('gelombang_id', 'jurusan_id')
                      ->get();
        
        $html = '<h2>Rekap Keuangan SPMB</h2><table border="1" cellpadding="5"><tr><th>Gelombang</th><th>Jurusan</th><th>Total Pendaftar</th><th>Sudah Bayar</th><th>Total Pemasukan</th></tr>';
        
        foreach ($rekap as $r) {
            $html .= '<tr><td>' . ($r->gelombang->nama ?? '-') . '</td><td>' . ($r->jurusan->nama ?? '-') . '</td><td>' . $r->total_pendaftar . '</td><td>' . $r->sudah_bayar . '</td><td>Rp ' . number_format($r->total_pemasukan, 0, ',', '.') . '</td></tr>';
        }
        
        $html .= '</table>';
        
        $pdf = Pdf::loadHTML($html);
        return $pdf->download('rekap_keuangan_' . date('Y-m-d') . '.pdf');
    }
}