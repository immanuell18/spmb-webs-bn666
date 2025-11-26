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

    private function sendStatusNotification($pendaftar, $newStatus, $oldStatus)
    {
        try {
            $user = $pendaftar->user;
            if (!$user) return;

            switch ($newStatus) {
                case 'lulus':
                case 'tidak_lulus':
                case 'cadangan':
                    Mail::to($user->email)->send(new SelectionResultMail($pendaftar, $newStatus));
                    break;
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send status notification: ' . $e->getMessage());
        }
    }

    private function sendDocumentStatusNotification($pendaftar, $status)
    {
        try {
            $user = $pendaftar->user;
            if (!$user) return;

            switch ($status) {
                case 'ditolak':
                    $reason = $pendaftar->catatan_berkas ?? 'Berkas tidak sesuai persyaratan';
                    Mail::to($user->email)->send(new DocumentCorrectionMail($pendaftar, $reason));
                    break;
                case 'diterima':
                    // Kirim instruksi pembayaran
                    Mail::to($user->email)->send(new PaymentInstructionMail($pendaftar));
                    break;
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send document status notification: ' . $e->getMessage());
        }
    }

    private function sendPaymentStatusNotification($pendaftar, $status)
    {
        try {
            $user = $pendaftar->user;
            if (!$user) return;

            if ($status === 'lunas') {
                Mail::to($user->email)->send(new PaymentConfirmationMail($pendaftar));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send payment status notification: ' . $e->getMessage());
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