<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class RekapKeuanganExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $rekap;

    public function __construct($rekap)
    {
        $this->rekap = $rekap;
    }

    public function collection()
    {
        return $this->rekap;
    }

    public function headings(): array
    {
        return [
            'Gelombang',
            'Jurusan', 
            'Total Pendaftar',
            'Sudah Bayar',
            'Total Pemasukan (Rp)'
        ];
    }

    public function map($row): array
    {
        return [
            $row->gelombang->nama ?? '-',
            $row->jurusan->nama ?? '-',
            (int) $row->total_pendaftar,
            (int) $row->sudah_bayar,
            (int) $row->total_pemasukan
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'E:E' => ['numberFormat' => ['formatCode' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1]]
        ];
    }

    public function title(): string
    {
        return 'Rekap Keuangan SPMB';
    }
}