<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiSheetExport implements WithMultipleSheets
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        return [
            'Pendaftar' => new PendaftarExport($this->filters),
            'Pembayaran' => new PembayaranExport($this->filters),
            'Sebaran Geografis' => new SebaranGeografisExport($this->filters),
            'Statistik' => new StatistikExport($this->filters),
        ];
    }
}