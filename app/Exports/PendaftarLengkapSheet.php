<?php

namespace App\Exports;

use App\Models\Pendaftar;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PendaftarLengkapSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Pendaftar::with(['jurusan', 'gelombang', 'dataSiswa', 'dataOrtu', 'asalSekolah']);
        
        if (isset($this->filters['jurusan_id'])) {
            $query->where('jurusan_id', $this->filters['jurusan_id']);
        }
        
        if (isset($this->filters['gelombang_id'])) {
            $query->where('gelombang_id', $this->filters['gelombang_id']);
        }
        
        if (isset($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        
        return $query->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'No. Pendaftaran',
            'Nama Lengkap',
            'NIK',
            'NISN',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Agama',
            'Alamat',
            'Jurusan',
            'Gelombang',
            'Status',
            'Status Akhir',
            'Nama Ayah',
            'Nama Ibu',
            'Asal Sekolah',
            'Nilai Rata-rata',
            'Tanggal Daftar',
        ];
    }

    public function map($pendaftar): array
    {
        return [
            $pendaftar->no_pendaftaran,
            $pendaftar->nama,
            $pendaftar->dataSiswa->nik ?? '-',
            $pendaftar->dataSiswa->nisn ?? '-',
            $pendaftar->dataSiswa->jk ?? '-',
            $pendaftar->dataSiswa->tmp_lahir ?? '-',
            $pendaftar->dataSiswa->tgl_lahir ?? '-',
            $pendaftar->dataSiswa->agama ?? '-',
            $pendaftar->dataSiswa->alamat ?? '-',
            $pendaftar->jurusan->nama ?? '-',
            $pendaftar->gelombang->nama ?? '-',
            $pendaftar->status,
            $pendaftar->status_akhir ?? '-',
            $pendaftar->dataOrtu->nama_ayah ?? '-',
            $pendaftar->dataOrtu->nama_ibu ?? '-',
            $pendaftar->asalSekolah->nama_sekolah ?? '-',
            $pendaftar->asalSekolah->nilai_rata ?? '-',
            $pendaftar->created_at->format('d/m/Y'),
        ];
    }

    public function title(): string
    {
        return 'Data Pendaftar Lengkap';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}