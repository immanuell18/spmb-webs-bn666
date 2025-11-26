<?php

namespace App\Exports;

use App\Models\AuditLog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AuditLogExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = AuditLog::with('user')->latest();

        if (isset($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }

        if (isset($this->filters['action'])) {
            $query->where('action', 'like', '%' . $this->filters['action'] . '%');
        }

        if (isset($this->filters['severity'])) {
            $query->where('severity', $this->filters['severity']);
        }

        if (isset($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (isset($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tanggal',
            'User',
            'Aksi',
            'Deskripsi',
            'IP Address',
            'Severity',
            'Suspicious',
            'URL',
            'Method',
        ];
    }

    public function map($log): array
    {
        return [
            $log->id,
            $log->created_at->format('d/m/Y H:i:s'),
            $log->user->name ?? 'System',
            $log->action,
            $log->description,
            $log->ip_address,
            strtoupper($log->severity),
            $log->is_suspicious ? 'YES' : 'NO',
            $log->url,
            $log->method,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}