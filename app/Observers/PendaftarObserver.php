<?php

namespace App\Observers;

use App\Models\Pendaftar;
use App\Models\AuditLog;
use App\Mail\DocumentCorrectionMail;
use App\Mail\PaymentInstructionMail;
use App\Mail\PaymentConfirmationMail;
use App\Mail\SelectionResultMail;
use Illuminate\Support\Facades\Mail;

class PendaftarObserver
{
    public function created(Pendaftar $pendaftar)
    {
        AuditLog::logActivity([
            'action' => 'pendaftar_created',
            'model_type' => Pendaftar::class,
            'model_id' => $pendaftar->id,
            'new_values' => $pendaftar->toArray(),
            'description' => "Pendaftar baru dibuat: {$pendaftar->nama} ({$pendaftar->no_pendaftaran})",
            'severity' => 'medium'
        ]);
    }

    public function updated(Pendaftar $pendaftar)
    {
        $changes = $pendaftar->getChanges();
        $original = $pendaftar->getOriginal();
        
        // Log status changes specifically
        if (isset($changes['status'])) {
            AuditLog::logActivity([
                'action' => 'status_changed',
                'model_type' => Pendaftar::class,
                'model_id' => $pendaftar->id,
                'old_values' => ['status' => $original['status']],
                'new_values' => ['status' => $changes['status']],
                'description' => "Status pendaftar {$pendaftar->nama} berubah dari {$original['status']} ke {$changes['status']}",
                'severity' => 'high'
            ]);

            // Kirim email notifikasi berdasarkan status
            $this->sendStatusNotification($pendaftar, $changes['status'], $original['status']);
        }

        // Kirim notifikasi untuk perubahan status berkas
        if (isset($changes['status_berkas'])) {
            $this->sendDocumentStatusNotification($pendaftar, $changes['status_berkas']);
        }

        // Kirim notifikasi untuk perubahan status pembayaran
        if (isset($changes['status_pembayaran'])) {
            $this->sendPaymentStatusNotification($pendaftar, $changes['status_pembayaran']);
        }

        // Log other updates
        if (!empty($changes)) {
            AuditLog::logActivity([
                'action' => 'pendaftar_updated',
                'model_type' => Pendaftar::class,
                'model_id' => $pendaftar->id,
                'old_values' => array_intersect_key($original, $changes),
                'new_values' => $changes,
                'description' => "Data pendaftar {$pendaftar->nama} diperbarui",
                'severity' => 'medium'
            ]);
        }
    }

    private function sendStatusNotification($pendaftar, $newStatus, $oldStatus): void
    {
        $user = $pendaftar->user;
        if (!$user) return;

        try {
            switch ($newStatus) {
                case 'lulus':
                case 'tidak_lulus':
                case 'cadangan':
                    // queue() memanfaatkan Queueable trait di SelectionResultMail
                    Mail::to($user->email)->queue(new SelectionResultMail($pendaftar, $newStatus));
                    break;
            }
        } catch (\Throwable $e) {
            \Log::error('[Observer] Gagal queue status notification: ' . $e->getMessage(), [
                'pendaftar_id' => $pendaftar->id,
                'status'       => $newStatus,
            ]);
        }
    }

    private function sendDocumentStatusNotification($pendaftar, $status): void
    {
        $user = $pendaftar->user;
        if (!$user) return;

        try {
            switch ($status) {
                case 'ditolak':
                    $reason = $pendaftar->catatan_berkas ?? 'Berkas tidak sesuai persyaratan';
                    Mail::to($user->email)->queue(new DocumentCorrectionMail($pendaftar, $reason));
                    break;
                case 'diterima':
                    Mail::to($user->email)->queue(new PaymentInstructionMail($pendaftar));
                    break;
            }
        } catch (\Throwable $e) {
            \Log::error('[Observer] Gagal queue document notification: ' . $e->getMessage(), [
                'pendaftar_id' => $pendaftar->id,
                'status'       => $status,
            ]);
        }
    }

    private function sendPaymentStatusNotification($pendaftar, $status): void
    {
        $user = $pendaftar->user;
        if (!$user) return;

        try {
            if ($status === 'lunas') {
                Mail::to($user->email)->queue(new PaymentConfirmationMail($pendaftar));
            }
        } catch (\Throwable $e) {
            \Log::error('[Observer] Gagal queue payment notification: ' . $e->getMessage(), [
                'pendaftar_id' => $pendaftar->id,
            ]);
        }
    }

    public function deleted(Pendaftar $pendaftar)
    {
        AuditLog::logActivity([
            'action' => 'pendaftar_deleted',
            'model_type' => Pendaftar::class,
            'model_id' => $pendaftar->id,
            'old_values' => $pendaftar->toArray(),
            'description' => "Pendaftar dihapus: {$pendaftar->nama} ({$pendaftar->no_pendaftaran})",
            'severity' => 'high',
            'is_suspicious' => true
        ]);
    }
}