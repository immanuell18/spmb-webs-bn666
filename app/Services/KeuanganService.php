<?php

namespace App\Services;

use App\Models\Gelombang;
use App\Models\Jurusan;
use App\Models\Pendaftar;
use App\Models\SystemSetting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * KeuanganService
 *
 * Responsibility: Kalkulasi dan query data keuangan SPMB.
 * Menghilangkan duplikasi query di KeuanganController
 * (rekapKeuangan, exportExcel, exportPdf — query yang sama persis 3×).
 */
class KeuanganService
{
    /**
     * Ambil rekap keuangan berdasarkan filter opsional.
     *
     * @return Collection
     */
    public function getRekapKeuangan(?string $gelombangId, ?string $jurusanId, ?string $periode): Collection
    {
        $query = Pendaftar::select(
            'gelombang_id',
            'jurusan_id',
            DB::raw('COUNT(*) as total_pendaftar'),
            DB::raw('SUM(CASE WHEN status_pembayaran = "terbayar" THEN biaya_pendaftaran ELSE 0 END) as total_pemasukan'),
            DB::raw('COUNT(CASE WHEN status_pembayaran = "terbayar" THEN 1 END) as sudah_bayar')
        );

        if ($gelombangId) {
            $query->where('gelombang_id', $gelombangId);
        }

        if ($jurusanId) {
            $query->where('jurusan_id', $jurusanId);
        }

        if ($periode) {
            $query->whereYear('created_at', substr($periode, 0, 4))
                  ->whereMonth('created_at', substr($periode, 5, 2));
        }

        return $query->with(['gelombang', 'jurusan'])
                     ->groupBy('gelombang_id', 'jurusan_id')
                     ->get();
    }

    /**
     * Hitung rekap pemasukan per gelombang.
     */
    public function getRekapGelombang(): Collection
    {
        return Gelombang::withCount([
            'pendaftar',
            'pendaftar as sudah_bayar' => fn($q) => $q->where('status', 'PAID'),
        ])->with(['pendaftar' => fn($q) => $q->where('status', 'PAID')])
          ->get()
          ->map(function ($item) {
              $item->total_pemasukan = $item->pendaftar->sum('biaya_pendaftaran');
              return $item;
          });
    }

    /**
     * Hitung rekap pemasukan per jurusan.
     */
    public function getRekapJurusan(): Collection
    {
        return Jurusan::withCount([
            'pendaftar',
            'pendaftar as sudah_bayar' => fn($q) => $q->where('status', 'PAID'),
        ])->with(['pendaftar' => fn($q) => $q->where('status', 'PAID')])
          ->get()
          ->map(function ($item) {
              $item->total_pemasukan = $item->pendaftar->sum('biaya_pendaftaran');
              return $item;
          });
    }

    /**
     * Statistik ringkasan dashboard keuangan.
     */
    public function getStats(Collection $rekapGelombang): array
    {
        return [
            'menunggu_verifikasi' => Pendaftar::sudahVerifikasi()->count(),
            'sudah_bayar'         => Pendaftar::sudahBayar()->count(),
            'belum_bayar'         => Pendaftar::sudahVerifikasi()->count(),
            'total_pemasukan'     => $rekapGelombang->sum('total_pemasukan'),
        ];
    }
}
