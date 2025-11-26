<?php

namespace App\Services;

use App\Models\Pendaftar;
use Illuminate\Support\Facades\DB;

class MapService
{
    public function getAreaStatistics($filters = [])
    {
        $query = Pendaftar::with(['dataSiswa'])
                         ->whereHas('dataSiswa', function($q) {
                             $q->whereNotNull('lat')->whereNotNull('lng');
                         });

        // Apply filters
        if (!empty($filters['jurusan_id'])) {
            $query->where('jurusan_id', $filters['jurusan_id']);
        }

        if (!empty($filters['gelombang_id'])) {
            $query->where('gelombang_id', $filters['gelombang_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $pendaftar = $query->get();

        return [
            'total_with_coordinates' => $pendaftar->count(),
            'by_status' => $pendaftar->groupBy('status')->map->count(),
            'by_jurusan' => $pendaftar->groupBy('jurusan.nama')->map->count(),
        ];
    }

    public function getTopAreas($limit = 5, $filters = [])
    {
        $query = DB::table('pendaftar')
            ->join('pendaftar_data_siswa', 'pendaftar.id', '=', 'pendaftar_data_siswa.pendaftar_id')
            ->whereNotNull('pendaftar_data_siswa.lat')
            ->whereNotNull('pendaftar_data_siswa.lng');

        // Apply filters
        if (!empty($filters['jurusan_id'])) {
            $query->where('pendaftar.jurusan_id', $filters['jurusan_id']);
        }

        if (!empty($filters['gelombang_id'])) {
            $query->where('pendaftar.gelombang_id', $filters['gelombang_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('pendaftar.status', $filters['status']);
        }

        // Extract area from address (last part after comma)
        $areas = $query->select(
                DB::raw('TRIM(SUBSTRING_INDEX(alamat, ",", -1)) as area'),
                DB::raw('COUNT(*) as total_pendaftar'),
                DB::raw('SUM(CASE WHEN pendaftar.status = "PAID" THEN 1 ELSE 0 END) as sudah_bayar')
            )
            ->groupBy('area')
            ->orderByDesc('total_pendaftar')
            ->limit($limit)
            ->get();

        return $areas;
    }

    public function generateMapExport($filters = [])
    {
        $query = Pendaftar::with(['jurusan', 'gelombang', 'dataSiswa'])
                         ->whereHas('dataSiswa', function($q) {
                             $q->whereNotNull('lat')->whereNotNull('lng');
                         });

        // Apply filters
        if (!empty($filters['jurusan_id'])) {
            $query->where('jurusan_id', $filters['jurusan_id']);
        }

        if (!empty($filters['gelombang_id'])) {
            $query->where('gelombang_id', $filters['gelombang_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $pendaftar = $query->get();

        return [
            'export_date' => now()->format('Y-m-d H:i:s'),
            'filters_applied' => $filters,
            'total_records' => $pendaftar->count(),
            'data' => $pendaftar->map(function($p) {
                return [
                    'no_pendaftaran' => $p->no_pendaftaran,
                    'nama' => $p->nama,
                    'jurusan' => $p->jurusan->nama,
                    'gelombang' => $p->gelombang->nama,
                    'status' => $p->status,
                    'alamat' => $p->dataSiswa->alamat,
                    'latitude' => $p->dataSiswa->lat,
                    'longitude' => $p->dataSiswa->lng,
                ];
            })
        ];
    }
}