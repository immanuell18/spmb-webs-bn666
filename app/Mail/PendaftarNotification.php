<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PendaftarNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $pendaftar;
    public $type;
    public $data;

    public function __construct($pendaftar, $type, $data = [])
    {
        $this->pendaftar = $pendaftar;
        $this->type = $type;
        $this->data = $data;
    }

    public function build()
    {
        $subject = $this->getSubject();
        
        return $this->subject($subject)
                    ->view('emails.pendaftar-notification')
                    ->with([
                        'pendaftar' => $this->pendaftar,
                        'type' => $this->type,
                        'data' => $this->data
                    ]);
    }

    private function getSubject()
    {
        return match($this->type) {
            'aktivasi' => 'Aktivasi Akun SPMB - SMK BAKTI NUSANTARA 666',
            'berkas_ditolak' => 'Perbaikan Berkas Diperlukan - SPMB',
            'berkas_diterima' => 'Berkas Diterima - SPMB',
            'instruksi_bayar' => 'Instruksi Pembayaran - SPMB',
            'pembayaran_diterima' => 'Pembayaran Diterima - SPMB',
            'pengumuman' => 'Pengumuman Hasil Seleksi - SPMB',
            default => 'Notifikasi SPMB'
        };
    }
}