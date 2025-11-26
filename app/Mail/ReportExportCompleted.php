<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReportExportCompleted extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $exportType;
    public $downloadUrl;
    public $expiryTime;
    public $fileName;

    public function __construct($userName, $exportType, $downloadUrl, $expiryTime, $fileName)
    {
        $this->userName = $userName;
        $this->exportType = $exportType;
        $this->downloadUrl = $downloadUrl;
        $this->expiryTime = $expiryTime;
        $this->fileName = $fileName;
    }

    public function build()
    {
        return $this->subject('Export Laporan SPMB Selesai')
                    ->view('emails.report-export-completed');
    }
}