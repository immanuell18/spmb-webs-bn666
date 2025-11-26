<?php

namespace App\Jobs;

use App\Exports\SPMBMultiSheetExport;
use App\Mail\ReportExportCompleted;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ExportReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $exportType;
    protected $filters;
    protected $fileName;

    public function __construct($userId, $exportType, $filters = [])
    {
        $this->userId = $userId;
        $this->exportType = $exportType;
        $this->filters = $filters;
        $this->fileName = 'SPMB_Report_' . date('Y-m-d_H-i-s') . '.xlsx';
    }

    public function handle(): void
    {
        try {
            $user = User::find($this->userId);
            
            // Generate export berdasarkan type
            switch ($this->exportType) {
                case 'multi_sheet':
                    Excel::store(new SPMBMultiSheetExport($this->filters), 'exports/' . $this->fileName, 'public');
                    break;
                    
                default:
                    throw new \Exception('Unknown export type: ' . $this->exportType);
            }
            
            // Generate download URL dengan expiry 24 jam
            $downloadUrl = url('storage/exports/' . $this->fileName);
            $expiryTime = now()->addHours(24);
            
            // Kirim email notifikasi
            Mail::to($user->email)->send(new ReportExportCompleted(
                $user->name,
                $this->exportType,
                $downloadUrl,
                $expiryTime,
                $this->fileName
            ));
            
            // Schedule file deletion setelah 24 jam
            \App\Jobs\DeleteExpiredFileJob::dispatch('exports/' . $this->fileName)
                ->delay($expiryTime);
                
        } catch (\Exception $e) {
            \Log::error('Export job failed: ' . $e->getMessage());
            
            // Kirim email error notification
            if ($user = User::find($this->userId)) {
                Mail::to($user->email)->send(new \App\Mail\ReportExportFailed(
                    $user->name,
                    $this->exportType,
                    $e->getMessage()
                ));
            }
            
            throw $e;
        }
    }
}