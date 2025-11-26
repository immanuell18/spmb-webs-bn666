<?php

namespace App\Jobs;

use App\Exports\PendaftarExport;
use App\Exports\PembayaranExport;
use App\Exports\SebaranGeografisExport;
use App\Exports\MultiSheetExport;
use App\Mail\ReportExportCompleted;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProcessReportExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $type;
    protected $format;
    protected $filters;
    protected $email;
    protected $jobId;

    public function __construct($type, $format, $filters, $email, $jobId)
    {
        $this->type = $type;
        $this->format = $format;
        $this->filters = $filters;
        $this->email = $email;
        $this->jobId = $jobId;
    }

    public function handle()
    {
        try {
            $filename = $this->generateFilename();
            $filePath = 'exports/' . $filename;

            // Generate export based on type
            switch ($this->type) {
                case 'pendaftar':
                    Excel::store(new PendaftarExport($this->filters), $filePath);
                    break;
                case 'pembayaran':
                    Excel::store(new PembayaranExport($this->filters), $filePath);
                    break;
                case 'geografis':
                    Excel::store(new SebaranGeografisExport($this->filters), $filePath);
                    break;
                case 'multi-sheet':
                    Excel::store(new MultiSheetExport($this->filters), $filePath);
                    break;
            }

            // Send email notification
            Mail::to($this->email)->send(new ReportExportCompleted($filename, $filePath, $this->type));

        } catch (\Exception $e) {
            // Log error and send failure notification
            \Log::error('Export job failed: ' . $e->getMessage());
            
            Mail::to($this->email)->send(new \App\Mail\ReportExportFailed($this->type, $e->getMessage()));
        }
    }

    private function generateFilename()
    {
        $typeNames = [
            'pendaftar' => 'data-pendaftar',
            'pembayaran' => 'laporan-pembayaran',
            'geografis' => 'sebaran-geografis',
            'multi-sheet' => 'laporan-lengkap'
        ];
        
        $name = $typeNames[$this->type] ?? 'laporan';
        return $name . '-' . date('Y-m-d-H-i-s') . '.' . $this->format;
    }
}