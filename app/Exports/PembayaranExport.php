<?php

namespace App\Exports;

use App\Models\Pendaftar;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PembayaranExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Pendaftar::with(['jurusan', 'gelombang', 'dataSiswa', 'berkas'])
                         ->whereIn('status', ['PAID', 'ADM_PASS']);

        if (!empty($this->filters['jurusan_id'])) {
            $query->where('jurusan_id', $this->filters['jurusan_id']);
        }

        if (!empty($this->filters['gelombang_id'])) {
            $query->where('gelombang_id', $this->filters['gelombang_id']);
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
            'Jurusan',
            'Gelombang',
            'Status Pembayaran',
            'Biaya Pendaftaran',
            'Tanggal Upload Bukti',
            'Status Verifikasi',
            'Tanggal Verifikasi'
        ];
    }

    public function map($pendaftar): array
    {
        $buktiPembayaran = $pendaftar->berkas->where('jenis_berkas', 'bukti_pembayaran')->first();
        
        return [
            $pendaftar->no_pendaftaran,
            $pendaftar->nama,
            $pendaftar->jurusan->nama ?? '',
            $pendaftar->gelombang->nama ?? '',
            $pendaftar->status === 'PAID' ? 'Sudah Bayar' : 'Belum Bayar',
            'Rp 250.000',
            $buktiPembayaran ? $buktiPembayaran->created_at->format('d/m/Y H:i') : '-',
            $pendaftar->status === 'PAID' ? 'Terverifikasi' : 'Pending',
            $buktiPembayaran && $pendaftar->status === 'PAID' ? $buktiPembayaran->updated_at->format('d/m/Y H:i') : '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}