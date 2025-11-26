<?php

namespace App\Exports;

use App\Models\Pendaftar;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SebaranGeografisSheet implements FromCollection, WithTitle, WithHeadings, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        return Pendaftar::join('pendaftar_data_siswa', 'pendaftar.id', '=', 'pendaftar_data_siswa.pendaftar_id')
            ->join('jurusan', 'pendaftar.jurusan_id', '=', 'jurusan.id')
            ->selectRaw('
                SUBSTRING_INDEX(pendaftar_data_siswa.alamat, ",", -1) as wilayah,
                jurusan.nama as jurusan,
                COUNT(*) as jumlah_pendaftar,
                AVG(pendaftar_data_siswa.lat) as avg_lat,
                AVG(pendaftar_data_siswa.lng) as avg_lng
            ')
            ->groupBy('wilayah', 'jurusan.nama')
            ->orderByDesc('jumlah_pendaftar')
            ->get()
            ->map(function($item) {
                return [
                    'wilayah' => trim($item->wilayah),
                    'jurusan' => $item->jurusan,
                    'jumlah_pendaftar' => $item->jumlah_pendaftar,
                    'koordinat_lat' => $item->avg_lat ? round($item->avg_lat, 6) : '-',
                    'koordinat_lng' => $item->avg_lng ? round($item->avg_lng, 6) : '-',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Wilayah',
            'Jurusan',
            'Jumlah Pendaftar',
            'Koordinat Latitude',
            'Koordinat Longitude',
        ];
    }

    public function title(): string
    {
        return 'Sebaran Geografis';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}