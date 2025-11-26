<?php

namespace App\Mail;

use App\Models\Pendaftar;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DocumentCorrectionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pendaftar;
    public $message;

    public function __construct(Pendaftar $pendaftar, string $message)
    {
        $this->pendaftar = $pendaftar;
        $this->message = $message;
    }

    public function build()
    {
        return $this->subject('Perbaikan Berkas Diperlukan - ' . $this->pendaftar->no_pendaftaran)
                    ->view('emails.document-correction')
                    ->with([
                        'pendaftar' => $this->pendaftar,
                        'correctionMessage' => $this->message,
                        'berkasUrl' => route('siswa.berkas')
                    ]);
    }
}