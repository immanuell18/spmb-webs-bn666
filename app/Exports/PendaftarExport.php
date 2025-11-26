<?php

namespace App\Exports;

use App\Models\Pendaftar;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PendaftarExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Pendaftar::with(['jurusan', 'gelombang', 'dataSiswa']);

        if (!empty($this->filters['jurusan_id'])) {
            $query->where('jurusan_id', $this->filters['jurusan_id']);
        }

        if (!empty($this->filters['gelombang_id'])) {
            $query->where('gelombang_id', $this->filters['gelombang_id']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No Pendaftaran',
            'Nama Lengkap',
            'NIK',
            'Email',
            'No HP',
            'Jurusan',
            'Gelombang',
            'Status',
            'Tanggal Daftar',
            'Alamat',
            'Nama Ayah',
            'Nama Ibu',
            'Asal Sekolah',
            'Tahun Lulus'
        ];
    }

    public function map($pendaftar): array
    {
        return [
            $pendaftar->no_pendaftaran,
            $pendaftar->nama,
            $pendaftar->dataSiswa->nik ?? '',
            $pendaftar->email,
            $pendaftar->dataSiswa->no_hp ?? '',
            $pendaftar->jurusan->nama ?? '',
            $pendaftar->gelombang->nama ?? '',
            $this->getStatusText($pendaftar->status),
            $pendaftar->created_at->format('d/m/Y H:i'),
            $pendaftar->dataSiswa->alamat ?? '',
            $pendaftar->dataSiswa->nama_ayah ?? '',
            $pendaftar->dataSiswa->nama_ibu ?? '',
            $pendaftar->dataSiswa->nama_sekolah ?? '',
            $pendaftar->dataSiswa->tahun_lulus ?? ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    private function getStatusText($status)
    {
        return match($status) {
            'SUBMIT' => 'Menunggu Verifikasi',
            'ADM_PASS' => 'Berkas Disetujui',
            'ADM_REJECT' => 'Berkas Ditolak',
            'PAID' => 'Sudah Bayar',
            default => 'Unknown'
        };
    }
}