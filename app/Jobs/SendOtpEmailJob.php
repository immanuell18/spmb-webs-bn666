<?php

namespace App\Jobs;

use App\Mail\OtpMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * SendOtpEmailJob
 *
 * Job terpisah untuk mengirim OTP via email secara asynchronous.
 *
 * Kenapa pakai Job (bukan Mail::queue langsung)?
 *  - Retry otomatis jika SMTP gagal (3x dengan delay 30 detik)
 *  - Bisa monitor via `php artisan queue:work`
 *  - Timeout terkontrol (30 detik max per attempt)
 *  - Logging terpusat jika gagal
 *
 * Cara dispatch:
 *   SendOtpEmailJob::dispatch($email, $otpCode, $userName);
 *   SendOtpEmailJob::dispatch($email, $otpCode, $userName)->onQueue('emails');
 */
class SendOtpEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Maksimal percobaan kirim jika SMTP error.
     * 3x → delay 10s, 60s, 300s (exponential backoff).
     */
    public int $tries = 3;

    /**
     * Timeout per attempt dalam detik.
     * Hindari request nunggu SMTP terlalu lama.
     */
    public int $timeout = 30;

    /**
     * Retry setelah N detik jika gagal (backoff).
     * @return array<int>
     */
    public function backoff(): array
    {
        return [10, 60, 300]; // 10 detik, 1 menit, 5 menit
    }

    public function __construct(
        private readonly string $email,
        private readonly string $otpCode,
        private readonly string $userName,
    ) {}

    /**
     * Kirim email OTP.
     * Dipanggil oleh queue worker secara background.
     */
    public function handle(): void
    {
        Mail::to($this->email)->send(new OtpMail($this->otpCode, $this->userName));

        Log::info('[OTP] Email berhasil dikirim', [
            'email'    => $this->email,
            'attempt'  => $this->attempts(),
        ]);
    }

    /**
     * Handler jika semua percobaan gagal.
     * Dicatat di log agar bisa diinvestigasi.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('[OTP] Gagal mengirim email setelah ' . $this->tries . ' percobaan', [
            'email' => $this->email,
            'error' => $exception->getMessage(),
        ]);
    }
}
