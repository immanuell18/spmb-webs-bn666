<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Gelombang;
use App\Models\Jurusan;
use App\Models\Pendaftar;
use App\Models\Persyaratan;
use App\Models\Wilayah;
use Illuminate\Http\Request;

/**
 * AdminController
 *
 * Responsibility: Dashboard, Master Data view, Monitoring, Peta Sebaran,
 * export, profile, dan audit logs.
 *
 * CRUD resources sudah dipindah ke:
 *   - Admin\JurusanController
 *   - Admin\GelombangController
 *   - Admin\PersyaratanController
 *   - Admin\VerifikasiController
 *   - Admin\PengumumanController
 */
class AdminController extends Controller
{
    // =============================================
    // DASHBOARD
    // =============================================

    public function dashboard(Request $request = null)
    {
        $gelombangAktif    = Gelombang::getActive();
        $selectedGelombang = $request?->get('gelombang');

        // KPI Utama — pakai Eloquent Scopes
        $totalPendaftar     = Pendaftar::filterGelombang($selectedGelombang)->count();
        $pendaftarBaru      = Pendaftar::filterGelombang($selectedGelombang)->baru()->count();
        $menungguVerifikasi = Pendaftar::filterGelombang($selectedGelombang)->menungguVerifikasi()->count();
        $sudahVerifikasi    = Pendaftar::filterGelombang($selectedGelombang)->sudahVerifikasi()->count();
        $sudahBayar         = Pendaftar::filterGelombang($selectedGelombang)->sudahBayar()->count();
        $ditolak            = Pendaftar::filterGelombang($selectedGelombang)->ditolak()->count();

        // Tren pendaftaran 30 hari
        $trenQuery = Pendaftar::trenHarian(30);
        if ($selectedGelombang) {
            $trenQuery->where('gelombang_id', $selectedGelombang);
        }
        $trenHarian = $trenQuery->get();

        if ($trenHarian->isEmpty()) {
            $trenHarian = collect(range(6, 0))->map(fn($i) => (object)[
                'tanggal' => now()->subDays($i)->format('Y-m-d'),
                'jumlah'  => 0,
            ]);
        }

        // Statistik per jurusan
        $statistikJurusan = Jurusan::withCount('pendaftar')
            ->with(['pendaftar' => fn($q) => $q
                ->selectRaw('jurusan_id, status, COUNT(*) as jumlah')
                ->groupBy('jurusan_id', 'status')
            ])->get();

        // Statistik per gelombang
        $statistikGelombang = Gelombang::withCount('pendaftar')
            ->with(['pendaftar' => fn($q) => $q
                ->selectRaw('gelombang_id, status, COUNT(*) as jumlah')
                ->groupBy('gelombang_id', 'status')
            ])->get();

        // Sebaran koordinat
        $sebaranKoordinat = Pendaftar::denganKoordinat()
            ->join('jurusan', 'pendaftar.jurusan_id', '=', 'jurusan.id')
            ->select('pendaftar_data_siswa.lat', 'pendaftar_data_siswa.lng', 'pendaftar.nama', 'jurusan.nama as jurusan')
            ->get();

        // Top 5 asal sekolah
        $topSekolah = Pendaftar::join('pendaftar_asal_sekolah', 'pendaftar.id', '=', 'pendaftar_asal_sekolah.pendaftar_id')
            ->selectRaw('nama_sekolah, COUNT(*) as jumlah')
            ->groupBy('nama_sekolah')
            ->orderByDesc('jumlah')
            ->limit(5)
            ->get();

        // Pendaftar terbaru
        $pendaftarTerbaru = Pendaftar::with(['jurusan', 'gelombang'])
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalPendaftar', 'pendaftarBaru', 'sudahVerifikasi', 'sudahBayar',
            'menungguVerifikasi', 'ditolak', 'trenHarian', 'statistikJurusan',
            'statistikGelombang', 'sebaranKoordinat', 'topSekolah', 'gelombangAktif',
            'pendaftarTerbaru'
        ));
    }

    // =============================================
    // MASTER DATA — view only, CRUD ada di sub-controllers
    // =============================================

    public function masterData()
    {
        $jurusan      = Jurusan::all();
        $gelombang    = Gelombang::all();
        $wilayah      = Wilayah::all();
        $provinces    = \App\Models\Province::all();
        $regencies    = \App\Models\Regency::all();
        $districts    = \App\Models\District::all();
        $villages     = \App\Models\Village::paginate(50);
        $persyaratan  = Persyaratan::orderBy('urutan')->get();

        return view('admin.master-data', compact(
            'jurusan', 'gelombang', 'wilayah',
            'provinces', 'regencies', 'districts', 'villages', 'persyaratan'
        ));
    }

    // =============================================
    // MONITORING BERKAS — dengan pagination
    // =============================================

    public function monitoringBerkas(Request $request)
    {
        $query = Pendaftar::with(['jurusan', 'gelombang', 'dataSiswa', 'dataOrtu', 'asalSekolah', 'berkas']);

        if ($request->filled('jurusan')) {
            $query->where('jurusan_id', $request->jurusan);
        }
        if ($request->filled('gelombang')) {
            $query->where('gelombang_id', $request->gelombang);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('no_pendaftaran', 'like', '%' . $request->search . '%');
        }

        $pendaftar       = $query->paginate(20)->withQueryString();
        $totalPendaftar  = Pendaftar::count();
        $berkasLengkap   = Pendaftar::sudahVerifikasi()->count();
        $pendingReview   = Pendaftar::menungguVerifikasi()->count();
        $tidakLengkap    = Pendaftar::ditolak()->count();
        $jurusan         = Jurusan::all();
        $gelombang       = Gelombang::all();

        return view('admin.monitoring-berkas', compact(
            'pendaftar', 'totalPendaftar', 'berkasLengkap',
            'pendingReview', 'tidakLengkap', 'jurusan', 'gelombang'
        ));
    }

    // =============================================
    // PETA SEBARAN
    // =============================================

    public function petaSebaran(Request $request)
    {
        $query = Pendaftar::with(['jurusan', 'gelombang']);

        if ($request->filled('jurusan')) {
            $query->where('jurusan_id', $request->jurusan);
        }
        if ($request->filled('gelombang')) {
            $query->where('gelombang_id', $request->gelombang);
        }

        $pendaftar = $query->get();

        // TODO: Ganti dummy data dengan query nyata dari pendaftar_data_siswa.kecamatan
        $sebaranKecamatan = collect([
            (object)['kecamatan' => 'Jagakarsa',     'total' => 45],
            (object)['kecamatan' => 'Pasar Minggu',  'total' => 38],
            (object)['kecamatan' => 'Kebayoran Lama','total' => 32],
            (object)['kecamatan' => 'Cilandak',      'total' => 28],
            (object)['kecamatan' => 'Pesanggrahan',  'total' => 25],
        ]);

        $sebaranDetail = collect([
            (object)['kecamatan' => 'Jagakarsa',   'kelurahan' => 'Cipedak',       'jurusan' => 'TKJ', 'total' => 15],
            (object)['kecamatan' => 'Jagakarsa',   'kelurahan' => 'Lenteng Agung', 'jurusan' => 'RPL', 'total' => 12],
            (object)['kecamatan' => 'Pasar Minggu','kelurahan' => 'Kebagusan',     'jurusan' => 'MM',  'total' => 10],
        ]);

        $jurusan   = Jurusan::all();
        $gelombang = Gelombang::all();

        return view('admin.peta-sebaran', compact(
            'pendaftar', 'sebaranKecamatan', 'sebaranDetail', 'jurusan', 'gelombang'
        ));
    }

    public function exportExcel(Request $request)
    {
        $pendaftar = Pendaftar::with(['jurusan', 'gelombang'])->get();
        $fileName = 'data_pendaftar_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No', 'No Pendaftaran', 'Nama', 'Email', 'Jurusan', 'Gelombang', 'Status', 'Tanggal Daftar'];

        $callback = function() use($pendaftar, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            $no = 1;
            foreach ($pendaftar as $p) {
                fputcsv($file, [
                    $no++,
                    $p->no_pendaftaran,
                    $p->nama,
                    $p->email,
                    $p->jurusan->nama ?? '-',
                    $p->gelombang->nama ?? '-',
                    $p->status,
                    $p->created_at->format('Y-m-d')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $pendaftar = Pendaftar::with(['jurusan', 'gelombang'])->orderBy('created_at', 'desc')->get();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.pendaftar', compact('pendaftar'))
                 ->setPaper('a4', 'landscape')
                 ->setOptions(['defaultFont' => 'sans-serif']);
        
        return $pdf->download('laporan-pendaftar-' . date('Y-m-d') . '.pdf');
    }

    // =============================================
    // PROFILE & MISC
    // =============================================

    public function profile()
    {
        return view('admin.profile');
    }

    public function jurusanPublic()
    {
        $jurusan = Jurusan::all();
        return view('jurusan', compact('jurusan'));
    }

    // =============================================
    // AUDIT LOGS
    // =============================================

    public function auditLogs()
    {
        try {
            // Bersihkan log lama — pertahankan hanya 500 terbaru
            $latestIds = AuditLog::orderBy('created_at', 'desc')->limit(500)->pluck('id');
            AuditLog::whereNotIn('id', $latestIds)->delete();

            $logs = AuditLog::orderBy('created_at', 'desc')->paginate(50);

        } catch (\Exception $e) {
            $logs = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]), 0, 50, 1, ['path' => request()->url()]
            );
        }

        return view('admin.audit-logs', compact('logs'));
    }
}