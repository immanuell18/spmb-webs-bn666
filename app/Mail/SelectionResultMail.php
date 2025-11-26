<?php

namespace App\Mail;

use App\Models\Pendaftar;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SelectionResultMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pendaftar;

    public function __construct(Pendaftar $pendaftar)
    {
        $this->pendaftar = $pendaftar;
    }

    public function build()
    {
        $status = $this->pendaftar->status_akhir ?? 'PENDING';
        $subject = match($status) {
            'LULUS' => 'Selamat! Anda Diterima',
            'TIDAK_LULUS' => 'Hasil Seleksi SPMB',
            'CADANGAN' => 'Anda Masuk Daftar Cadangan',
            default => 'Pengumuman Hasil Seleksi'
        };

        return $this->subject($subject . ' - ' . $this->pendaftar->no_pendaftaran)
                    ->view('emails.selection-result')
                    ->with([
                        'pendaftar' => $this->pendaftar,
                        'status' => $status,
                        'dashboardUrl' => route('siswa.dashboard')
                    ]);
    }
}