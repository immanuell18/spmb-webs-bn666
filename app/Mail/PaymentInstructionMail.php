<?php

namespace App\Mail;

use App\Models\Pendaftar;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentInstructionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pendaftar;

    public function __construct(Pendaftar $pendaftar)
    {
        $this->pendaftar = $pendaftar;
    }

    public function build()
    {
        return $this->subject('Instruksi Pembayaran - ' . $this->pendaftar->no_pendaftaran)
                    ->view('emails.payment-instruction')
                    ->with([
                        'pendaftar' => $this->pendaftar,
                        'amount' => $this->pendaftar->biaya_pendaftaran ?? $this->pendaftar->gelombang->biaya_daftar,
                        'paymentUrl' => route('siswa.pembayaran')
                    ]);
    }
}