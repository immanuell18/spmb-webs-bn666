<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Events\DocumentVerificationRequested;
use App\Events\PaymentInstructionSent;
use App\Events\PaymentVerified;
use App\Events\SelectionResultAnnounced;
use App\Mail\AccountActivationMail;
use App\Mail\DocumentCorrectionMail;
use App\Mail\PaymentInstructionMail;
use App\Mail\PaymentConfirmationMail;
use App\Mail\SelectionResultMail;
use App\Notifications\SPMBNotification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Mail;

class SendNotificationListener
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle($event)
    {
        switch (get_class($event)) {
            case UserRegistered::class:
                $this->handleUserRegistered($event);
                break;
            case DocumentVerificationRequested::class:
                $this->handleDocumentVerificationRequested($event);
                break;
            case PaymentInstructionSent::class:
                $this->handlePaymentInstructionSent($event);
                break;
            case PaymentVerified::class:
                $this->handlePaymentVerified($event);
                break;
            case SelectionResultAnnounced::class:
                $this->handleSelectionResultAnnounced($event);
                break;
        }
    }

    private function handleUserRegistered(UserRegistered $event)
    {
        Mail::to($event->user->email)->send(new AccountActivationMail($event->user));
        
        $event->user->notify(new SPMBNotification(
            'account_activation',
            'Akun Berhasil Dibuat',
            'Selamat datang! Akun Anda telah berhasil dibuat. Silakan login untuk melanjutkan pendaftaran.',
            route('login'),
            'Login Sekarang'
        ));

        // Log notification manually
        try {
            \App\Models\NotificationLog::create([
                'user_id' => $event->user->id,
                'type' => 'registration',
                'channel' => 'email',
                'recipient' => $event->user->email,
                'message' => 'Akun berhasil dibuat',
                'status' => 'sent',
                'sent_at' => now()
            ]);
        } catch (\Exception $e) {
            \Log::warning('Failed to log notification', ['error' => $e->getMessage()]);
        }
    }

    private function handleDocumentVerificationRequested(DocumentVerificationRequested $event)
    {
        $user = $event->pendaftar->user;
        
        \Log::info('DocumentVerificationRequested event triggered', [
            'pendaftar_id' => $event->pendaftar->id,
            'user_email' => $user->email,
            'message' => $event->message
        ]);
        
        try {
            Mail::to($user->email)->send(new DocumentCorrectionMail($event->pendaftar, $event->message));
            \Log::info('DocumentCorrectionMail sent successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to send DocumentCorrectionMail', ['error' => $e->getMessage()]);
        }
        
        try {
            $user->notify(new SPMBNotification(
                'document_correction',
                'Perbaikan Berkas Diperlukan',
                'Berkas Anda memerlukan perbaikan. ' . $event->message,
                route('siswa.berkas'),
                'Perbaiki Berkas'
            ));
            \Log::info('SPMBNotification sent successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to send SPMBNotification', ['error' => $e->getMessage()]);
        }

        // Log notification manually since service expects different parameters
        try {
            \App\Models\NotificationLog::create([
                'user_id' => $user->id,
                'type' => 'verification',
                'channel' => 'email',
                'recipient' => $user->email,
                'message' => 'Permintaan perbaikan berkas',
                'status' => 'sent',
                'sent_at' => now()
            ]);
        } catch (\Exception $e) {
            \Log::warning('Failed to log notification', ['error' => $e->getMessage()]);
        }
    }

    private function handlePaymentInstructionSent(PaymentInstructionSent $event)
    {
        $user = $event->pendaftar->user;
        
        \Log::info('PaymentInstructionSent event triggered', [
            'pendaftar_id' => $event->pendaftar->id,
            'user_email' => $user->email
        ]);
        
        try {
            Mail::to($user->email)->send(new PaymentInstructionMail($event->pendaftar));
            \Log::info('PaymentInstructionMail sent successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to send PaymentInstructionMail', ['error' => $e->getMessage()]);
        }
        
        try {
            $user->notify(new SPMBNotification(
                'payment_instruction',
                'Instruksi Pembayaran',
                'Berkas Anda telah diverifikasi. Silakan lakukan pembayaran untuk menyelesaikan pendaftaran.',
                route('siswa.bayar'),
                'Bayar Sekarang'
            ));
            \Log::info('PaymentInstruction SPMBNotification sent successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to send PaymentInstruction SPMBNotification', ['error' => $e->getMessage()]);
        }

        // Log notification manually
        try {
            \App\Models\NotificationLog::create([
                'user_id' => $user->id,
                'type' => 'payment',
                'channel' => 'email',
                'recipient' => $user->email,
                'message' => 'Instruksi pembayaran dikirim',
                'status' => 'sent',
                'sent_at' => now()
            ]);
        } catch (\Exception $e) {
            \Log::warning('Failed to log notification', ['error' => $e->getMessage()]);
        }
    }

    private function handlePaymentVerified(PaymentVerified $event)
    {
        $user = $event->pendaftar->user;
        
        Mail::to($user->email)->send(new PaymentConfirmationMail($event->pendaftar));
        
        $user->notify(new SPMBNotification(
            'payment_confirmed',
            'Pembayaran Terverifikasi',
            'Pembayaran Anda telah berhasil diverifikasi. Tunggu pengumuman hasil seleksi.',
            route('siswa.dashboard'),
            'Lihat Status'
        ));

        // Log notification manually
        try {
            \App\Models\NotificationLog::create([
                'user_id' => $user->id,
                'type' => 'payment_verified',
                'channel' => 'email',
                'recipient' => $user->email,
                'message' => 'Pembayaran terverifikasi',
                'status' => 'sent',
                'sent_at' => now()
            ]);
        } catch (\Exception $e) {
            \Log::warning('Failed to log notification', ['error' => $e->getMessage()]);
        }
    }

    private function handleSelectionResultAnnounced(SelectionResultAnnounced $event)
    {
        $user = $event->pendaftar->user;
        $status = $event->pendaftar->status_akhir;
        
        Mail::to($user->email)->send(new SelectionResultMail($event->pendaftar));
        
        $message = match($status) {
            'LULUS' => 'Selamat! Anda diterima di ' . $event->pendaftar->jurusan->nama,
            'TIDAK_LULUS' => 'Mohon maaf, Anda belum berhasil pada seleksi kali ini.',
            'CADANGAN' => 'Anda masuk dalam daftar cadangan. Tunggu informasi selanjutnya.',
            default => 'Hasil seleksi telah diumumkan.'
        };

        $user->notify(new SPMBNotification(
            'selection_result',
            'Pengumuman Hasil Seleksi',
            $message,
            route('siswa.dashboard'),
            'Lihat Hasil'
        ));

        // Log notification manually
        try {
            \App\Models\NotificationLog::create([
                'user_id' => $user->id,
                'type' => 'selection_result',
                'channel' => 'email',
                'recipient' => $user->email,
                'message' => 'Pengumuman hasil seleksi',
                'status' => 'sent',
                'sent_at' => now()
            ]);
        } catch (\Exception $e) {
            \Log::warning('Failed to log notification', ['error' => $e->getMessage()]);
        }
    }
}