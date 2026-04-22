<?php

namespace App\Jobs;

use App\Mail\PendaftarNotification;
use App\Models\NotificationLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * SendNotificationEmailJob
 *
 * Job untuk kirim email notifikasi ke pendaftar secara asynchronous.
 * Digunakan oleh NotificationService menggantikan Mail::send() langsung.
 *
 * States yang dikirim:
 *  - aktivasi         → setelah OTP diverifikasi
 *  - berkas_diterima  → setelah admin approve berkas
 *  - berkas_ditolak   → setelah admin reject berkas
 *  - instruksi_bayar  → setelah berkas diterima
 *  - pembayaran_diterima → setelah keuangan konfirmasi bayar
 *  - pengumuman       → setelah set status akhir
 */
class SendNotificationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Retry 3x jika SMTP error */
    public int $tries = 3;

    /** Timeout 45 detik — email notifikasi lebih besar dari OTP */
    public int $timeout = 45;

    public function backoff(): array
    {
        return [30, 120, 600]; // 30s, 2 menit, 10 menit
    }

    public function __construct(
        private readonly string $toEmail,
        private readonly int    $pendaftarId,
        private readonly string $type,
        private readonly array  $data = [],
        // Serialize minimal data pendaftar agar tidak load dari DB saat queue jalan
        private readonly string $pendaftarNama  = '',
        private readonly string $pendaftarNoPendaftaran = '',
    ) {}

    public function handle(): void
    {
        // Lazy-load pendaftar dari DB saat job dieksekusi
        $pendaftar = \App\Models\Pendaftar::with(['jurusan', 'gelombang', 'user'])
                                         ->find($this->pendaftarId);

        if (!$pendaftar) {
            Log::warning('[Notifikasi] Pendaftar tidak ditemukan, skip email', [
                'pendaftar_id' => $this->pendaftarId,
                'type'         => $this->type,
            ]);
            return;
        }

        Mail::to($this->toEmail)->send(
            new PendaftarNotification($pendaftar, $this->type, $this->data)
        );

        // Update log ke 'sent'
        $this->updateLog('sent');

        Log::info('[Notifikasi] Email berhasil dikirim', [
            'pendaftar_id' => $this->pendaftarId,
            'type'         => $this->type,
            'attempt'      => $this->attempts(),
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        $this->updateLog('failed', $exception->getMessage());

        Log::error('[Notifikasi] Gagal kirim email notifikasi', [
            'pendaftar_id' => $this->pendaftarId,
            'type'         => $this->type,
            'error'        => $exception->getMessage(),
        ]);
    }

    private function updateLog(string $status, ?string $error = null): void
    {
        try {
            NotificationLog::where('pendaftar_id', $this->pendaftarId)
                           ->where('type', $this->type)
                           ->latest()
                           ->first()
                           ?->update([
                                'status'        => $status,
                                'error_message' => $error,
                                'sent_at'       => $status === 'sent' ? now() : null,
                            ]);
        } catch (\Throwable $e) {
            Log::warning('[Notifikasi] Gagal update log: ' . $e->getMessage());
        }
    }
}
