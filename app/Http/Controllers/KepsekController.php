<?php

namespace App\Http\Controllers;

use App\Models\Pendaftar;
use App\Models\Jurusan;
use App\Models\Gelombang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanEksekutifExport;

class KepsekController extends Controller
{
    public function dashboard(Request $request)
    {
        // Filter berdasarkan gelombang jika dipilih
        $gelombangFilter = $request->get('gelombang');
        
        // KPI Ringkas sesuai UKK
        $pendaftarQuery = Pendaftar::query();
        if ($gelombangFilter) {
            $pendaftarQuery->where('gelombang_id', $gelombangFilter);
        }
        
        $totalPendaftar = $pendaftarQuery->count();
        $totalKuota = Jurusan::sum('kuota');
        $rasioTerverifikasi = $totalPendaftar > 0 ? 
            round(((clone $pendaftarQuery)->whereIn('status', ['ADM_PASS', 'PAID'])->count() / $totalPendaftar) * 100, 1) : 0;
        $progressKuota = $totalKuota > 0 ? round(($totalPendaftar / $totalKuota) * 100, 1) : 0;
        
        // Tren harian dengan indikator performa
        $trenQuery = Pendaftar::selectRaw('DATE(created_at) as tanggal, COUNT(*) as pendaftar')
            ->where('created_at', '>=', now()->subDays(14));
        if ($gelombangFilter) {
            $trenQuery->where('gelombang_id', $gelombangFilter);
        }
        $trenHarian = $trenQuery->groupBy('tanggal')->orderBy('tanggal')->get();
        
        $avgHarian = $trenHarian->avg('jumlah');
        $trenToday = $trenHarian->where('tanggal', today()->format('Y-m-d'))->first()->jumlah ?? 0;
        $performanceIndicator = $avgHarian > 0 ? ($trenToday >= $avgHarian ? 'good' : 'low') : 'neutral';
        
        // Komposisi asal sekolah
        $asalSekolah = Pendaftar::join('pendaftar_asal_sekolah', 'pendaftar.id', '=', 'pendaftar_asal_sekolah.pendaftar_id')
            ->selectRaw('nama_sekolah, kabupaten, COUNT(*) as jumlah')
            ->groupBy('nama_sekolah', 'kabupaten')
            ->orderByDesc('jumlah')
            ->limit(10)
            ->get();
        
        // Sebaran wilayah
        $sebaranWilayah = Pendaftar::join('pendaftar_data_siswa', 'pendaftar.id', '=', 'pendaftar_data_siswa.pendaftar_id')
            ->selectRaw('SUBSTRING_INDEX(alamat, ",", -1) as wilayah, COUNT(*) as jumlah')
            ->groupBy('wilayah')
            ->orderByDesc('jumlah')
            ->limit(8)
            ->get();
        
        // Status distribusi untuk pie chart
        $statusDistribusi = Pendaftar::selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->get();
        
        // Pendaftar vs Kuota per jurusan
        $jurusanStats = Jurusan::withCount([
            'pendaftar' => function($query) use ($gelombangFilter) {
                if ($gelombangFilter) {
                    $query->where('gelombang_id', $gelombangFilter);
                }
            },
            'pendaftar as terverifikasi_count' => function($query) use ($gelombangFilter) {
                $query->whereIn('status', ['ADM_PASS', 'PAID']);
                if ($gelombangFilter) {
                    $query->where('gelombang_id', $gelombangFilter);
                }
            }
        ])->get();

        $perJurusan = $jurusanStats->map(function($item) {
            return [
                'nama' => $item->nama,
                'kuota' => $item->kuota,
                'pendaftar' => $item->pendaftar_count,
                'terverifikasi' => $item->terverifikasi_count,
            ];
        });
        
        // KPI array untuk view
        $kpi = [
            'total_pendaftar' => $totalPendaftar,
            'total_kuota' => $totalKuota,
            'terverifikasi' => (clone $pendaftarQuery)->whereIn('status', ['ADM_PASS', 'PAID'])->count(),
            'rasio_terverifikasi' => $rasioTerverifikasi,
            'sudah_bayar' => (clone $pendaftarQuery)->where('status', 'PAID')->count(),
            'total_pemasukan' => (clone $pendaftarQuery)->where('status', 'PAID')->sum('biaya_pendaftaran'),
        ];
        
        // Komposisi jurusan untuk chart
        $komposisiJurusan = $jurusanStats;
        
        return view('kepsek.dashboard', compact(
            'totalPendaftar', 'totalKuota', 'rasioTerverifikasi', 'progressKuota',
            'trenHarian', 'avgHarian', 'trenToday', 'performanceIndicator',
            'asalSekolah', 'sebaranWilayah', 'statusDistribusi', 'jurusanStats',
            'kpi', 'komposisiJurusan', 'perJurusan'
        ));
    }

    public function laporanEksekutif()
    {
        $gelombang = Gelombang::with(['pendaftar'])->get();
        
        $laporan = [
            'total_pendaftar' => Pendaftar::count(),
            'rasio_verifikasi' => Pendaftar::whereIn('status', ['ADM_PASS', 'PAID'])->count() / max(Pendaftar::count(), 1) * 100,
            'rasio_pembayaran' => Pendaftar::where('status', 'PAID')->count() / max(Pendaftar::count(), 1) * 100,
            'total_pemasukan' => Pendaftar::where('status', 'PAID')->sum('biaya_pendaftaran'),
        ];

        return view('kepsek.laporan-eksekutif', compact('gelombang', 'laporan'));
    }

    public function exportLaporanPdf()
    {
        $gelombang = Gelombang::with(['pendaftar'])->get();
        
        $laporan = [
            'total_pendaftar' => Pendaftar::count(),
            'rasio_verifikasi' => Pendaftar::whereIn('status', ['ADM_PASS', 'PAID'])->count() / max(Pendaftar::count(), 1) * 100,
            'rasio_pembayaran' => Pendaftar::where('status', 'PAID')->count() / max(Pendaftar::count(), 1) * 100,
            'total_pemasukan' => Pendaftar::where('status', 'PAID')->sum('biaya_pendaftaran'),
        ];

        $pdf = Pdf::loadView('reports.pdf.laporan-eksekutif', compact('gelombang', 'laporan'))
                 ->setPaper('a4', 'portrait')
                 ->setOptions(['defaultFont' => 'sans-serif']);
        
        return $pdf->download('laporan-eksekutif-' . date('Y-m-d') . '.pdf');
    }

    public function exportLaporanExcel()
    {
        return Excel::download(new LaporanEksekutifExport, 'laporan-eksekutif-' . date('Y-m-d') . '.xlsx');
    }
}