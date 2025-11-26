<?php

namespace App\Exports;

use App\Models\Gelombang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapPembayaranSheet implements FromCollection, WithTitle, WithHeadings, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        return Gelombang::withCount([
            'pendaftar',
            'pendaftar as sudah_bayar' => function($q) { $q->where('status', 'PAID'); },
            'pendaftar as belum_bayar' => function($q) { $q->where('status', 'ADM_PASS'); },
        ])
        ->get()
        ->map(function($gelombang) {
            $totalPemasukan = $gelombang->sudah_bayar * $gelombang->biaya_daftar;
            $potensialPemasukan = $gelombang->belum_bayar * $gelombang->biaya_daftar;
            
            return [
                'gelombang' => $gelombang->nama,
                'tahun' => $gelombang->tahun,
                'biaya_daftar' => 'Rp ' . number_format($gelombang->biaya_daftar, 0, ',', '.'),
                'total_pendaftar' => $gelombang->pendaftar_count,
                'sudah_bayar' => $gelombang->sudah_bayar,
                'belum_bayar' => $gelombang->belum_bayar,
                'total_pemasukan' => 'Rp ' . number_format($totalPemasukan, 0, ',', '.'),
                'potensial_pemasukan' => 'Rp ' . number_format($potensialPemasukan, 0, ',', '.'),
                'rasio_pembayaran' => $gelombang->pendaftar_count > 0 ? 
                    round(($gelombang->sudah_bayar / $gelombang->pendaftar_count) * 100, 1) . '%' : '0%',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Gelombang',
            'Tahun',
            'Biaya Daftar',
            'Total Pendaftar',
            'Sudah Bayar',
            'Belum Bayar',
            'Total Pemasukan',
            'Potensial Pemasukan',
            'Rasio Pembayaran',
        ];
    }

    public function title(): string
    {
        return 'Rekap Pembayaran';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}