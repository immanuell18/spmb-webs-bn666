<?php

namespace App\Mail;

use App\Models\Pendaftar;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pendaftar;

    public function __construct(Pendaftar $pendaftar)
    {
        $this->pendaftar = $pendaftar;
    }

    public function build()
    {
        return $this->subject('Pembayaran Terverifikasi - ' . $this->pendaftar->no_pendaftaran)
                    ->view('emails.payment-confirmation')
                    ->with([
                        'pendaftar' => $this->pendaftar,
                        'dashboardUrl' => route('siswa.dashboard')
                    ]);
    }
}