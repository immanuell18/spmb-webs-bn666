<?php

namespace App\Exports;

use App\Models\Pendaftar;
use App\Models\Jurusan;
use App\Models\Gelombang;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StatistikExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $stats = new Collection();

        // Statistik per Jurusan
        $jurusanStats = Jurusan::withCount([
            'pendaftar',
            'pendaftar as paid_count' => function($query) {
                $query->where('status', 'PAID');
            },
            'pendaftar as verified_count' => function($query) {
                $query->where('status', 'ADM_PASS');
            }
        ])->get();

        foreach ($jurusanStats as $jurusan) {
            $stats->push((object)[
                'kategori' => 'Jurusan',
                'nama' => $jurusan->nama,
                'total_pendaftar' => $jurusan->pendaftar_count,
                'sudah_bayar' => $jurusan->paid_count,
                'terverifikasi' => $jurusan->verified_count,
                'persentase_bayar' => $jurusan->pendaftar_count > 0 ? 
                    round(($jurusan->paid_count / $jurusan->pendaftar_count) * 100, 2) : 0
            ]);
        }

        // Statistik per Gelombang
        $gelombangStats = Gelombang::withCount([
            'pendaftar',
            'pendaftar as paid_count' => function($query) {
                $query->where('status', 'PAID');
            }
        ])->get();

        foreach ($gelombangStats as $gelombang) {
            $stats->push((object)[
                'kategori' => 'Gelombang',
                'nama' => $gelombang->nama,
                'total_pendaftar' => $gelombang->pendaftar_count,
                'sudah_bayar' => $gelombang->paid_count,
                'terverifikasi' => 0,
                'persentase_bayar' => $gelombang->pendaftar_count > 0 ? 
                    round(($gelombang->paid_count / $gelombang->pendaftar_count) * 100, 2) : 0
            ]);
        }

        return $stats;
    }

    public function headings(): array
    {
        return [
            'Kategori',
            'Nama',
            'Total Pendaftar',
            'Sudah Bayar',
            'Terverifikasi',
            'Persentase Bayar (%)'
        ];
    }

    public function map($stat): array
    {
        return [
            $stat->kategori,
            $stat->nama,
            $stat->total_pendaftar,
            $stat->sudah_bayar,
            $stat->terverifikasi,
            $stat->persentase_bayar
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}