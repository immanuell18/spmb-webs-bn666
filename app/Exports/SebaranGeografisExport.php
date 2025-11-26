<?php

namespace App\Exports;

use App\Models\Pendaftar;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SebaranGeografisExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = DB::table('pendaftar')
            ->join('pendaftar_data_siswa', 'pendaftar.id', '=', 'pendaftar_data_siswa.pendaftar_id')
            ->join('jurusan', 'pendaftar.jurusan_id', '=', 'jurusan.id')
            ->select(
                DB::raw('SUBSTRING_INDEX(pendaftar_data_siswa.alamat, ",", -2) as wilayah'),
                'jurusan.nama as jurusan',
                DB::raw('COUNT(*) as total_pendaftar'),
                DB::raw('COUNT(CASE WHEN pendaftar.status = "PAID" THEN 1 END) as sudah_bayar'),
                DB::raw('AVG(pendaftar_data_siswa.lat) as avg_lat'),
                DB::raw('AVG(pendaftar_data_siswa.lng) as avg_lng')
            )
            ->whereNotNull('pendaftar_data_siswa.alamat');

        if (!empty($this->filters['jurusan_id'])) {
            $query->where('pendaftar.jurusan_id', $this->filters['jurusan_id']);
        }

        if (!empty($this->filters['gelombang_id'])) {
            $query->where('pendaftar.gelombang_id', $this->filters['gelombang_id']);
        }

        return $query->groupBy('wilayah', 'jurusan.nama')
                    ->orderBy('total_pendaftar', 'desc')
                    ->get();
    }

    public function headings(): array
    {
        return [
            'Wilayah',
            'Jurusan',
            'Total Pendaftar',
            'Sudah Bayar',
            'Persentase Bayar',
            'Latitude Rata-rata',
            'Longitude Rata-rata'
        ];
    }

    public function map($data): array
    {
        $persentase = $data->total_pendaftar > 0 ? 
            round(($data->sudah_bayar / $data->total_pendaftar) * 100, 2) : 0;

        return [
            $data->wilayah,
            $data->jurusan,
            $data->total_pendaftar,
            $data->sudah_bayar,
            $persentase . '%',
            round($data->avg_lat, 6),
            round($data->avg_lng, 6)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}