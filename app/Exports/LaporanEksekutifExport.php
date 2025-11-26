<?php

namespace App\Exports;

use App\Models\Gelombang;
use App\Models\Pendaftar;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class LaporanEksekutifExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    public function collection()
    {
        return Gelombang::with(['pendaftar'])->get();
    }

    public function headings(): array
    {
        return [
            'Gelombang',
            'Periode Mulai',
            'Periode Selesai',
            'Total Pendaftar',
            'Terverifikasi',
            'Terbayar',
            'Pemasukan',
            'Status'
        ];
    }

    public function map($gelombang): array
    {
        $status = 'Belum Mulai';
        if ($gelombang->tanggal_selesai < now()) {
            $status = 'Selesai';
        } elseif ($gelombang->tanggal_mulai <= now()) {
            $status = 'Aktif';
        }

        return [
            $gelombang->nama,
            $gelombang->tanggal_mulai,
            $gelombang->tanggal_selesai,
            $gelombang->pendaftar->count(),
            $gelombang->pendaftar->where('status', 'ADM_PASS')->count(),
            $gelombang->pendaftar->where('status_pembayaran', 'terbayar')->count(),
            $gelombang->pendaftar->where('status_pembayaran', 'terbayar')->sum('biaya_pendaftaran'),
            $status
        ];
    }

    public function title(): string
    {
        return 'Laporan Eksekutif';
    }
}