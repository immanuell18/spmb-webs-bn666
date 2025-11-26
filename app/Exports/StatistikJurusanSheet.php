<?php

namespace App\Exports;

use App\Models\Jurusan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StatistikJurusanSheet implements FromCollection, WithTitle, WithHeadings, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        return Jurusan::withCount([
            'pendaftar',
            'pendaftar as submit' => function($q) { $q->where('status', 'SUBMIT'); },
            'pendaftar as adm_pass' => function($q) { $q->where('status', 'ADM_PASS'); },
            'pendaftar as paid' => function($q) { $q->where('status', 'PAID'); },
            'pendaftar as lulus' => function($q) { $q->where('status_akhir', 'LULUS'); },
        ])
        ->get()
        ->map(function($jurusan) {
            return [
                'nama' => $jurusan->nama,
                'kuota' => $jurusan->kuota,
                'total_pendaftar' => $jurusan->pendaftar_count,
                'submit' => $jurusan->submit,
                'adm_pass' => $jurusan->adm_pass,
                'paid' => $jurusan->paid,
                'lulus' => $jurusan->lulus,
                'rasio_kuota' => $jurusan->kuota > 0 ? round(($jurusan->pendaftar_count / $jurusan->kuota) * 100, 1) . '%' : '0%',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Jurusan',
            'Kuota',
            'Total Pendaftar',
            'Submit',
            'Lulus Administrasi',
            'Sudah Bayar',
            'Lulus Seleksi',
            'Rasio Kuota',
        ];
    }

    public function title(): string
    {
        return 'Statistik per Jurusan';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}