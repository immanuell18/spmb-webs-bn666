<?php

namespace App\Exports;

use App\Models\Pendaftar;
use App\Models\Jurusan;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SPMBMultiSheetExport implements WithMultipleSheets
{
    use Exportable;

    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        return [
            0 => new PendaftarLengkapSheet($this->filters),
            1 => new StatistikJurusanSheet($this->filters),
            2 => new SebaranGeografisSheet($this->filters),
            3 => new RekapPembayaranSheet($this->filters),
        ];
    }
}