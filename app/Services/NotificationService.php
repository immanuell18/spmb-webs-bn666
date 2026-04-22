<?php

namespace App\Services;

use App\Jobs\SendNotificationEmailJob;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Log;

/**
 * NotificationService
 *
 * Mengelola pengiriman notifikasi email ke pendaftar.
 * Email dikirim via queue (SendNotificationEmailJob) sehingga
 * tidak memblokir request dan punya retry otomatis.
 */
class NotificationService
{
    /**
     * Kirim notifikasi email ke pendaftar via queue.
     *
     * @param  \App\Models\Pendaftar  $pendaftar
     * @param  string                 $type    aktivasi|berkas_diterima|berkas_ditolak|instruksi_bayar|pembayaran_diterima|pengumuman
     * @param  array                  $data    Data tambahan (alasan, jumlah, dll)
     */
    public function sendNotification($pendaftar, string $type, array $data = []): bool
    {
        // Guard: pastikan ada email tujuan
        $email = $pendaftar->user?->email ?? $pendaftar->email ?? null;

        if (!$email) {
            Log::warning('[Notifikasi] Email pendaftar tidak ditemukan — skip', [
                'pendaftar_id' => $pendaftar->id,
                'type'         => $type,
            ]);
            return false;
        }

        try {
            // Catat dulu ke log dengan status 'queued'
            $this->logNotification($pendaftar, $type, 'queued', $data);

            // Dispatch ke queue background
            SendNotificationEmailJob::dispatch(
                toEmail:              $email,
                pendaftarId:          $pendaftar->id,
                type:                 $type,
                data:                 $data,
                pendaftarNama:        $pendaftar->nama ?? '',
                pendaftarNoPendaftaran: $pendaftar->no_pendaftaran ?? '',
            )->onQueue('emails');

            Log::info('[Notifikasi] Job di-dispatch ke queue', [
                'pendaftar_id' => $pendaftar->id,
                'type'         => $type,
            ]);

            return true;

        } catch (\Throwable $e) {
            Log::error('[Notifikasi] Gagal dispatch job', [
                'pendaftar_id' => $pendaftar->id,
                'type'         => $type,
                'error'        => $e->getMessage(),
            ]);

            $this->logNotification($pendaftar, $type, 'failed', $data, $e->getMessage());

            return false;
        }
    }

    // ================================================================
    // Shortcut Methods (API publik yang dipakai controller)
    // ================================================================

    public function sendAktivasiAkun($pendaftar): bool
    {
        return $this->sendNotification($pendaftar, 'aktivasi');
    }

    public function sendBerkasDitolak($pendaftar, ?string $alasan = null): bool
    {
        return $this->sendNotification($pendaftar, 'berkas_ditolak', ['alasan' => $alasan]);
    }

    public function sendBerkasDiterima($pendaftar): bool
    {
        return $this->sendNotification($pendaftar, 'berkas_diterima');
    }

    public function sendInstruksiBayar($pendaftar, int|float $jumlah, string $batasWaktu = '3 hari'): bool
    {
        return $this->sendNotification($pendaftar, 'instruksi_bayar', [
            'jumlah'     => $jumlah,
            'batas_waktu' => $batasWaktu,
        ]);
    }

    public function sendPembayaranDiterima($pendaftar): bool
    {
        return $this->sendNotification($pendaftar, 'pembayaran_diterima');
    }

    public function sendPengumuman($pendaftar, string $status): bool
    {
        return $this->sendNotification($pendaftar, 'pengumuman', ['status' => $status]);
    }

    // ================================================================
    // Private Helper
    // ================================================================

    private function logNotification(
        $pendaftar,
        string  $type,
        string  $status,
        array   $data = [],
        ?string $error = null
    ): void {
        try {
            NotificationLog::create([
                'pendaftar_id'  => $pendaftar->id,
                'type'          => $type,
                'status'        => $status,
                'data'          => json_encode($data),
                'error_message' => $error,
                'sent_at'       => $status === 'sent' ? now() : null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('[Notifikasi] Gagal log ke database', [
                'pendaftar_id' => $pendaftar->id,
                'type'         => $type,
                'error'        => $e->getMessage(),
            ]);
        }
    }
}